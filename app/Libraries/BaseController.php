<?php


namespace App\Libraries;

use App\Exceptions\RequestException;
use App\Helpers\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends Controller
{
    //基础模型
    protected $models = null;
    //基础服务类
    protected $services = null;
    //基础规则
    protected $baseRules = [

    ];

    protected $baseMessage = [
        'required' => '字段 :attribute 不能为空!',
        'max' => '字段 :attribute 超出最大长度限制!',
        'min' => '字段 :attribute 超出最小长度限制!'
    ];

    /**
     * 自动验证
     */
    public function __construct()
    {
        $this->autoValidate();
        $this->registService();
    }

    /**
     * 获取所有请求参数
     * @return array
     */
    public function getParams() {
        $params = request()->all();
        unset($params['s']);
        return $params;
    }

    /**
     * 获取指定参数
     * @param null $key
     * @return array|\Illuminate\Foundation\Application|Request|mixed|string
     */
    public function rq($key = null) {
        return $key == null? $this->getParams(): request($key);
    }

    /**
     * 获取路由信息
     * @return array
     */
    public function getRouteInfo() {
        $pathInfo = explode('/', request()->path());
        return [
            'terminal' => $pathInfo[0]?? '',
            'module' => $pathInfo[1]?? '',
            'method' => $pathInfo[2]?? ''
        ];
    }

    /**
     * 自动验证
     */
    public function autoValidate() {
        if(!isset($this->rule) || empty($this->rule)) return;
        $routeInfo = $this->getRouteInfo();
        $rules = $this->rule[$routeInfo['method']]?? [];
        $message = $this->message[$routeInfo['method']]?? [];
        $this->validateParams($this->rq(), $rules, $message);
    }

    /**
     * 验证参数
     * @param $params
     * @param $rules
     * @param $message
     * @return false|mixed
     */
    public function validateParams($params, $rules, $message) {
        $rules = array_merge($this->baseRules, $rules);
        $message = array_merge($this->baseMessage, $message);
        $validate = Validator::make($params, $rules, $message);
        if($validate->fails()) {
            $errorMessage = json_decode($validate->errors(), true);
            throw new RequestException(array_first($errorMessage)[0], 1);
        }
        return false;
    }


    /**
     * 'terminal' => $pathInfo[0],
     * 'module' => $pathInfo[1],
     * 'method' => $pathInfo[2]
     * 自动注册服务类
     */
    public function registService() {
        $info = $this->getRouteInfo();
        $service = 'App\\Services\\'.ucfirst($info['terminal']).'\\'.ucfirst($info['module']).'Service';
        if(class_exists($service)) {
            $this->services = app($service);
        }
    }

    /**
     * @param $data
     */
    public function success($data = []) {
        return Tools::success('获取成功', 0, $data);
    }


    public function callBefore() {

    }
}
