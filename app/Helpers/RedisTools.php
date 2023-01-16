<?php
/**
 * Redis缓存工具文件
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;

class RedisTools
{
    /**
     * 各种redis数据类型的用法
     * 1.字符串 如果redis中仅仅只有字体串，可以使用字符串类型存储
     * 2.队列，当一个key对应多个元素值时，可以使用队列，它允许元素重复，有下标
     * 3.集合，当一个key对应多个元素值时，可以使用集合，但是他不允许元素值重复（唯一性）没有下标的操作（无序性）
     * 4.有序集合，在集合的基础上为每个元素添加一个score分值的属性，分值可以被设置，并且允许依据分值排序，很适合排行榜类逻辑, 计数器.
     * 5.哈希，key value键值对模式不变，但value是一个键值对时使用哈希
     */

    public static function getPrefix()
    {
        return "business_chat:";
    }

    /**
     * 返回所有匹配key
     * @param $key
     * @return mixed
     */
    public static function keys($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::keys($key);
    }

    /**
     * 最常用的类型，如果redis中仅仅只有字符串，可以使用这类方法
     * @cache_type string(字符串)
     * @param $key [缓存名称]
     * @param $value [缓存值]
     * @return mixed
     */
    public static function set($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::set($key, $value);
    }

    /**
     * 获取存储的值
     * @cache_type string(字符串)
     * @param $key [缓存名称]
     * @return mixed
     */
    public static function get($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::get($key);
    }

    /**
     * 如果存储的字符串有时效性，可以用$time来设置时间以秒为单位
     * @cache_type string(字符串)
     * @param $key [缓存的名称]
     * @param $time [缓存的时间]
     * @param $value [缓存的值]
     * @return mixed
     */
    public static function setex($key, $time, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::setex($key, $time, $value);
    }

    /**
     * 存储数据，add操作如果操作的key已经有值，则不会覆盖已有的值
     * @cache_type string(字符串)
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function setnx($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::setnx($key, $value);
    }

    /**
     * set的变种，返回替换前的值如果之前不存在，则返回null（字符串）
     * @cache_type string(字符串)
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function getset($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::getset($key, $value);
    }

    /**
     * 查检key的值是否存在
     * @cache_type string(字符串)
     * @param $key [缓存名称]
     * @return mixed
     */
    public static function exists($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::exists($key);
    }


    /**
     * 对值进行递增每次+1
     * @cache_type string(字符串)
     * @param $key [缓存名称]
     * @return mixed
     */
    public static function incr($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::incr($key);
    }

    /**
     * 对值进行递增，每次递增的值根据$value决定
     * @cache_type string(字符串)
     * @param $key [缓存名称]
     * @param int $value [每次递增的值]
     * @return mixed
     */
    public static function incrby($key, $value = 1)
    {
        $key = self::getPrefix() . $key;
        return Redis::incrby($key, $value);
    }

    /**
     * 对值进行递减每次-1
     * @cache_type string(字符串)
     * @param $key
     * @return mixed
     */
    public static function decr($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::decr($key);
    }

    /**
     * 对值进行递减，每次递减的值根据$value决定
     * @cache_type string(字符串)
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function decrby($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::decrby($key, $value);
    }

    /**
     * 对字符串缓存进行删除
     * @cache_type string(字符串)
     * @param $key
     * @param bool $addPrefix [是否携带头部信息]
     * @return mixed
     */
    public static function del($key, $addPrefix = true)
    {
        $keyPrefix = $addPrefix ? self::getPrefix() : "";
        $key = $keyPrefix . $key;
        return Redis::del($key);
    }

    /**
     * 获取字符串长度
     * @cache_type string(字符串)
     * @param $key
     * @return mixed
     */
    public static function strlen($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::strlen($key);
    }

    /**
     * 设置缓存的有效期
     * @cache_type string(字符串)
     * @param $key [缓存名称]
     * @param $time [时间]
     * @return mixed
     */
    public static function expire($key, $time)
    {
        $key = self::getPrefix() . $key;
        return Redis::expire($key, $time);
    }

    /**
     * 查询缓存的类型
     * @param $key
     * @return mixed
     */
    public static function type($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::type($key);
    }

    /**
     * 当一个key,对应多个值时使用队列（复合结构）元素可以重复
     *  向队列的尾部插入数据返回队列的长度
     * @cache_type list(队列)
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function rpush($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::rpush($key, $value);
    }

    /**
     * 向队列的尾部插入数据返回队列的长度,它只在已存在的key插入数据（队列）
     * @cache_type list(队列)
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function rpushx($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::rpushx($key, $value);
    }

    /**
     *  当一个key,对应多个值时使用队列（复合结构）元素可以重复
     *  向队列的最前边插入数据返回队列的长度
     * @cache_type list(队列)
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function lpush($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::lpush($key, $value);
    }

    /**
     * 向队列的头部插入数据返回队列的长度,它只在已存在的key插入数据（队列）
     * @cache_type list(队列)
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function lpushx($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::lpushx($key, $value);
    }

    /**
     * 返回队列的长度
     * @cache_type list(队列)
     * @param $key
     * @return mixed
     */
    public static function llen($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::llen($key);
    }

    /**
     * 返回队列中一个区间的元素
     * @cache_type list(队列)
     * @param $key
     * @param int $start
     * @param int $end
     * @return mixed
     */
    public static function lrange($key, $start = 0, $end = -1)
    {
        $key = self::getPrefix() . $key;
        return Redis::lrange($key, $start, $end);
    }

    /**
     * 返回队列中一个指定位置的元素
     * @cache_type list(队列)
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function lindex($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::lindex($key, $value);
    }

    /**
     *  从队列的最左边弹出删除一个元素
     * @cache_type list(队列)
     * @param $key
     * @return mixed
     */
    public static function lpop($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::lpop($key);
    }

    /**
     *  从队列的最右边弹出删除一个元素
     * @cache_type list(队列)
     * @param $key
     * @return mixed
     */
    public static function rpop($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::rpop($key);
    }

    /**
     * 集合也是一种复合结构，一个key可以存储多个值，但是他与队列不同的是1.无序性（没有下标的操作）2.唯一性（元素的值不能重复）
     * 向一个集合中增加一个元素
     * @cache_type set(集合)
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function sadd($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::sadd($key, $value);
    }

    /**
     * 移除指定元素
     * @cache_type set(集合)
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function srem($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::srem($key, $value);
    }

    /**
     *  返回当前集合元素的个数
     * @cache_type set(集合)
     * @param $key
     * @return mixed
     */
    public static function scard($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::scard($key);
    }

    /**
     *  返回当前集合的所有元素
     * @cache_type set(集合)
     * @param $key
     * @return mixed
     */
    public static function smembers($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::smembers($key);
    }

    /**
     * 判断数值是否在集合中
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function sismember($key, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::sismember($key, $value);
    }

    /**
     * 有序集合，在集合的基础上为每个元素添加一个score分值的属性，分值可以被设置，并且允许依据分值排序，很适合排行榜类逻辑, 计数器.
     * 向一个集合中增加一个元素并设置分数值
     * @cache_type有序集合
     * @param $key
     * @param $score [分数值]
     * @param $value
     * @return mixed
     */
    public static function zadd($key, $score, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::zadd($key, $score, $value);
    }

    /**
     * 按位置次序返回表中指定区间的元素，按次序倒排
     * @cache_type有序集合
     * @param $key
     * @param int $start
     * @param int $end
     * @return mixed
     */
    public static function zrevrange($key, $start = 0, $end = -1)
    {
        $key = self::getPrefix() . $key;
        return Redis::zrevrange($key, $start, $end);
    }

    /**
     * 统计元素的个数
     * @cache_type有序集合
     * @param $key
     * @return mixed
     */
    public static function zcard($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::zcard($key);
    }

    /**
     * key value键值对模式不变，但value是一个键值对时使用哈希
     * 存储元素
     * @cache_type hash(哈希)
     * @param $key
     * @param $hash
     * @param $value
     * @return mixed
     */
    public static function hset($key, $hash, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::hset($key, $hash, $value);
    }

    /**
     * 存储多个元素
     * @cache_type hash(哈希)
     * @param $key
     * @param array $dictionary
     * @return mixed
     */
    public static function hmset($key, array $dictionary)
    {
        $key = self::getPrefix() . $key;
        return Redis::hmset($key, $dictionary);
    }

    /**
     * 获取一个元素
     * @cache_type hash(哈希)
     * @param $key
     * @param $hash
     * @return mixed
     */
    public static function hget($key, $hash)
    {
        $key = self::getPrefix() . $key;
        return Redis::hget($key, $hash);
    }

    /**
     * 获取全部的表元素
     * @cache_type hash(哈希)
     * @param $key
     * @return mixed
     */
    public static function hgetall($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::hgetall($key);
    }

    /**
     * 返回hash表中指定的key是否存在
     * @cache_type hash(哈希)
     * @param $key
     * @param $hash
     * @return mixed
     */
    public static function hexists($key, $hash)
    {
        $key = self::getPrefix() . $key;
        return Redis::hexists($key, $hash);
    }

    /**
     * 删除hash表中指定的key的元素
     * @cache_type hash(哈希)
     * @param $key
     * @param $hash
     * @return mixed
     */
    public static function hdel($key, $hash)
    {
        $key = self::getPrefix() . $key;
        return Redis::hdel($key, $hash);
    }

    /**
     * 返回哈希表中key的个数
     * @cache_type hash(哈希)
     * @param $key
     * @return mixed
     */
    public static function hlen($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::hlen($key);
    }

    /**
     * 返回哈希表中所有key的值
     * @cache_type hash(哈希)
     * @param $key
     * @return mixed
     */
    public static function hkeys($key)
    {
        $key = self::getPrefix() . $key;
        return Redis::hkeys($key);
    }

    /**
     * hash自增长
     * @cache_type hash(哈希)
     * @param $key
     * @return mixed
     */
    public static function hincrby($key, $hash, $num = 1)
    {
        $key = self::getPrefix() . $key;
        return Redis::hincrby($key, $hash, $num);
    }


    /**
     *  hash自增长 (支持浮点数)
     * @cache_type hash(哈希)
     * @param $key
     * @return mixed
     */
    public static function hincrbyfloat($key, $hash, $num = 1)
    {
        $key = self::getPrefix() . $key;
        return Redis::hincrbyfloat($key, $hash, $num);
    }


    /**
     * 返回一个集合的全部成员，该集合是所有给定集合的并集。
     * @cache_type set(集合)
     * @param $key1
     * @param $key2
     * @return mixed
     */
    public static function sunion($key1, $key2)
    {
        $key1 = self::getPrefix() . $key1;
        $key2 = self::getPrefix() . $key2;
        return Redis::sunion($key1, $key2);
    }

    /**
     * 获取字符串值指定偏移量上的位
     * @param $key
     * @param $offset
     * @return mixed
     */
    public static function getbit($key, $offset)
    {
        $key = self::getPrefix() . $key;
        return Redis::getbit($key, $offset);
    }

    /**
     * 设置字符串值指定偏移量上的位
     * @param $key
     * @param $offset
     * @param $value
     * @return mixed
     */
    public static function setbit($key, $offset, $value)
    {
        $key = self::getPrefix() . $key;
        return Redis::setbit($key, $offset, $value);
    }

    /**
     * HyperLogLog 添加指定元素到 HyperLogLog 中。
     * @param $key
     * @param $elements
     * @return mixed
     */
    public static function pfadd($key, $elements)
    {
        $key = self::getPrefix() . $key;
        return Redis::pfadd($key, $elements);
    }

    /**
     * HyperLogLog 将多个 HyperLogLog 合并为一个 HyperLogLog
     * @param $destinationKey
     * @param $sourceKeys
     * @return mixed
     */
    public static function pfmerge($destinationKey, $sourceKeys)
    {
        $destinationKey = self::getPrefix() . $destinationKey;
        $sourceKeys = self::getPrefix() . $sourceKeys;
        return Redis::pfmerge($destinationKey, $sourceKeys);
    }

    /**
     * HyperLogLog 返回给定 HyperLogLog 的基数估算值。
     * @param $key
     * @return mixed
     */
    public static function pfcount($key)
    {
        if (is_array($key)) {
            $keyArr = [];
            foreach ($key as $k) {
                array_push($keyArr, self::getPrefix() . $k);
            }
            $key = $keyArr;
        } else {
            $key = self::getPrefix() . $key;
        }
        return Redis::pfcount($key);
    }
}
