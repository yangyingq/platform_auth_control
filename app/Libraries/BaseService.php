<?php


namespace App\Libraries;


class BaseService
{
    protected $model = null;

    public function __construct()
    {
        $this->registModel();
    }

    public function registModel()
    {
        $info = $this->getRouteInfo();
        $model = 'App\\Models\\'.ucfirst($info['terminal']).'\\'.ucfirst($info['module']).'Model';
        if(class_exists($model)) {
            $this->model = app($model);
        }
    }

    /**
     * 获取路由信息
     * @return array
     */
    public function getRouteInfo() {
        $pathInfo = explode('/', request()->path());
        return [
            'terminal' => $pathInfo[0],
            'module' => $pathInfo[1],
            'method' => $pathInfo[2]
        ];
    }
}
