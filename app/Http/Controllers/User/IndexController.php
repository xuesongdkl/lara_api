<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\UserModel;

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
            $token = Redis::hGet($key,'token');

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

    //登录
    public function dologin(Request $request){
        $u_name=$request->input('u_name');
        $u_pwd=$request->input('u_pwd');
        $data=[
            'u_name' =>$u_name,
            'u_pwd'  =>$u_pwd
        ];
        $url='http://xpassport.52xiuge.com/user/login';
        $curl = curl_init();                                        //初始化
        curl_setopt($curl, CURLOPT_URL,$url);                       //设置抓取的url
        curl_setopt($curl, CURLOPT_POST, 1);                        //设置post方式提交
        curl_setopt($curl, CURLOPT_POSTFIELDS,['data'=>$data]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);              //设置获取的信息以文件流的形式返回，而不是直接输出
        curl_setopt($curl, CURLOPT_HEADER, 0);                      //设置头文件的信息作为数据流输出
        $rs = curl_exec($curl);
        var_dump($rs);
    }

    /**
     * 防刷
     */
    public function order(){
        echo __METHOD__;
//        $uri=$_SERVER['REQUEST_URI'];
////        echo $uri;echo "<br>";
//
//        $uri_hash=substr(md5($uri),0,10);
////        echo $uri_hash;echo "<br>";
//
//        $ip=$_SERVER['SERVER_ADDR'];     //客户ip
//        echo $ip;
//
//        $redis_key= 'str:' .$uri_hash . ':' .$ip;
//        echo $redis_key;echo "<br>";
//
//        $num=Redis::incr($redis_key);      //+1（字符串）
//        Redis::expire($redis_key,60);      //过期时间60秒
//
//        echo 'count: ' .$num;echo "<br>";
//
//        //非法请求
//        if($num>5){
//            //拒绝服务10分钟
//            $response=[
//                'errno'   =>   40003,
//                'msg'     =>    'Invalid Ruquest!!!'
//            ];
//            Redis::expire($redis_key,600);     //过期时间10分钟
//
//            //记录非法ip
//            $redis_invalid_ip= 's:invalid:ip';
//            Redis::sAdd($redis_invalid_ip,$ip);       //集合的添加
//        }else{
//            $response=[
//                'errno' => 0,
//                'msg'   => 'ok',
//                'data'  =>[
//                    'dkl'  =>  'xs'
//                ]
//            ];
//        }
//        return $response;
    }
}