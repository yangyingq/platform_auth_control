<?php

namespace App\Helpers;

use App\Domain\Order\Models\Order;
use App\Exceptions\RequestException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Tools
{
    // 加密串
    const ENCRYPT_KEY = 'rgB5N8B+DOSoZA10jmcAR6Eg3pPYj950';

    /**
     * 写入成功返回
     * @param string $message
     * @return array
     */
    public static function success($message = '写入成功', $code = 0, $data = [])
    {
        if (defined('ERROR_CODE')) $code = ERROR_CODE;
        $response = [
            'code' => $code,
            'message' => $message
        ];
        if (!empty($data)) $response = array_merge($response, ['data' => $data]);
        return $response;
    }

    /**
     * 写入成功返回
     * @param array $data
     * @param int $code
     * @return array
     */
    public static function setData($data = [], $code = 0)
    {
        if (defined('ERROR_CODE')) $code = ERROR_CODE;
        $response = [
            'message' => '',
            'code' => $code,
            'data' => $data
        ];
        return $response;
    }

    /**
     * 写入失败返回
     * @param string $message
     * @param int $code
     * @return array
     */
    public static function error($message = '写入失败', $code = 1, $data = [])
    {
        if (defined('ERROR_CODE')) $code = ERROR_CODE;
        $response = [
            'code' => $code,
            'message' => $message
        ];
        if (!empty($data)) $response = array_merge($response, ['data' => $data]);
        return $response;
    }

    /**
     * 设置日志文件名
     * @param $fileName
     * @param $bugLevel
     * @return Logger
     * @throws \Exception
     */
    public static function setFileName($fileName, $bugLevel)
    {
        if (!env('DEFINING_LOG_FILE_ON', true)) $fileName = 'haochuan_custom';
        $stream = new StreamHandler(storage_path('logs/' . Carbon::now()->format('Y/m/d') . '/' . $fileName . '.log'), $bugLevel);
        $stream->setFormatter(new LineFormatter(null, null, true, true));
        $log = new Logger($fileName);
        $log->pushHandler($stream);

        return $log;
    }

    /**
     * 单个日志输出
     * @param $content
     * @throws \Exception
     */
    public static function logInfo($content, $title = null, $fileName = 'haochuan_custom')
    {
        if (env('LOG_ON', true)) {
            $log = self::setFileName($fileName, Logger::INFO);
            if ($title) $log->info($title);
            $log->info('==========================');
            $log->info(print_r($content, true));
            $log->info('==========================');
        }
    }

    /**
     * 单个错误日志输出
     * @param string $content
     * @throws \Exception
     */
    public static function logError($content, $title = null, $fileName = 'haochuan_custom')
    {
        $log = self::setFileName($fileName, Logger::ERROR);
        if ($title) $log->info($title);
        $log->error('**************************');
        $log->error($content);
        $log->error('**************************');
    }

    /**
     * 事务异常错误日志输出
     * @param $exception
     * @param null $title
     * @param string $fileName
     * @throws \Exception
     */
    public static function logUnusualError($exception, $title = null, $fileName = 'haochuan_custom')
    {
        $log = self::setFileName($fileName, Logger::ERROR);
        if ($title) $log->info($title);
        $log->error('**************************');
        $log->error("\n"
            . "----------------------------------------\n"
            . "| 错误信息 | {$exception->getMessage()}\n"
            . "| 文件路径 | {$exception->getFile()} (第{$exception->getLine()}行)\n"
            . "| 访问路径 | [" . request()->method() . "] " . request()->url() . "\n"
            . "| 请求参数 | " . json_encode(request()->all()) . "\n"
            . "----------------------------------------\n");
        $log->error('**************************');
    }

    /**
     * 多个日志一次性输出
     * @param $content
     * @param null $title
     * @param bool $isEnd
     * @param string $fileName
     * @return bool
     * @throws \Exception
     */
    public static function singleLog($content, $title = null, $isEnd = false, $fileName = 'haochuan_custom')
    {
        if (!isset($GLOBALS['debugArray'])) {
            $GLOBALS['debugArray'] = array();
        }

        if ($title) {
            array_push($GLOBALS['debugArray'], $title);
            array_push($GLOBALS['debugArray'], '==========================');
        }

        if ($content) {
            array_push($GLOBALS['debugArray'], print_r($content, true));
            array_push($GLOBALS['debugArray'], '--------------------------');
        }

        if ($isEnd) {
            self::logInfo($GLOBALS['debugArray'], null, $fileName);
            unset($GLOBALS['debugArray']);
        }

        return true;
    }

    /**
     * 异步日志
     * @param $keyName
     * @param $content
     * @param null $title
     * @param bool $isEnd
     * @param string $fileName
     * @return bool
     * @throws \Exception
     */
    public static function asyncLog($keyName, $content, $title = null, $isEnd = false, $fileName = 'haochuan_custom')
    {
        if (!isset($GLOBALS[$keyName])) {
            $GLOBALS[$keyName] = array();
        }

        if ($title) {
            array_push($GLOBALS[$keyName], $title);
            array_push($GLOBALS[$keyName], '==========================');
        }

        if ($content) {
            array_push($GLOBALS[$keyName], print_r($content, true));
            array_push($GLOBALS[$keyName], '--------------------------');
        }

        if ($isEnd) {
            self::logInfo($GLOBALS[$keyName], null, $fileName);
            unset($GLOBALS[$keyName]);
        }

        return true;
    }

    /**
     * curl请求
     * @param string $url 访问的URL
     * @param string $post post数据(不填则为GET)
     * @param string $method 请求方式，POST/GET
     * @param string|array $header 请求头
     * @param string $cookie 提交的$cookies
     * @param int $returnCookie 是否返回$cookies
     * @return mixed|string
     */
    public static function curlRequest($url, $post = '', $method = '', $header = 'Content-Type: application/json', $cookie = '', $returnCookie = 0)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");

        if (strtolower($method) == 'post' || (empty($method) && !empty($post))) {
            curl_setopt($curl, CURLOPT_POST, 1);
        } elseif (strtolower($method) == 'get' || empty($post)) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        }

        if (is_array($post)) {
            // 数组类型
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        } else if ($post) {
            // json类型
            if (is_string($header)) {
                $httpHeader = [
                    $header,
                    'Content-Length: ' . strlen($post),
                    'User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)'
                ];
            } elseif (is_array($header)) {
                $httpHeader = $header;
            }
            isset($httpHeader) && curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }

        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if ($returnCookie) {
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie'] = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        } else {
            return $data;
        }
    }

    /**
     * 检查请求参数
     * @param $keys
     * @param bool $isOnly
     * @return array|\Illuminate\Http\Request|\Laravel\haochuan_custom\Application|mixed|string
     * @throws RequestException
     */
    public static function checkRequest($keys, $isOnly = true)
    {
        // 判断是否是数组
        if (!is_array($keys)) {
            $required[] = $keys;
        } else {
            $required = $keys;
        }
        // 检查必传参数
        $allRequest = request()->keys();
        foreach ($required as $requiredKey) {
            if (!in_array($requiredKey, $allRequest)) {
                $withoutKeys[] = $requiredKey;
            }
        }

        // 拼接错误参数
        if (!empty($withoutKeys)) {
            $message = '缺少参数';
            if (env('APP_ENV') != 'master') {
                $message .= ':' . implode(',', $withoutKeys);
            }
            throw new RequestException($message);
        }

        if ($isOnly) {
            return request($required);
        } else {
            return request()->all();
        }
    }

    /**
     * 保留两位小数
     * @param $price
     * @return string|bool
     */
    public static function formatPrice($price, $trimZero = true)
    {
        if ($trimZero) {
            return (string)floatval(substr(sprintf("%.3f", $price), 0, -1));
        }
        return substr(sprintf("%.3f", $price), 0, -1);
    }

    /**
     * 加密
     * @param $data
     * @return mixed
     */
    public static function dataEncrypt($data)
    {
        $key = self::ENCRYPT_KEY;
        ksort($data);
        return md5(http_build_query($data) . $key);
    }

    /**
     * 传参验证
     * @param array $data 需要验证的数组
     * @param array $rules 验证规则
     * @param string $messageKey 使用哪个板块的验证提示
     * @throws \Exception
     */
    public static function dataValidator($data, $rules, $messageKey)
    {
        if (is_array($data)) {
            $messages = config("validator_message." . $messageKey);
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                $errorMessage = json_decode($validator->errors(), true);
                throw new ValidationException(array_first($errorMessage)[0] ?? '验证数据失败');
            }
        }
    }

    /**
     * 模拟生成token
     * @return string
     */
    public static function setToken()
    {
        // 生成一个不会重复的字符串
        $str = md5(uniqid(md5(microtime(true)), true));
        $str = sha1($str);
        return $str;
    }

    /**
     * 过滤掉EmoJi表情
     * @param $str
     * @return mixed
     */
    public static function filterEmoJi($str)
    {
        $str = preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $str);

        return $str ?? '?';
    }

    /**
     * cos curl请求
     * @param        $url
     * @param string $method
     * @param array $header
     * @param array $body
     * @return mixed
     */
    public static function requestWithHeader($url, $method = 'POST', $header = array(), $body = array())
    {
        //array_push($header, 'Accept:application/json');
        //array_push($header, 'Content-Type:application/json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        switch ($method) {
            case "GET" :
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case "POST" :
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case "PUT" :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE" :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        if (isset($body{3}) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        if (count($header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $ret = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($ret, true);

        return $data;
    }

    /**
     * 远程下载图片
     * @param        $imageUrl
     * @param        $imageName
     * @return string
     */
    public static function curlDownPic($imageUrl, $imageName = '')
    {
        $uploadDir = '/uploads/temp/';

        // 文件保存目录
        $fileDir = public_path() . $uploadDir;

        if (!is_dir($fileDir)) @mkdir($fileDir, 755, true);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);
        $filename = $fileDir . time() . ($imageName ?: pathinfo($imageUrl, PATHINFO_BASENAME)) . '.png';
        $resource = fopen($filename, 'a');
        fwrite($resource, $file);
        fclose($resource);
        return $filename;
    }

    /**
     * 时间对象转化
     * @param $timeObj
     * @param $format
     * @return string
     */
    public static function formatTime($timeObj, $format = 'Y-m-d H:i:s')
    {
        if (!$timeObj) return '';
        return Carbon::parse($timeObj)->format($format);
    }

    /**
     * 清除缓存
     * @param $tableName
     * @param $id
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function delObjectCache($tableName, $id)
    {
        $key = "{$GLOBALS['company_id']}:$tableName:$id";
        if (Cache::delete($key)) return true;
        return false;
    }

    /**
     * 转换为时间区间
     * @param        $date
     * @param string $startTime
     * @param string $endTime
     * @return array
     */
    public static function getBetweenTime($date, $startTime = '', $endTime = '')
    {
        switch ($date) {
            case 'today':
                // 今天
                return [Carbon::today(), Carbon::tomorrow()->subSecond(1)];
            case 'yesterday':
                // 昨天
                return [Carbon::yesterday(), Carbon::today()->subSecond(1)];
            case 'week':
                // 7天
                return [Carbon::yesterday()->subDay(6), Carbon::today()->subSecond(1)];
            case 'month':
                // 30天
                return [Carbon::yesterday()->subDay(29), Carbon::today()->subSecond(1)];
            case 'season':
                // 90天
                return [Carbon::yesterday()->subDay(89), Carbon::today()->subSecond(1)];
            default:
                if (!empty($startTime) && !empty($endTime)) {
                    $startTime .= ' 00:00:00';
                    $endTime .= ' 23:59:59';
                }
                $startTime = Carbon::parse($startTime);
                $endTime = Carbon::parse($endTime);
                return [$startTime, $endTime];
        }
    }

    /**
     * 阿拉伯数字转汉字
     * @param int $number 数字
     * @param bool $isRmb 是否是金额数据
     * @return string
     */
    public static function number2chinese($number, $isRmb = false)
    {
        // 判断正确数字
        if (!preg_match('/^-?\d+(\.\d+)?$/', $number)) {
            return 'number2chinese() wrong number';
        }
        list($integer, $decimal) = explode('.', $number . '.0');

        // 检测是否为负数
        $symbol = '';
        if (substr($integer, 0, 1) == '-') {
            $symbol = '负';
            $integer = substr($integer, 1);
        }
        if (preg_match('/^-?\d+$/', $number)) {
            $decimal = null;
        }
        $integer = ltrim($integer, '0');

        // 准备参数
        $numArr = ['', '一', '二', '三', '四', '五', '六', '七', '八', '九', '.' => '点'];
        $descArr = ['', '十', '百', '千', '万', '十', '百', '千', '亿', '十', '百', '千', '万亿', '十', '百', '千', '兆', '十', '百', '千'];
        if ($isRmb) {
            $number = substr(sprintf("%.5f", $number), 0, -1);
            $numArr = ['', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖', '.' => '点'];
            $descArr = ['', '拾', '佰', '仟', '万', '拾', '佰', '仟', '亿', '拾', '佰', '仟', '万亿', '拾', '佰', '仟', '兆', '拾', '佰', '仟'];
            $rmbDescArr = ['角', '分', '厘', '毫'];
        }

        // 整数部分拼接
        $integerRes = '';
        $count = strlen($integer);
        if ($count > max(array_keys($descArr))) {
            return 'number2chinese() number too large.';
        } else if ($count == 0) {
            $integerRes = '零';
        } else {
            for ($i = 0; $i < $count; $i++) {
                $n = $integer[$i];      // 位上的数
                $j = $count - $i - 1;   // 单位数组 $descArr 的第几位
                // 零零的读法
                $isLing = $i > 1                    // 去除首位
                    && $n !== '0'                   // 本位数字不是零
                    && $integer[$i - 1] === '0';    // 上一位是零
                $cnZero = $isLing ? '零' : '';
                $cnNum = $numArr[$n];
                // 单位读法
                $isEmptyDanwei = ($n == '0' && $j % 4 != 0)     // 是零且一断位上
                    || substr($integer, $i - 3, 4) === '0000';  // 四个连续0
                $descMark = isset($cnDesc) ? $cnDesc : '';
                $cnDesc = $isEmptyDanwei ? '' : $descArr[$j];
                // 第一位是一十
                if ($i == 0 && $cnNum == '一' && $cnDesc == '十') $cnNum = '';
                // 二两的读法
                $isChangeEr = $n > 1 && $cnNum == '二'       // 去除首位
                    && !in_array($cnDesc, ['', '十', '百'])  // 不读两\两十\两百
                    && $descMark !== '十';                   // 不读十两
                if ($isChangeEr) $cnNum = '两';
                $integerRes .= $cnZero . $cnNum . $cnDesc;
            }
        }

        // 小数部分拼接
        $decimalRes = '';
        $count = strlen($decimal);
        if ($decimal === null) {
            $decimalRes = $isRmb ? '整' : '';
        } else if ($decimal === '0') {
            $decimalRes = '零';
        } else if ($count > max(array_keys($descArr))) {
            return 'number2chinese() number too large.';
        } else {
            for ($i = 0; $i < $count; $i++) {
                if ($isRmb && $i > count($rmbDescArr) - 1) break;
                $n = $decimal[$i];
                $cnZero = $n === '0' ? '零' : '';
                $cnNum = $numArr[$n];
                $cnDesc = $isRmb ? $rmbDescArr[$i] : '';
                $decimalRes .= $cnZero . $cnNum . $cnDesc;
            }
        }
        // 拼接结果
        $res = $symbol . ($isRmb ?
                $integerRes . ($decimalRes === '零' ? '元整' : "元$decimalRes") :
                $integerRes . ($decimalRes === '' ? '' : "点$decimalRes"));
        return $res;
    }

    /**
     * 距离处理
     * @param $distance
     * @return string
     */
    public static function dealDistance($distance)
    {
        if (!$distance) {
            $distance = '';
        } else if ($distance > 1000) {
            $distance = substr(sprintf("%.3f", ($distance / 1000)), 0, -1) . '千米';
        } else {
            $distance = substr(sprintf("%.3f", $distance), 0, -1) . '米';
        }
        return $distance;
    }

    /**
     * 去掉小数00
     * @param int $price
     * @return string
     */
    public static function fatPrice($price = 0)
    {
        $price = sprintf('%.2f', $price);
        return rtrim(rtrim($price, '0'), '.');
    }

    /**
     * 获取外网IP
     * @return string
     */
    public static function getClientIp()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        return $cip ?? '';
    }

    /**
     * 精度计算（最多只支持4位小数计算）
     * @param $m
     * @param $n
     * @param $x
     * @return float|int|string
     */
    public static function calc($m, $n, $x)
    {
        $numCountM = 0;
        $numCountN = 0;
        $tempM = explode('.', $m);
        if (sizeof($tempM) > 1) {
            $decimal = end($tempM);
            $numCountM = strlen($decimal);
        }

        $tempN = explode('.', $n);
        if (sizeof($tempN) > 1) {
            $decimal = end($tempN);
            $numCountN = strlen($decimal);
        }

        if (($numCountM ?? 0) > ($numCountN ?? 0)) {
            $baseNum = pow(10, $numCountM ?? 0);
        } else {
            $baseNum = pow(10, $numCountN ?? 0);
        }

        $m = intval(round($m * $baseNum));
        $n = intval(round($n * $baseNum));

        switch ($x) {
            case '+':
                $response = $m + $n;
                break;
            case '-':
                $response = $m - $n;
                break;
            case '*':
                $response = $m * $n / $baseNum;
                break;
            case '/':
                if ($n != 0) {
                    $response = $m / $n;
                } else {
                    $response = '被除数不能为零';
                }
                break;
            default:
                $response = '参数传递错误';
                break;
        }
        return $response / $baseNum;
    }

    /**
     * 文件转base64
     * @param $file
     * @return string
     */
    public static function fileToBase64($file)
    {
        $base64File = '';
        if (file_exists($file)) {
            $mimeType = mime_content_type($file) ?: 'image/png';
            $base64Data = base64_encode(file_get_contents($file));
            $base64File = 'data:' . $mimeType . ';base64,' . $base64Data;
        }
        return $base64File;
    }

    /**
     * 格式化整形
     * @param $value
     * @return int
     */
    public static function formatInt($value)
    {
        return (int)$value;
    }

    /**
     * 转换URL链接HTTP为HTTPS
     * @param $url
     * @return mixed
     */
    public static function httpToHttps($url)
    {
        if (empty($url)) return $url;
        $url = trim($url);
        $prefix = substr($url, 0, 7);
        if (strtolower($prefix) == 'http://') {
            $url = 'https://' . substr($url, 7);
        }
        return $url;
    }

    /**
     * 日转换为星期
     * @param $day
     * @return mixed
     */
    public static function dayOfWeek($day)
    {
        if (!$day) return $day;
        $weekNum = Carbon::parse($day)->dayOfWeek;
        switch ($weekNum) {
            case '0':
                $weekCn = '星期日';
                break;
            case '1':
                $weekCn = '星期一';
                break;
            case '2':
                $weekCn = '星期二';
                break;
            case '3':
                $weekCn = '星期三';
                break;
            case '4':
                $weekCn = '星期四';
                break;
            case '5':
                $weekCn = '星期五';
                break;
            case '6':
                $weekCn = '星期六';
                break;
            default:
                $weekCn = '未知';
                break;
        }
        return $weekCn;
    }

    /**
     * 获取当前访问的控制器目录、控制器名和方法名
     * @param string|null $field
     * @return mixed
     */
    public static function getControllerInfo($field = null)
    {
        $routeInfo = app('request')->route();

        if (is_null($field) && empty($routeInfo[2])) {
            $pathInfo = explode('/', request()->path());
            return [
                'terminal' => self::toCamelCase($pathInfo[0] ?? ''),
                'module' => self::toCamelCase($pathInfo[1] ?? ''),
                'controller' => self::toCamelCase($pathInfo[3] ?? ''),
                'method' => self::toCamelCase($pathInfo[4] ?? ''),
            ];
        }

        // 如果是中横杆，转换为驼峰
        $info = [
            'terminal' => self::toCamelCase($routeInfo[2]['terminal'] ?? ''),
            'module' => self::toCamelCase($routeInfo[2]['module'] ?? ''),
            'controller' => self::toCamelCase($routeInfo[2]['controller'] ?? ''),
            'method' => self::toCamelCase($routeInfo[2]['method'] ?? ''),
        ];

        if(!is_null($field)){
            if($info[$field]){
                return $info[$field];
            }
            $pathInfo = explode('/', request()->path());
            $pathInfo = [
                'terminal' => self::toCamelCase($pathInfo[0] ?? ''),
                'module' => self::toCamelCase($pathInfo[1] ?? ''),
                'controller' => self::toCamelCase($pathInfo[3] ?? ''),
                'method' => self::toCamelCase($pathInfo[4] ?? ''),
            ];
            return $pathInfo[$field] ?? null;
        }
//        if (!is_null($field)) return isset($info[$field]) ? $info[$field] : null;
        return $info;
    }

    /**
     * 下划线命名到驼峰命名
     * @param $str
     * @param $flag
     * @return mixed
     */
    public static function toCamelCase($str, $flag = '_')
    {
        $array = explode($flag, $str);
        $result = $array[0];
        $len = count($array);
        if ($len > 1) {
            for ($i = 1; $i < $len; $i++) $result .= ucfirst($array[$i]);
        }
        return $result;
    }

    /**
     * 驼峰命名转下划线命名
     * @param $str
     * @return string
     */
    public static function toUnderScore($str): string
    {
        $dstr = preg_replace_callback('/([A-Z]+)/',function($matchs)
        {
            return '_'.strtolower($matchs[0]);
        },$str);
        return trim(preg_replace('/_{2,}/','_',$dstr),'_');
    }

    /**
     * 获取完整文件路径
     * @param $fileUrl
     * @return string
     */
    public static function getFullFileUrl($fileUrl)
    {
        if (empty($fileUrl)) {
            return $fileUrl;
        }
        return rtrim(env('APP_URL'), '/') . '/' . trim($fileUrl, '/');
    }

    /**
     * 通过UID获取分表表名
     * @param $table
     * @param $uid
     * @param $num
     * @return string
     */
    public static function getHashTable($table, $uid, $num = 10)
    {
        $str = crc32($uid);
        $hash = abs($str % $num);
        if (empty($hash)) {
            $hash = 0;
        }

        return $table . $hash;
    }


    /**
     * 分表union查询
     * @param string $table 表
     * @param string $where 查询条件
     * @param integer $num 查询的表个数
     * @param string $field 查询字段
     * @return string
     */
    public static function querySqlUnion($table, $where, $num, $field = '*')
    {
        $str = '';
        if ($where) {
            $where = ' where ' . $where;
        }
        for ($i = 0; $i < $num; $i++) {
            if ($i == 0) {
                $str = "SELECT {$field} FROM " . $table . $i . " " . $where;
            } else {
                $str .= " UNION ALL SELECT {$field} FROM " . $table . $i . " " . $where;
            }
        }
        return $str;
    }

    /**
     * 判断ftp上文件是否存在
     * @param $config
     * @param $fileName
     * @param string $logPath
     * @return bool
     * @throws \Exception
     */
    public static function ftpFileExists($config, $fileName, $logPath = 'ftp_operation')
    {
        // 配置判断
        $configKeys = array_keys($config);
        $necessaryConfig = ['ip', 'port', 'user_name', 'password'];
        if (!array_diff($necessaryConfig, $configKeys)) {
            self::logInfo(json_encode($config), '连接ftp配置参数有误', $logPath);
            return false;
        }
        // 连接FTP
        $ftpConnectId = ftp_connect($config['ip'], $config['port']);
        if (!$ftpConnectId) {
            self::logInfo(json_encode($config), '连接ftp失败', $logPath);
            return false;
        }
        // 登录FTP
        if (!ftp_login($ftpConnectId, $config['user_name'], $config['password'])) {
            ftp_close($ftpConnectId);
            self::logInfo(json_encode($config), '登录ftp失败', $logPath);
            return false;
        }
        // 关闭被动传输模式
        ftp_pasv($ftpConnectId, true);
        // 验证文件是否存在
        $filePath = ($config['file_dir'] ?? '') . ltrim($fileName, '/');
        if (-1 != ftp_size($ftpConnectId, $filePath)) {
            return true;
        }
        // 关闭连接
        ftp_close($ftpConnectId);
        return false;
    }

    /**
     * 格式化枚举 ['例1', '例2'] => [{'value': 0, 'title': '例1'},{'value': 1, 'title': '例2'}]
     * @param array $array
     * @return array
     */
    public static function enumFormat($array)
    {
        $data = [];
        foreach ($array as $key => $value) {
            $arr['value'] = $key;
            $arr['title'] = $value;
            array_push($data, $arr);
        }
        return $data;
    }

    /**
     * 生成单号
     * @param string $product
     * @return string
     */
    public static function createOrderNo($product = 'HC')
    {
        // 当前年月日时分秒
        $year = substr(date('Y'), 2, 2);
        $orderNo = strtoupper($product) . $year . date('mdHis');

        // 拼上4位随机数
        $orderNo .= rand(1000, 9999);

//        return self::checkOrderNo($orderNo, $product);
        return $orderNo;
    }

    /**
     * 检验订单编码是否唯一
     * @param $orderNo
     * @param $product
     * @return string
     */
    protected static function checkOrderNo($orderNo, $product)
    {
        // 生成当前值
        $date = date('YmdH');
        $redisKey = sprintf(config('rediskeys.order.set.order_no'), $GLOBALS['product_key'], $date);

        // 缓存不存在，查询数据库，设置缓存
        if (!RedisTools::exists($redisKey)) {
            $orderNos = Order::where('created_at', '>', date('Y-m-d H:00:00'))->pluck('trans_no')->toArray();
            if (count($orderNos)) {
                RedisTools::sadd($redisKey, $orderNos);
                RedisTools::expire($redisKey, 3 * 3600);
            } else {
                RedisTools::sadd($redisKey, $orderNo);
                RedisTools::expire($redisKey, 3 * 3600);
                return $orderNo;
            }
        }

        // 判断集合中该值是否存在
        if (RedisTools::sadd($redisKey, $orderNo)) {
            return $orderNo;
        } else {
            return self::createOrderNo($product);
        }
    }

    /**
     * 获取统一媒资外部编码
     * @param $productKey
     * @param $materialId
     * @param string $type
     * @return string
     */
    public static function getBigDataMaterialId($productKey, $materialId, $type = 'album')
    {
        if (!$productKey || empty($materialId)) return '';
        $type = $type == 'album' ? 'series' : 'program';
        $materialId = "{$productKey}_{$type}_" . str_pad($materialId, 12, 0, STR_PAD_LEFT);
        return $materialId;
    }

}
