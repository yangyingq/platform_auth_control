<?php


namespace App\Http\Controllers\Admin\System;
use App\Libraries\BaseController;

class RoleController extends BaseController
{
    public function list() {
        $params = $this->listBefore($this->getParams());
        $datas = $this->services->list($params);
        return $this->success($datas);
    }

    public function listBefore($params) {
        if (!empty($params['role_name'])) {
            $params['%%role_name'] = $params['role_name'];
        }
        unset($params['role_name']);
        return $params;
    }
}
