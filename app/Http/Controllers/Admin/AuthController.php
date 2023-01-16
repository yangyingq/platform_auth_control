<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Services\Admin\UserService;

class AuthController extends BaseController
{
    /**
     * 后台管理登陆
     * @return mixed
     */
    public function login()
    {
        $params = $this->rq();
        $userService = new UserService();
        $userService->login($params);
        return $this->success();
    }
}
