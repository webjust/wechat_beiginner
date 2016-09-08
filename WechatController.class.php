<?php

namespace Home\Controller;

use Think\Controller;

class WechatController  extends Controller
{
    //模拟get/post请求
    protected function curlHttp($url, $data=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //无论是post还是get都不能直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //判断是不是post
        if( $data ){
            //告诉小弟需要带上的数据
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    //生成自定义菜单
    //通过浏览器访问这个方法，来生成菜单
    public function makeMenu()
    {  
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getAccessToken();
        $data = '{
             "button":[
             {  
                  "type":"click",
                  "name":"今日歌曲",
                  "key":"V1001_TODAY_MUSIC"
              },
              {
                   "name":"菜单",
                   "sub_button":[
                   {    
                       "type":"view",
                       "name":"搜索",
                       "url":"http://www.soso.com/"
                    },
                    {
                       "type":"view",
                       "name":"博客",
                       "url":"http://www.xuanhaoguo.com"
                    },
                    {
                       "type":"click",
                       "name":"赞一下我们",
                       "key":"V1001_GOOD"
                    }]
               }]
         }';

        $res = $this->curlHttp($url, $data);
        dump($res);
    }


    //获取access_token
    public function getAccessToken()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx502172d981e6267b&secret=2aec676d197d2a21757693101705d1f3';

        $res = $this->curlHttp($url);

        $accessToken = json_decode($res, true);
        
        return $accessToken['access_token'];
    }
}