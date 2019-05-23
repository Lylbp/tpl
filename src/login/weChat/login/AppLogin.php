<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/4/29
 * Time: 下午3:49
 */

namespace tpl\login\weChat\login;

use thl\common\CurlTool;
use thl\common\ResultUtil;
use thl\entity\ThlResult;
use tpl\enum\TplResultEnum;
use tpl\enum\WechatParEnum;

class AppLogin extends WechatLogin
{

    /**
     * @param $redirect_url
     * @param $scope
     * @param $state
     * @return string|ThlResult
     */
    public function getAuthorizationRoute($redirect_url, $scope, $state)
    {
        //授权后重定向的回调链接地址
        $redirect_url = urlencode($redirect_url);

        //设置第三方授权接口参数
        $appid = self::$weChatConfig->getWechatAppId();
        $response_type = 'code';

        //拼接url
        if (!isset(self::$tplUrlConfig['get_code_url'])){
            return new ThlResult(
                TPlResultEnum::NO_URL_IN_YML_ERROR_CODE,
                TPlResultEnum::NO_URL_IN_YML_ERROR_MSG
            );
        }


        return self::$tplUrlConfig['get_code_url']."?appid=$appid&redirect_uri=$redirect_url&response_type=$response_type&scope=$scope&state=$state#wechat_redirect";
    }

    /**
     * @param $redirect_url
     * @param $scope
     * @param $state
     * @return ThlResult
     */
    public function getCodeAndState($redirect_url, $scope, $state)
    {
        if(!isset($_GET['state']) || !isset($_GET['code'])){
            $url = $this->getAuthorizationRoute($redirect_url, $scope, $state);
            Header("Location:{$url}");exit;
        }

        return ResultUtil::success(array('state'=>$_GET['state'],"code"=>$_GET['code']));
    }

    /**
     * @param $code
     * @return ThlResult
     */
    public function getAccessTokenByCode($code)
    {
        //获取配置参数
        $app_id = self::$weChatConfig->getWechatAppId();;
        $secret = self::$weChatConfig->getWechatAppSecret();
        $grant_type = WechatParEnum::GRANT_TYPE;

        //判断code
        if(empty($code)){
            return new ThlResult(TplResultEnum::NO_CODE_ERROR_MSG,TplResultEnum::NO_CODE_ERROR_CODE);
        }

        //请求微信
        if (!isset(self::$tplUrlConfig['get_access_token_url'])){
            return new ThlResult(
                TPlResultEnum::NO_URL_IN_YML_ERROR_CODE,
                TPlResultEnum::NO_URL_IN_YML_ERROR_MSG
            );
        }
        $url = self::$tplUrlConfig['get_access_token_url']."?appid=$app_id&secret=$secret&code=$code&grant_type=$grant_type";
        $access_token_res = json_decode(CurlTool::httpGet($url), true);

        return $this->returnRewrite($access_token_res);
    }

    /**
     * @param $redirect_url
     * @param string $state
     * @return ThlResult
     */
    public function getUserInfo($redirect_url, $state = '')
    {
        $res = $this->getCodeAndState($redirect_url,$scope = WechatParEnum::SNSAPI_USERINFO,$state);
        $data = $res->getData();
        if (!isset($data['code'])){
            return new ThlResult(TplResultEnum::NO_CODE_ERROR_MSG,TplResultEnum::NO_CODE_ERROR_MSG);
        }

        return $this->getUserInfoByCode($data['code']);
    }

    /**
     * @param $code
     * @return ThlResult
     */
    public function getUserInfoByCode($code)
    {
        if (empty($code)){
            return new ThlResult(TplResultEnum::NO_CODE_ERROR_MSG,TplResultEnum::NO_CODE_ERROR_CODE);
        }

        $access_token_res = $this->getAccessTokenByCode($code);
        $access_token_data = $access_token_res->getData();
        if (!isset($access_token_data['access_token']) || !isset($access_token_data['openid'])){
            return new ThlResult(
                TPlResultEnum::NO_ACCESS_TOKEN_OPENID_ERROR_CODE,
                TPlResultEnum::NO_ACCESS_TOKEN_OPENID_ERROR_MSG
            );
        }

        $access_token = $access_token_data['access_token'];
        $open_id = $access_token_data['openid'];

        if (!isset(self::$tplUrlConfig['get_user_info_url'])){
            return new ThlResult(
                TPlResultEnum::NO_URL_IN_YML_ERROR_CODE,
                TPlResultEnum::NO_URL_IN_YML_ERROR_MSG
            );
        }

        $url = self::$tplUrlConfig['get_user_info_url']."?access_token=$access_token&openid=$open_id";

        //请求微信
        $user_info = json_decode(CurlTool::httpGet($url), true);

        //数据判断
        if(!is_array($user_info) || empty($user_info)){

        }

        return ResultUtil::success($user_info);
    }

    /**
     * @param $redirect_url
     * @param string $state
     * @return ThlResult
     */
    public function getOpenId($redirect_url, $state = '')
    {
        $res = $this->getCodeAndState($redirect_url,WechatParEnum::SNSAPI_BASE,$state);
        $data = $res->getData();
        if (!isset($data['code'])){
            return new ThlResult(TplResultEnum::NO_CODE_ERROR_MSG,TplResultEnum::NO_CODE_ERROR_MSG);
        }

        return $this->getOpenIdByCode($data['code']);
    }

    /**
     * @param $code
     * @return ThlResult
     */
    public function getOpenIdByCode($code)
    {
        if (empty($code)){
            return new ThlResult(TplResultEnum::NO_CODE_ERROR_MSG,TplResultEnum::NO_CODE_ERROR_CODE);
        }

        return $this->getAccessTokenByCode($code);
    }
}