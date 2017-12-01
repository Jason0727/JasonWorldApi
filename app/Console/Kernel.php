<?php

namespace App\Console;

use App\Jobs\GroupCommitJob;
use Illuminate\Support\Facades\DB;
use App\Jobs\InitializeAssessmentsJob;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Utils\Helpers;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
        $schedule->call(function (){
            dispatch(new InitializeAssessmentsJob());
        })->monthlyOn(1, '00:00');

//        $schedule->call(function(){
//            dispatch(new GroupCommitJob());
//        })->monthlyOn(12, '00:00');

        //23点定时统计当天出库设备数
        $schedule->call(function (){
            $guid = Helpers::create_guid();
            $date = date('Y-m-d', time());
            $dateTime = date('Y-m-d H:i:s', time());
            $sql = "INSERT INTO term_out_count select '" . $guid . "' AS id, COUNT(terminals.id) AS term_count, '" .
                $date . "' AS report_date, '" . $dateTime . "' AS created_at, '" . $dateTime . "' AS updated_at " .
                "FROM terminals LEFT JOIN boxes ON terminals.box_id = boxes.id WHERE boxes.outed_at LIKE '" .
                $date . "%'";
            DB::select($sql);
        })->dailyAt('23:00');

        //每个月一号清理errorExcel错误文件
        $schedule->exec('rm -rf ' . base_path() . '/public/errorExcel/*')->monthlyOn(1, '01:00');
        //每个月一号清理successExcel错误文件
        $schedule->exec('rm -rf ' . base_path() . '/public/successExcel/*')->monthlyOn(1, '01:01');
    }
}
