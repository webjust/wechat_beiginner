<?php

namespace Home\Controller;

use Think\Controller;

define("TOKEN", "lamp16");

class IndexController extends Controller 
{

    //用户的openid
    protected $fromUsername;


    //公众号的id
    protected $toUsername;

    //这个方法负责接入
    public function index()
    {

       
        $echoStr = $_GET["echostr"];

        //接收成功后，微信服务器就不给我们服务器发送$_GET['echostr']
        //
        if( $this->checkSignature() && isset($echoStr) ){

            //接收成功后，就不应该让程序执行到这里。
            echo $echoStr;
            exit;
        }else{

            //当接入成功后，微信服务器不送echostr,也就是只能走else,
            //我们通过responseMsg()方法接收到用户给我们发送消息( ? )
            $this->responseMsg();
        }

    }

    //接收到微信给我们发送的消息( 用户的？ )
    public function responseMsg()
    {
        // $postStr还是一个XML格式的数据
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];//这样接收是接收到最原始的POST数据，也就说没有经过编码的。


        $str = json_encode($postStr);

        $data['xml'] = $str;

        M('tmp')->add($data);

        //simplexml_load_string将XML格式数据转换成对象
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

        //获取到用户openid
        $this->fromUsername = $postObj->FromUserName;

        //获取到公众号的id
        $this->toUsername = $postObj->ToUserName;

        //获取到用户发送的内容
        $keyword = trim($postObj->Content);
        $time = time();

        //当用户发送的内容是b  我们就返回一个  草
        if( $keyword =='b'  ){

            $textTpl = '<xml>
<ToUserName><![CDATA['.$this->fromUsername.']]></ToUserName>
<FromUserName><![CDATA['.$this->toUsername .']]></FromUserName>
<CreateTime>'.time().'</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[草]]></Content>
</xml>';

            echo $textTpl;
            exit;

        }elseif( $keyword =='图文'  ){

            $newTpl = '<xml>
<ToUserName><![CDATA['.$this->fromUsername.']]></ToUserName>
<FromUserName><![CDATA['.$this->toUsername.']]></FromUserName>
<CreateTime>'.time().'</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>1</ArticleCount>
<Articles>
<item>
<Title><![CDATA[订阅职位后精准推送]]></Title> 
<Description><![CDATA[上百万个高薪职位]]></Description>
<PicUrl><![CDATA[http://static.6789.com/media/images/logo.png]]></PicUrl>
<Url><![CDATA[http://www.github.com]]></Url>
</item>
</Articles>
</xml>';

            echo $newTpl;exit;


        }elseif( $keyword =='多图文'  ){

            //从数据库中获取到了一个数组
            $data = array(

                array(
                    'Title'=>'111&&&&&&&&&&&11',
                    'Description'=>'github很好要去#######上它!',
                    'PicUrl'=>'http://static.6789.com/media/images/logo.png',
                    'Url'=>'http://www.github.com'
                ),
                array(
                    'Title'=>'11111',
                    'Description'=>'github很好要去上它!',
                    'PicUrl'=>'http://static.6789.com/media/images/logo.png',
                    'Url'=>'http://www.github.com'
                )
            );

            $this->responseNews($data);
        }


        //用户发送一个"图文" 回复一条图文消息
       
  
    }

    //回复图文消息的方法
    //$data 数组
    protected function responseNews($data)
    {

        $newTpl = '<xml>
<ToUserName><![CDATA['.$this->fromUsername.']]></ToUserName>
<FromUserName><![CDATA['.$this->toUsername.']]></FromUserName>
<CreateTime>'.time().'</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>'.count($data).'</ArticleCount>
<Articles>';

        foreach ($data as $key => $value) {
            
            $newTpl .='<item>
    <Title><![CDATA['.$value['Title'].']]></Title>
    <Description><![CDATA['.$value['Description'].']]></Description>
    <PicUrl><![CDATA['.$value['PicUrl'].']]></PicUrl>
    <Url><![CDATA['.$value['Url'].']]></Url>
    </item>';

        }

        $newTpl .='</Articles></xml>';

        echo $newTpl;exit;

    }




    //验证签名是否有效
    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
                
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }


}