<?php

namespace App\Utils;

use App\Models\Permission;
/**
 * Created by PhpStorm.
 * User: zeuss
 * Date: 2016/11/14
 * Time: 19:23
 */
class Helpers
{
    public static function create_guid()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid =substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }

    public static function get_files($dir)
    {
        $namespace =env('CONTROLLER_NAMESPACE','App\Http\Controllers\Api');
//        $fixedPrefix="/home/vagrant/WWW/Tako/App/Http/Controllers/Api/v1/Role/";
        $files = array();

        $dirpath = realpath($dir);
        $filenames = scandir($dir);
        foreach ($filenames as $filename)
        {
            if ($filename=='.' || $filename=='..')
            {
                continue;
            }

            $file = $dirpath . DIRECTORY_SEPARATOR . $filename;

            if (is_dir($file))
            {
                $files = array_merge($files, self::get_files($file));

            }
            else
            {
                $seperates= explode("/",substr($file,0,-4));

//                $key = array_search("Controller",$seperates);
                $class=$namespace.'\\'.array_last($seperates);

                $methods=get_class_methods($class);

                if(!$methods)
                    continue;

                $key=array_search("middleware",$methods);
                $search__=array_search("__construct",$methods);
                if($search__!==false)
                    unset($methods[$search__]);
                $methods=array_slice($methods,0,$key-1);
                $permission = Permission::all();
                $permissions=[];
                foreach($permission as $item)
                    $permissions[]=$item->res_action;

                foreach ($methods as $method)
                {
                    $action=$class."@".$method;
                    //$permission = Permission::where('res_action',$action)->first();
                    if(in_array($action,$permissions,true))
                        continue;
                    $files[] = $action;
                }
                //var_dump($files);
            }
        }
        return $files;
    }
}