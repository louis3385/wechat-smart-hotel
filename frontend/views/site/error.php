<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;


?>
<style>
        html,body,.container{
            height:100%;
        }
    body{
        text-align: center;
    }
    .container{
        display: inline-block;
        padding-top:10%;
        box-sizing: border-box;
    }
    .errorInfo,.errorAction{
        display:inline-block;
        margin:0 auto;
        text-align:left;
        width:99%;
    }
    hr{
        border:none;
        border-top:thin solid black;
        -webkit-transform:scaleY(0.5);
        transform:scaleY(0.5);
        margin:1rem auto;
    }
    .errorInfo{
        font-size:1.3rem;
        line-height:2rem;
        color:#595757;
        font-weight:bold;
    }
    .errorAction{
        font-size:1rem;
        line-height:1.5rem;
    }
    @media screen and (min-device-width:360px){
        html{
            font-size:19px;
        }
    }
    @media screen and (min-device-width: 400px) {   
        html{
            font-size:21px;
        }
    }  

</style>
<div class="container">
    <div class="errorShow">
        <img src= "/images/other/M-05.png" style="height:12.6rem;width:8.5rem" />
        <img src="/images/other/y6-05.png" style="width:8rem;height:6.6rem;vertical-align:top;margin-left:-1.3rem"/>
    </div>
    <div class="errorInfo">你所访问的页面不存在哦，<br/>所以看不见呢!
        <hr/>
    </div>
    <br/>
    <div class="errorAction">
        你要查看的页面可能已被删除，<br/>
        已更改名称或暂时不可用！
        <br/>
        <a href="<?= Yii::$app->request->hostInfo?>" > >>返回首页</a>
    </div>
</div>
