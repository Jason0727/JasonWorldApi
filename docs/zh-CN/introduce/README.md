---
name: 说明
---
## apidoc使用注释生成文档
### 示例
  *  [http://192.168.10.135:8010/apidoc/](http://192.168.10.135:8010/apidoc/)
  *   [官方网站](http://apidocjs.com/)

### 注释代码示例
````markdown
 /**
     * @api {get} /ota 固件升级
     * @apiName ota
     * @apiGroup api
     * @apiVersion 1.0.0
     * @apiParam {String} TermType             终端类型
     * @apiParam {String} TermHardType         终端硬件版本号
     * @apiParam {String} TermVer              终端版本号
     * @apiParam {String} TermIMEI             终端IMEI
     * @apiParam {String} TermBTMac            蓝牙Mac地址
     * @apiParam {String} TermWiFiMac          WIFI Mac地址
     * @apiParam {String} TermSoftType         软件版本号
     * @apiParam {String} TermID               终端ID
     * @apiParam {String} TerminalNum          终端号
     * @apiParam {String} CustomNum            商户号
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *       {
     *          "result": true,
     *          "msg": "",
     *          "data": {
     *              "TermType": "DJ-W790",
     *              "TermVersion": "1.6.4",
     *              "DownUrl":"eyJpdiI6Ilg4YTBKV1VjRjFkbmErYzU5eWJMWnc9PSIsInZhbHVlIjoiaE5tXC84UmQzTHRmMDFzQmljc0xYQXc9PSIsIm1hYyI6IjBiN2I5ZWI4YjdlNGUwNzFhMjY5YjA2ZTQzMjE1OTEzM2E2NGE0M2IwYjMxMzI1ODc0ZTMyMzkxYjUyYmY2ZWEifQ==",
     *              "MD5": "22BB150F1D0CFB6F4C50C6E10F279AE9",
     *              "Size": "1522986",
     *              "TermHardType": "709AW_ZH_F2_V1.0.1NJS",
     *              "isForced": "00",
     *              "isAutoed": "00"
     *          }
     *      }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 200
     *     {
     *          "result": false,
     *          "msg": "",
     *          "data": ''
     *     }
     * @apiSampleRequest http://192.168.10.135:8010/api/ota
     */
````
### 安装和使用
#### 安装
````markdown
npm install apidoc -g
````
#### 生成
````markdown
apidoc -i app/Http/Controllers/Api/V1 -o public/apidoc
````
 注：在项目根目录进行操作，查找app/Http/Controllers/Api/V1下的控制器，有备注的话依次生成文档在public/apidoc目录
