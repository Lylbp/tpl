<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/4/28
 * Time: 下午4:51
 */

namespace tpl\enum;


use MyCLabs\Enum\Enum;

class WechatParEnum extends Enum
{
    const SNSAPI_BASE = "snsapi_base";
    const SNSAPI_USERINFO = "snsapi_userinfo";
    const GRANT_TYPE = "authorization_code";

    public function __construct($value)
    {
        parent::__construct($value);
    }

}