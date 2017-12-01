<?php
namespace App\Http\Bll;
use App\Enums\LevelEnum;
use App\Enums\OperateEnum;
use App\Enums\StatusesEnum as StatusEnum;
use App\Models\Operate;
use App\Models\User;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class AssessmentManager implements Imanager
{
    public function doesUserExistInOperatorsDept($dept_id, $user_id)
    {
        $ret = false;

        if (!$dept_id || !$user_id)
            return $ret;

        $userManager = new UserManager();
        $users = $userManager->getAllDeptUsers($dept_id);

        foreach ($users as $user) {
            if ($user_id == $user->id) {
                $ret = true;
            }
        }

        return $ret;
    }

    /**
     * 提交(组长、主管、总监)
     */
    public function assessmentsCommit($cur_user, $status)
    {
        $ret = false;
        if (!$cur_user
            || !is_numeric($status)
            || $status <= StatusEnum::Commited
            || $status > StatusEnum::Confirmed
        )
            return $ret;

        $userManager = new UserManager();
        $users = $userManager->getAllDeptUsers($cur_user->dept_id);

        foreach ($users as $user) {
            $assessment = $user->assessments()->where('date', date('Ym',strtotime('-1 month')))->first();

            if (!$assessment)
                continue;

            $operate = $this->createOperate($cur_user,
                $assessment->id,
                OperateEnum::SelfSave,
                $assessment->comprehesive_level);

            if ($cur_user->id == $assessment->user_id)//取消自己状态的修改
                continue;

            if ($status <= $assessment->status)
                continue;

            if ($assessment->status <= StatusEnum::Saved)
            {
                $assessment->comprehesive_level = LevelEnum::D;
                $assessment->team_level = LevelEnum::D;
                $assessment->plat_level = LevelEnum::D;
                $assessment->proj_level = LevelEnum::D;
                $assessment->attendence_level = LevelEnum::D;
                $assessment->saturation_level = LevelEnum::D;
                $operate->reason='强制打分';
            }


            $assessment->status = $status;
            $operateEnum = 0;

            switch ($status) {
                case StatusEnum::TeamRanked:
                    $operateEnum = OperateEnum::TeamCommit;
                    break;
                case StatusEnum::ManagerRanked:
                    $operateEnum = OperateEnum::ManagerCommit;
                    break;
                case StatusEnum::Confirmed:
                    $operateEnum = OperateEnum::Confirm;
                    break;
                default:
                    break;
            }

            if (0 == $operateEnum)
                return $ret;

            $operate->operate = $operateEnum;
            $operate->rank_level = $assessment->comprehesive_level;

            $assessment->operates()->save($operate);

            $assessment->save();
        }
        $ret = true;
        return $ret;
    }


    /**
     * 创建历史记录
     */
    public function createOperate($cur_user, $assessment_id, $operate_enum, $rank_level, $reason = null)
    {
        $ret = null;

        if (!$cur_user
            || $operate_enum > OperateEnum::Rank
            || $operate_enum < OperateEnum::SelfSave
            || $rank_level > LevelEnum::A
            || $rank_level < LevelEnum::Undefined
        )
            return $ret;

        $operate_prev = Operate::where('assesment_id', $assessment_id)
            ->orderBy('created_at', 'desc')->first();

        $ret = new Operate;
        $ret->id = Helpers::create_guid();
        $ret->operator = $cur_user->id;
        $ret->operate = $operate_enum;
        $ret->prev_level = !$operate_prev ? 0 : $operate_prev->rank_level;
        $ret->rank_level = $rank_level;
        $ret->reason = $reason;
        return $ret;
    }

    /**
     * 获取部门员工的考核情况
     * @param Request $request
     * @param $date
     * @param $dept_id
     * @param null $level
     */
    public function getDeptUsersAssessments(Request $request, $date, $dept_id, $is_export, $level = null, User $user = null)
    {
        if (!$date)
            throw new InvalidParameterException('invalid date');

        if (!$dept_id)
            throw new InvalidParameterException('invalid dept_id');

        if ($level && (!is_numeric($level) || $level < LevelEnum::Undefined || $level > LevelEnum::A))
            throw new InvalidParameterException('invalid level');

        $deptManager = new DeptManager();

        if (!$is_export && $user && !$deptManager->isDeptChild($user->dept_id, $dept_id))
            throw new InvalidParameterException('user does not has the right to serach other department');

        $deptIds = array();
        $deptManager->getDeptIdsByDeptTree(
            $deptManager->getDeptsTree($dept_id),
            $deptIds
        );

        $users = User::with(['assessments' => function ($query) use ($date, $is_export, $level) {

            $query = $query->with(['operates' => function ($query) {
                $query->orderBy('created_at','asc');
            },'attendence'])
                ->where('date', $date);

            if (!$query)
                $query = $query->where('comprehesive_level', $level);

            if ($is_export)
                $query->where('status', StatusEnum::Confirmed);

        }, 'dept', 'cls', 'role'])
            ->whereIn('dept_id', $deptIds)
            ->where('id', '!=', $user ? $user->id : '0000')
            ->get();

        $filters = $users->filter(function ($item) {
            return $item->assessments && count($item->assessments) > 0;
        });

        if($filters->values()) {
            foreach ($filters->values() as $v) {
                if ($v->assessments) {
                    $v->historyRank = $this->getRecentRank($v->id);//最近六个月的打分记录
                    foreach ($v->assessments as $val) {
                        if ($val->operates) {
                            foreach ($val->operates as &$value) {
                                $userInfo = User::select(['role_id','user_name'])->where('id', $value->operator)->first();
                                if(empty($userInfo))
                                    continue;
                                $value->reasonStatus = !empty($value->reason)? 1:0;
                                $value->rank_level = $this->scoreToLevel($value->rank_level);
                                $value->prev_level = $this->scoreToLevel($value->prev_level);
                                $value->ctime = strtotime($value->created_at);
                                $value->utime = strtotime($value->updated_at);
                                $value->role_id = $userInfo->role_id;
                                $value->user_name = $userInfo->user_name;
                                $value->operate = $this->operateToAct($value->operate);
                                $value->role_name = $this->roleIdToName($userInfo->role_id);
                            }
                        }
                    }
                }
            }
        }
        return $filters->values();
    }

    /**
     * 枚举值到等级的转换
     * @param $score
     */
    public function scoreToLevel($score)
    {
        if (!is_numeric($score))
            throw new InvalidParameterException('invalid score');

        $ret = false;

        switch ($score) {
            case LevelEnum::A:
                $ret = 'A';
                break;
            case LevelEnum::B:
                $ret = 'B';
                break;
            case LevelEnum::BPlus:
                $ret = 'B+';
                break;
            case LevelEnum::C:
                $ret = 'C';
                break;
            case LevelEnum::CPlus:
                $ret = 'C+';
                break;
            case LevelEnum::D:
                $ret = 'D';
                break;
            default:
                $ret = '未打分';
                break;
        }
        return $ret;
    }


    /**
     * 枚举值操作的转换
     * @param $operate
     */
    public function operateToAct($operate)
    {
        if (!is_numeric($operate))
            throw new InvalidParameterException('invalid operate');

        $ret = false;

        switch ($operate) {
            case OperateEnum::SelfSave:
                $ret = '保存';
                break;
            case OperateEnum::SelfCommit:
                $ret = '自己提交';
                break;
            case OperateEnum::TeamCommit:
                $ret = '小组提交';
                break;
            case OperateEnum::MicroFix:
                $ret = '微调';
                break;
            case OperateEnum::ManagerCommit:
                $ret = '主管提交';
                break;
            case OperateEnum::Confirm:
                $ret = '总监出分';
                break;
            case OperateEnum::Rank:
                $ret = '打分';
                break;
            default:
                $ret = '未知';
                break;
        }
        return $ret;
    }

    /**
     * 枚举值角色的转换
     * @param $role_id
     */
    public function roleIdToName($role_id)
    {
        if (!$role_id)
            throw new InvalidParameterException('invalid role');

        $ret = false;
        switch ($role_id) {
            case 'A9AA1307-9333-0EBC-B26E-E0FEA4431846':
                $ret = '员工';
                break;
            case 'F8F77564-EEB8-14D7-E00A-8BECCE416EFE':
                $ret = '组长';
                break;
            case '759080AC-DCD6-E39F-1643-AEF1789EE854':
                $ret = '主管';
                break;
            case '53AFC865-F349-CEE8-0405-ED4609185A91':
                $ret = '总监';
                break;
            case 'CCAC77A8-A20C-80FF-9AC2-41D92787467B':
                $ret = '研发助理';
                break;
            default:
                $ret = '不详';
                break;
        }
        return $ret;
    }


    /**
     * 统计最近六个月的得分
     **/
    public function getRecentRank($user_id)
    {
        $ret = '';
        if(empty($user_id))
            return $ret;
        $recentSixMonth = array(
            date("Ym",strtotime("-7 month")),
            date("Ym",strtotime("-6 month")),
            date("Ym",strtotime("-5 month")),
            date("Ym",strtotime("-4 month")),
            date("Ym",strtotime("-3 month")),
            date("Ym",strtotime("-2 month")),
        );
        $rankInfo = DB::table('assessments')->select('date','comprehesive_level')
                    ->where('status',OperateEnum::Confirm)
                    ->whereIn('date',$recentSixMonth)
                    ->where('user_id',$user_id)
                    ->orderBy('date','asc')
                    ->get();

        if(!empty($rankInfo))
        {
            foreach ($rankInfo as &$value)
            {
                $value->rankLevel = $this->scoreToLevel($value->comprehesive_level);
            }
            return $rankInfo;
        }
        return $ret;
    }
}