<?php

namespace App\Http\Controllers\Admin;
use App\Libraries\BaseController;
use App\Services\System\SiteService;

class SiteController extends BaseController
{
    /**
     * 后台管理登陆
     * @return mixed
     */
    public function login()
    {
        $params = $this->rq();
        $userService = new SiteService();
        $userService->login($params);
        return $this->success();
    }
    /**
     * 獲取用戶信息
     * @return array
     */
    public function getAdminInfo() {
        $data = [
            'roles' => 'admin',
            'name' => 'yang',
            'avatar' => 'https://gimg2.baidu.com/image_search/src=http%3A%2F%2Fc-ssl.duitang.com%2Fuploads%2Fblog%2F202106%2F13%2F20210613235426_7a793.thumb.1000_0.jpeg&refer=http%3A%2F%2Fc-ssl.duitang.com&app=2002&size=f9999,10000&q=a80&n=0&g=0n&fmt=auto?sec=1677808505&t=e3ec032a545588f60ed1acc6e53dd2ec',
            'introduction' => 'hello'
        ];
        return $this->success($data);
    }

    /**
     * @return array
     */
    public function logout() {
        return $this->success();
    }
}
