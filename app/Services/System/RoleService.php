<?php

namespace App\Services\System;
use App\Libraries\BaseService;

class RoleService extends BaseService
{
    /**
     *
     * @param $params
     * @return mixed
     */
    public function list($params) {
        $datas = $this->model->paramSearch($params)->pagination($params['page'], $params['limit']);
        return $datas;
    }
}
