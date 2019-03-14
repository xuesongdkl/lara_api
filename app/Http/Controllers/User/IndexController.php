<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{

    public $redis_h_u_key ='api:h:u';
    public function login(Request $request)
    {
        $uid=$request->input('uid');
        //生成token
        $token=substr(md5(time()+$uid+rand(1000,9999)),10,20);
        if(1){
            $key=$this->redis_h_u_key.$uid;
            Redis::hSet($key,'token',$token);      //存token
            Redis::expire($key,3600*24*7);         //设置过期时间

            $response=[
                'error'=>0,
                'token'=>$token
            ];
        }
        return $response;
    }

    //用户个人中心
    public function uCenter(Request $request)
    {
        $uid = $request->input('uid');
        if(!empty($_SERVER['HTTP_TOKEN'])){
            //验证token有效 是否过期 是否伪造
            $http_token = $_SERVER['HTTP_TOKEN'];
//            print_r($_SERVER);die;
            $key = $this->redis_h_u_key.$uid;
            $token = Redis::hget($key,'token');

            if($token==$http_token){
                $response=[
                    'error'=>0,
                    'msg'=>'ok'
                ];
            }else{
                $response=[
                    'error'=>50001,
                    'msg'=>'invalid token'
                ];
            }
        }else{
            $response=[
                'error'=>50000,
                'msg'=>'not find token'
            ];
        }
        return $response;
    }

}