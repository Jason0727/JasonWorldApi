<?php
/**
 * Created by PhpStorm.
 * User: zeuss
 * Date: 2016/11/16
 * Time: 14:52
 */

if ( ! function_exists('config_path'))
{
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

// 获取当前登录用户
if (! function_exists('auth_user')) {
    /**
     * Get the auth_user.
     *
     * @return mixed
     */
    function auth_user()
    {
        return app('Dingo\Api\Auth\Auth')->user();
    }
}

// 获取用户权限树
if (! function_exists('get_user_permissions')) {
    /**
     * 获取用户权限树
     *
     * @return mixed
     */
    function get_user_permissions($role_id)
    {
        $sql="";

        $params=array();

        if("0000" == $role_id)
            $sql=<<<sql
            select * from permissions
sql;
        else
        {
            $sql=<<<sql
        select p.* from role_permission rp
        left join permissions p
        on rp.resource_id=p.id
        where rp.role_id=?
        order by p.sort asc
sql;
            array_push($params,$role_id);
        }


        $permissions=DB::select(
            $sql,$params);

        for ($i=0;$i<count($permissions);$i++)
        {

            $permissions[$i]->children=array();

            for ($j=0;$j<count($permissions);$j++)
            {
                if($i == $j)
                    continue;

                if($permissions[$j]->res_parent == $permissions[$i]->id)
                    array_push($permissions[$i]->children,$permissions[$j]);

            }
        }

        foreach ($permissions as $permission)
        {
            if("0000" == $permission->id)
                return $permission;
        }

    }

}

if ( ! function_exists('config_path')) {
    /* Get the configuration path. @param string $path @return string */
    function config_path($path = '') {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

/**
 * 根据数组生成树状结构层级树
 *
 * @param  array $array     待处理数组
 * @param  string $id       子节点字段名
 * @param  array $pid       父节点字段名
 * @param  array $rootid    根节点的id
 * @param  array $lev       lev，一般默认
 * @return mixed
 */
if ( ! function_exists('getTree')) {
    function getTree($array,$id='id',$pid='pid',$rootid='0',$lev=1) {
        $tree = array();
        foreach($array as $v) {
            if($v[$pid] === $rootid) {
                $v['lev'] = $lev;
                $v['child'] = getTree($array,$id,$pid,$v[$id],$lev+1);
                $tree[] = $v;
            }
        }
        return $tree;
    }
}

if(!function_exists('getWeekStartEnd')){
    function getWeekStartEnd($year,$weekNo){
        $year_start = $year."-01-01";
        $year_end = $year."-12-31";
        $start_day = strtotime($year_start);
        $end_day = strtotime($year_end);

        if(intval(date('N',$start_day)) != 1){
            $start_day = strtotime("next monday",$start_day);
        }
        $year_monday = date('Y-m-d',$start_day);

        if(intval(date('N',$end_day)) != 7){
            $end_day = strtotime("last sunday",$end_day);
        }
        $year_sunday = date('Y-m-d',$end_day);

        $num = intval(date('W',$end_day));

        $week_array=[];

        for ($i = 1; $i<=$num;$i++){
            $j = $i - 1;
            $week_start_date = date('Y-m-d',strtotime("$year_monday +$j week"));
            $week_end_date = date('Y-m-d',strtotime("$week_start_date +6 day"));
            $week_array[$i] = [$week_start_date,$week_end_date];
        }

        if(count($week_array) < $weekNo)
            return [];

        return  $week_array[$weekNo];
    }
}