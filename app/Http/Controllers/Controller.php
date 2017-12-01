<?php

namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Log;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    use Helpers;
	protected $jwt;
	protected $request;
	//protected $url;
	protected $say_pageSize = 5;
	protected $video_pageSize = 2;
	protected $post_pageSize = 2;

    public function __construct(Request $request,JWTAuth $jwt)
    {
        $this->jwt = $jwt;
		$this->request = $request;
		//$this->url = '['.$request->method().']'.$request->fullUrl();
        //Log::info($this->url.' 请求json: '.$request->isJson()?json_encode($request->json()->all()):''.'是否缓存: '.$request->isNoCache());
    }

    public function formatReturnValue($key  = null,$data = null,$msg = null)
    {
        $prefix='errorcode';
        if(!$key)
            $key = '00001';
        $ret = [
            'code' => $key,
            'msg' => $msg ? $msg : config($prefix.'.'.$key),
            'data' => $data
        ];

        //Log::info($this->url.' 返回json：' .\GuzzleHttp\json_encode($ret));
        return $this->response()->array($ret)->withHeader('Cache-Control','no-cache');
    }
	
	/**
     * 导出excel(csv)
     * @data 导出数据
     * @headlist 第一行,列名
     * @fileName 输出Excel文件名
     */
    function csv_export($data = array(), $headlist = array(), $fileName) {
        $fileName = iconv('UTF-8','GBK',$fileName);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
        header('Cache-Control: max-age=0');

        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');

        //输出Excel列名信息
        foreach ($headlist as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $headlist[$key] =  iconv('UTF-8','GBK',$value);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $headlist);

        //计数器
        $num = 0;

        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;

        //逐行取出数据，不浪费内存
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {

            $num++;

            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) {
                ob_flush();
                flush();
                $num = 0;
            }

            $row = $data[$i];
            foreach ($row as $key => $value) {
                $row[$key] = iconv('UTF-8','GBK',$value."\t");
            }

            fputcsv($fp, $row);
        }
    }

}
