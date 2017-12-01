<?php

namespace App\Jobs;

use App\Models\User;

class SendRegisterEmail extends Job
{
    protected $user;
    protected $validateCode;

    public function __construct(User $user,$validateCode)
    {
        $this->user = $user;
        $this->validateCode=$validateCode;
    }

    public function handle()
    {
        $user = $this->user;
        $validateCode=$this->validateCode;

        $text = strtr('hello :name, 找回密码的验证码： :validatecode', [
            ':name' => $user->user_name,
            ':validatecode' => $validateCode
        ]);
        app('mailer')->raw($text, function ($message) use ($user) {
            $message->to($user->user_email);
        });
    }
}
