<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{

    /**
     * @api {post} /api/auth 登陆
     * @apiName login
     * @apiGroup User
     * @apiVersion 0.1.0
     * @apiParam {String} user_email        登陆邮箱
     * @apiParam {String} password          登陆密码
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "user": {
     *            "id": "0000",
     *            "user_name": "root",
     *            "user_phone": " 123213212222",
     *            "user_email": "root@root.com",
     *            "user_address": " ",
     *            "role_id": "0000",
     *            "dept_id": "0000",
     *            "class_id": "0000",
     *            "created_at": "2016-11-16 00:00:00",
     *            "updated_at": "2016-11-30 00:49:33",
     *            "remember_token": null,
     *            "current_state": 1,
     *            "work_state": 1,
     *            "sort": 0,
     *            "job_number": "0000",
     *            "enter_date": "1960-01-01 00:00:00",
     *            "birth_date": "1960-01-01 00:00:00",
     *            "sex": 0,
     *            "deleted_at": null,
     *            "dd_user_id": null,
     *            "token": {
     *                "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTkyLjE2OC4xMC4xNzk6OTAwMC9hcGkvYXV0aCIsImlhdCI6MTQ4MTc4NjQzMiwiZXhwIjoxNDgxNzkwMDMyLCJuYmYiOjE0ODE3ODY0MzIsImp0aSI6IjA5YmY1N2Q5ZjIzZGNmN2Y2NGMwN2JhMjMyOGU1MzBhIiwic3ViIjoiMDAwMCJ9.w2th7HCMYxTBOhEJLe-tu6lOwXbElSaB98Ez68KSgnY"
     *            },
     *            "login_time": 1481786432,
     *            "cls": {
     *                "id": "0000",
     *                "class_name": "root",
     *                "class_type": 0,
     *                "class_note": null,
     *                "created_at": "2016-11-16 00:00:00",
     *                "updated_at": "2016-11-16 00:00:00",
     *                "sort": 0
     *            },
     *            "role": {
     *                "id": "0000",
     *                "role_name": "root",
     *                "role_level": null,
     *                "role_note": null,
     *                "created_at": "2016-11-16 00:00:00",
     *                "updated_at": "2016-11-16 00:00:00",
     *                "deleted_at": null,
     *                "sort": 0
     *            },
     *            "dept": {
     *                "id": "0000",
     *                "dept_name": "东集",
     *                "dept_level": 0,
     *                "dept_manager": "0000",
     *                "dept_parent": "0000",
     *                "dept_note": "",
     *                "created_at": "2016-11-29 00:00:00",
     *                "updated_at": "2016-12-02 08:40:22",
     *                "sort": 0,
     *                "manager": {
     *                    "id": "0000",
     *                    "user_name": "root",
     *                    "user_phone": " 123213212222",
     *                    "user_email": "root@root.com",
     *                    "user_address": " ",
     *                    "role_id": "0000",
     *                    "dept_id": "0000",
     *                    "class_id": "0000",
     *                    "created_at": "2016-11-16 00:00:00",
     *                    "updated_at": "2016-11-30 00:49:33",
     *                    "remember_token": null,
     *                    "current_state": 1,
     *                    "work_state": 1,
     *                    "sort": 0,
     *                    "job_number": "0000",
     *                    "enter_date": "1960-01-01 00:00:00",
     *                    "birth_date": "1960-01-01 00:00:00",
     *                    "sex": 0,
     *                    "deleted_at": null,
     *                    "dd_user_id": null
     *                }
     *            },
     *            "groups": [
     *                {
     *                    "id": "DA51A15B-EAF7-6A67-F827-1FA639FC667C",
     *                    "group_name": "仙姑组",
     *                    "group_creator": "0000",
     *                    "created_at": "2016-11-21 01:10:59",
     *                    "updated_at": "2016-11-21 01:10:59",
     *                    "sort": 0,
     *                    "users": []
     *                },
     *                {
     *                    "id": "0B69473B-37A6-53D3-409A-094D03C99E15",
     *                    "group_name": "道友组",
     *                    "group_creator": "0000",
     *                    "created_at": "2016-11-21 01:38:12",
     *                    "updated_at": "2016-11-21 01:38:12",
     *                    "sort": 0,
     *                    "users": []
     *                },
     *                {
     *                    "id": "B9928570-E094-8069-378C-4D12ADD8443B",
     *                    "group_name": "新司机",
     *                    "group_creator": "0000",
     *                    "created_at": "2016-11-21 01:38:56",
     *                    "updated_at": "2016-11-21 01:38:56",
     *                    "sort": 0,
     *                    "users": []
     *                }
     *            ]
     *        }
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400
     *     {
     *          "message": "user_not_found",
     *          "status_code": 400
     *     }
     * @apiSampleRequest http://192.168.10.135:8010/api/auth
     */
    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_email'    => 'required|email|max:50',
            'password' => 'required|alpha_dash',
        ],[
            'user_email.email' =>':attribute 必须是email格式',
            'user_email.required' =>':attribute 不能为空',
            'password.required' => ':attribute 不能为空'
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($this->formatValidatorErrors($validator->errors()->all()));
        }

        $user = User::where('user_email',$request->get('user_email'))
                    ->where('password',$request->get('password'))
                    ->get()
                    ->first();

        if(!$user)
            return $this->errorBadRequest('用户不存在或者密码不正确!');

        try {
            if(!$token = $this->jwt->fromUser($user))
            {
                return $this->errorBadRequest('Token established error');
            }
//            if (! $token = $this->jwt->attempt($request->only('user_email','password'))) {
//                return response()->json(['user_not_found'], 404);
//            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent' => $e->getMessage()], 500);

        }

        $user->token=compact('token');
        $user->login_time=time();
        return $user;
    }

    /**
     * @api {put} /api/auth 修改密码
     * @apiName 修改密码
     * @apiGroup auth
     * @apiVersion 0.1.0
     * @apiParam {string} old_password     旧密码
     * @apiParam {string} password         新密码
     * @apiParam {string} password_confirmation         密码确认
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400
     *     {
     *          "message": "现有密码不对",
     *          "status_code": 400
     *     }
     * @apiSampleRequest http://192.168.10.179:9000/api/auth
     */
    public function resetPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|different:old_password',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($this->formatValidatorErrors($validator->errors()->all()));
        }

        $user = $this->jwt->user();

        if($user->password != $request->get('old_password'))
        {
            return $this->errorBadRequest('现有密码不对');
        }

        $user->password=$request->get('password');

        $user->save();
    }


    /**
     * 根据用户名与密码重置token
     * @param Request $request
     */
    public function refreshToken(Request $request)
    {
    }

    /**
     * 登出
     */
    public function logoff()
    {
        $this->jwt->invalidate();
    }

    /**
     * 发送确认邮件
     */
    public function sendPwdBackEmail(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_email' => 'required|exists:users|max:50',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($this->formatValidatorErrors($validator->errors()->all()));
        }

        $user_email=$request->get('user_email');
        $user=User::where('user_email',$user_email)->first();

        $validateCode=Helpers::create_guid();

        $key=$this->resolveRequestSignature($request);
        Cache::put($key.'validateCode',$validateCode,5);

        $this->dispatch(new SendRegisterEmail($user,$validateCode));
    }

    /**
     * 校验验证码
     * @param Request $request
     */
    public function checkValidateCode(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'validateCode' =>'required|max:36'
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($this->formatValidatorErrors($validator->errors()->all()));
        }

        $validateCode=$request->get('validateCode');

        $key=$this->resolveRequestSignature($request);
        $value = Cache::get($key.'validateCode');

        if(!$value)
            return $this->errorBadRequest('validateCode over time');

        if($value != $validateCode)
            return $this->errorBadRequest('invalid validateCode');
    }

    /**
     * 根据验证码找回密码
     * @param Request $request
     * @return mixed
     */
    public function getPwdBack(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_email' => 'required|exists:users|max:50',
            'password' =>'required|max:50',
            'validateCode' =>'required|max:36'
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($this->formatValidatorErrors($validator->errors()->all()));
        }


        $password=$request->get('password');
        $validateCode=$request->get('validateCode');

        $key=$this->resolveRequestSignature($request);
        $value = Cache::pull($key.'validateCode');

        if(!$value)
            return $this->errorBadRequest('validateCode over time');

        if($value != $validateCode)
            return $this->errorBadRequest('invalid validateCode');

        $user_email=$request->get('user_email');
        $user=User::where('user_email',$user_email)->first();

        $user->password=$password;
        $user->save();
    }


    /**
     * Resolve request signature.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        return sha1(
            $request->method() .
            '|' . $request->server('SERVER_NAME') .
            '|' . $request->ip()
        );
    }

    /**
     * 获取免登用户信息
     * @param Request $request
     */
    public function getUserInfo(Request $request)
    {
        $user = $this->user();

        $user->cls;
        $user->role;
//        $user->permissions=get_user_permissions($user->role_id);
        $user->dept->manager;

        foreach ($user->groups as $group) {
            $group->users;
        }

        return $user;
    }

    public function scanFaceLogin(Request $request)
    {
        if($request->hasFile('scan_image'))
        {
            $file=$request->file('scan_image');

            $size=$file->getSize();

            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名

            if(0 != strcasecmp($ext,'jpg') &&
                0 != strcasecmp($ext,'png'))
                return $this->response->errorBadRequest('截取图片格式不对');

            $realPath = $file->getRealPath();   //临时文件的绝对路径

            $files=[
                'image_file' => $realPath
            ];
            $um=new UserManager();
            $face_token = $um->searchFace($files);
            if($face_token)
            {
                $user = User::where('face_token',$face_token)->first();
                if(!$user)
                    return $this->response->errorBadRequest('找不到对应的用户');

                try {
                    if(!$token = $this->jwt->fromUser($user))
                    {
                        return $this->errorBadRequest('Token established error');
                    }

                } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return response()->json(['token_expired'], 500);

                } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return response()->json(['token_invalid'], 500);

                } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return response()->json(['token_absent' => $e->getMessage()], 500);

                }

                $user->cls;
                $user->role;
                $user->dept->manager;

                foreach ($user->groups as $group) {
                    $group->users;
                }

                $user->token=compact('token');
                $user->login_time=time();
                return $user;
            }
            else
            {
                return $this->response->errorBadRequest('找不到对应的用户');
            }
        }
        else
        {
            return $this->response->errorBadRequest('登录失败');
        }
    }
	
	public function video(Request $request){
		$data = DB::table('videos')->paginate(2);
		return $data;
	}
	
	public function image(Request $request){
		$data = DB::table("images")->paginate(5);
		return $data;
	}
	public function say(){
		$request = $this->request;
		$page_size = $request->input('page_size',$this->say_pageSize);
		$data = DB::table("says")->paginate($page_size);
		if(!$data->isEmpty($data)){
		foreach($data as &$v){
			$imgArr = explode(';',$v->img);
			$v->imgs = $imgArr;
			unset($v->img);
		}
	}
	return $data;
	}
	
}
