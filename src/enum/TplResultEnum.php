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


    const APPLET_DATA_CRYPT_ILLEGAL_IV_CODE = "305";
    const APPLET_DATA_CRYPT_ILLEGAL_IV_MSG = "encodingAesKey 非法";


    const APPLET_DATA_CRYPT_ILLEGAL_AES_KEY_CODE = "306";
    const APPLET_DATA_CRYPT_ILLEGAL_AES_KEY_MSG = "解密失败";


    const APPLET_DATA_CRYPT_ILLEGAL_BUFFER_CODE = "307";
    const APPLET_DATA_CRYPT_ILLEGAL_BUFFER_MSG = "aes 解密失败";

    const APPLET_DATA_CRYPT_DECODE_BASE64_CODE = "308";
    const APPLET_DATA_CRYPT_DECODE_BASE64_MSG = "解密后得到的buffer非法";


    const APPLET_DATA_CRYPT_NO_IV_CODE = "309";
    const APPLET_DATA_CRYPT_NO_IV_MSG = "解密时iv不能为空";

    const APPLET_DATA_CRYPT_NO_DATE_CODE = "310";
    const APPLET_DATA_CRYPT_NO_DATE_MSG = "要解密的数据不能为空";

    const APPLET_DATA_CRYPT_NO_SESSION_KEY_CODE = "311";
    const APPLET_DATA_CRYPT_NO_SESSION_KEY_MSG = "session_key不存在";

    public function __construct($value)
    {
        parent::__construct($value);
    }

}