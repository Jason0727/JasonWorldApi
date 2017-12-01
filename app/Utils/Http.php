<?php

namespace App\Utils;

use Httpful\Mime;
use Httpful\Request;

Class Http
{
    public static function get($path, $params)
    {
        $url = self::joinParams($path, $params);
        $response = Request::get($url)->send();
        if ($response->hasErrors())
        {
            var_dump($response);
        }
        if ($response->body->errcode != 0)
        {
            var_dump($response->body);
        }
        return $response->body;
    }
    
    
    public static function post($path, $params, $data)
    {
        $url = self::joinParams($path, $params);
        $response = Request::post($url)
            ->body($data)
            ->sendsJson()
            ->send();
        if ($response->hasErrors())
        {
            var_dump($response);
        }
        if ($response->body->errcode != 0)
        {
            var_dump($response->body);
        }
        return $response->body;
    }

    public static function postWithFiles($url, $files, $data)
    {
        $response = Request::post($url)
            ->body($data)
            ->attach($files)
            ->send();
        if ($response->hasErrors())
        {
            throw new \Exception($response->body->error_message);
        }

        return $response->body;
    }

    public static function postWithoutFiles($url,$data)
    {
        $response = Request::post($url)
            ->body($data)
            ->sendsType(Mime::FORM)
            ->send();
        if ($response->hasErrors())
        {
            throw new \Exception($response->body->error_message);
        }

        return $response->body;
    }
    
    
    private static function joinParams($path, $params)
    {
        $url = config('dingding.OAPI_HOST') . $path;
        if (count($params) > 0)
        {
            $url = $url . "?";
            foreach ($params as $key => $value)
            {
                $url = $url . $key . "=" . $value . "&";
            }
            $length = count($url);
            if ($url[$length - 1] == '&')
            {
                $url = substr($url, 0, $length - 1);
            }
        }
        return $url;
    }
}