<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/4/28
 * Time: 下午4:51
 */

namespace tpl\enum;


use thl\enum\ThlResultEnum;

class TplResultEnum extends ThlResultEnum
{
    //微信登录相关
    const NO_STATE_ERROR_CODE = "301";
    const NO_STATE_ERROR_MSG = "未获取到state";

    const NO_CODE_ERROR_CODE = "302";
    const NO_CODE_ERROR_MSG = "未获取到code";


    const NO_URL_IN_YML_ERROR_CODE = "303";
    const NO_URL_IN_YML_ERROR_MSG = "未在tplUrl获取到对应配置文件";

    const NO_ACCESS_TOKEN_OPENID_ERROR_CODE = "304";
    const NO_ACCESS_TOKEN_OPENID_ERROR_MSG = "未在获取到access_token或openid";

    public function __construct($value)
    {
        parent::__construct($value);
    }

}