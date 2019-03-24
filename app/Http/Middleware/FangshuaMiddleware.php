<?php
/**
 * Created by PhpStorm.
 * User: 雪松
 * Date: 2019/3/15
 * Time: 11:21
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class FangshuaMiddleware
{
    public function handle($request, Closure $next){

        $uri=$_SERVER['REQUEST_URI'];
//        echo $uri;echo "<br>";

        $uri_hash=substr(md5($uri),0,10);
//        echo $uri_hash;echo "<br>";

        $ip=$_SERVER['SERVER_ADDR'];     //客户ip
       // echo $ip;

        $redis_key= 'str:' .$uri_hash . ':' .$ip;
       // echo $redis_key;echo "<br>";

        $num=Redis::incr($redis_key);      //+1（字符串）
        Redis::expire($redis_key,60);      //过期时间60秒


        //非法请求
        if($num>5){
            //拒绝服务10分钟
            $response=[
                'errno'   =>   40003,
                'msg'     =>    'Invalid Ruquest!!!'
            ];
            Redis::expire($redis_key,600);     //过期时间10分钟

            //记录非法ip
            $redis_invalid_ip= 's:invalid:ip';
            Redis::sAdd($redis_invalid_ip,$ip);       //集合的添加
            return json_encode($response);
        }
        return $next($request);
    }
}