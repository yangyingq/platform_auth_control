<?php

namespace App\Libraries;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $connection = 'platform_auth_control';
    /**
     * 查询参数判断
     * @param $query
     * @param $params
     * @return mixed
     */
    public function scopeParamSearch($query, $params) {
        if (!empty($params)) {
            $fillable = array_merge($this->fillable, ['id', 'deleted_at', 'created_at', 'updated_at']);
            foreach ($params as $key => $value) {
                // $key示例：%%title =%title %=title  >=title  >title  <=title  !title  !=title
                // 解析出第三个字符开始的字段名字
                preg_match('/[a-z|\-|\_]+[0-9]*/', $key, $field);
                $field = $field[0] ?? '';
                if (!empty($field) && in_array($field, $fillable) && $value !== '') {
                    $prefixOne = substr($key, 0, 1); // 第一个字符可以当作判断符号
                    $prefixTwo = substr($key, 1, 1); // 第二个字符可以当作判断符号
                    if (is_null($value)) {
                        $query->whereNull($field);
                    } elseif ($prefixOne == '!' && is_null($value)) {
                        $query->whereNotNull($field);
                    } elseif (is_array($value)) {
                        $query->whereIn($field, $value);
                    } elseif ($prefixOne == '!' && is_array($value)) {
                        $query->whereNotIn($field, $value);
                    } elseif ($prefixOne . $prefixTwo == '<=' || $prefixOne . $prefixTwo == '>=') {
                        $query->where($field, $prefixOne . $prefixTwo, $value);
                    } elseif ($prefixOne == '<' || $prefixOne == '>') {
                        $query->where($field, $prefixOne, $value);
                    } elseif ($prefixOne == '!') {
                        $query->where($field, '!=', $value);
                    } elseif ($prefixOne == '%' && $prefixTwo == '%') {
                        $query->where($field, 'like', '%'.$value.'%');
                    } elseif ($prefixOne == '%' && $prefixTwo == '=') {
                        $query->where($field, 'like', '%'.$value);
                    } elseif ($prefixOne == '=' && $prefixTwo == '%') {
                        $query->where($field, 'like', $value.'%');
                    } else {
                        $query->where($field, $value);
                    }
                }
            }
        }
        return $query;
    }

    /**
     * 分页数据格式化
     * @param $query
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function scopePagination($query, int $page = 1, int $limit =10) {
        $data = [];
        $offset = ($page-1)*$limit;
        $data['total'] = $query->count();
        $data['data'] = $query->offset($offset)->limit($limit)->get($this->fillable);
        return $data;
    }
}
