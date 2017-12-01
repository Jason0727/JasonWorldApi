<?php

namespace App\Enums;

/**
 * Created by PhpStorm.
 * User: zeuss
 * Date: 2016/11/14
 * Time: 19:14
 */
class StatusesEnum
{
    const NotFilled = 0;
    const Saved = 1;
    const Commited=2;
    const TeamRanked=3;
    const ManagerRanked=4;
    const Confirmed=5;
}


class OperateEnum
{
    const SelfSave=0;
    const SelfCommit=1;
    const TeamCommit=2;
    const MicroFix=3;
    const ManagerCommit=4;
    const Confirm=5;
    const Rank=6;
}

class LevelEnum
{
    const Undefined=0;
    const D=10;
    const C=20;
    const CPlus=30;
    const B=40;
    const BPlus=50;
    const A=60;
}

class BoxTermStatusEnum
{
    const Created=0; //已创建
    const BindAuditing=1;//绑定待审核
    const Bound=2;//已绑定
    const Confirmed=3;//已确认绑定
    const OutAuditing=4;//出库待审核
    const Outed=5;//已出库
    const Repairing=6;//返工
    const Returned=7;//退货
}

class BoxTermOperateEnum
{
    const Create=0; //创建
    const StartBind=1;//发起绑定
    const BindAudit=2;//绑定审核
    const bindRefuse=2;//审核拒绝
    const UploadParams=4;//上传参数
    const BoxTermCheck=5;//参数校验
    const Out=6;//发起出库
    const OutConfirm=7;//出库确认
    const Rework=8;//返工
    const Ret=9;//退货
}

class BoxTermAuditEnum
{
    const WaitForAudit=0;//待审核
    const Passed=1;//审核通过
    const Denied=2;//审核拒绝
}

class TermParamsBindEnum
{
    const Qualcomm = '4G';//高通
    const Mtk = '3G';//Mtk
}

class PsksEnum
{
    const PsksNull = 0;//无
    const PsksIn = 1;//内置
    const PsksOut = 2;//外置
}

class WeeklyReportEnum
{
    const Saved = 0; //已保存
    const Submitted = 1; //已提交
}