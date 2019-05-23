<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/5/21
 * Time: 下午2:30
 */

namespace tpl\login\weChat\abstracts;


abstract class WeChatConfigAbstract
{
    public abstract function getWechatAppId();
    public abstract function getWechatAppSecret();
    public abstract function getWechatMerchantKey();
    public abstract function getWechatMerchantId();
    public abstract function getAppletAppId();
    public abstract function getAppletAppSecret();
}