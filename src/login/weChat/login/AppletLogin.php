<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/5/23
 * Time: 下午3:24
 */

namespace tpl\login\weChat\login;


use thl\common\CurlTool;
use thl\entity\ThlResult;
use thl\enum\ThlResultEnum;
use tpl\enum\TplResultEnum;
use tpl\enum\WechatParEnum;
use tpl\login\weChat\Tool\WxBizDataCrypt;

class AppletLogin extends WechatLogin
{
    public function jscode2session($code){
        //判断code
        if(empty($code)){
            return new ThlResult(TplResultEnum::NO_CODE_ERROR_MSG,TplResultEnum::NO_CODE_ERROR_CODE);
        }

        $applet_app_id = self::$weChatConfig->getAppletAppId();
        $applet_app_secret = self::$weChatConfig->getAppletAppSecret();
        $grant_type = WechatParEnum::GRANT_TYPE;
        if (!isset(self::$tplUrlConfig['js_code_to_session_url'])){
            return new ThlResult(
                TPlResultEnum::NO_URL_IN_YML_ERROR_CODE,
                TPlResultEnum::NO_URL_IN_YML_ERROR_MSG
            );
        }
        $url = self::$tplUrlConfig['js_code_to_session_url']."appid={$applet_app_id}&secret={$applet_app_secret}&js_code={$code}&grant_type={$grant_type}";
        $res = json_decode(CurlTool::httpGet($url),true);

        return $this->returnRewrite($res);
    }


    public function decryptData($code,$encryptedData,$iv)
    {
        if(empty($code)){
            return new ThlResult(TplResultEnum::NO_CODE_ERROR_MSG,TplResultEnum::NO_CODE_ERROR_CODE);
        }

        if(empty($encryptedData)){
            return new ThlResult(
                TplResultEnum::APPLET_DATA_CRYPT_NO_DATE_MSG,
                TplResultEnum::APPLET_DATA_CRYPT_NO_DATE_CODE
            );
        }


        if(empty($iv)){
            return new ThlResult(
                TplResultEnum::APPLET_DATA_CRYPT_NO_IV_MSG,
                TplResultEnum::APPLET_DATA_CRYPT_NO_IV_CODE
            );
        }

        $applet_app_id = self::$weChatConfig->getAppletAppId();

        $res = $this->jscode2session($code);
        if ($res->getCode() != ThlResultEnum::SUCCESS_CODE){
            return $res;
        }

        $data = $res->getData();
        if (!isset($data['session_key'])){
            return new ThlResult(
                TplResultEnum::APPLET_DATA_CRYPT_NO_SESSION_KEY_MSG,
                TplResultEnum::APPLET_DATA_CRYPT_NO_SESSION_KEY_CODE
            );
        }

        $session_key = $data['session_key'];

        $wxbizDataCrypt = new WxBizDataCrypt($applet_app_id,$session_key);

        return $wxbizDataCrypt->decryptData($encryptedData,$iv);

    }
}