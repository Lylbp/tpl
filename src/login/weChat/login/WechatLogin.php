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
use thl\common\YmlTool;
use thl\entity\ThlResult;
use thl\enum\ThlResultEnum;
use thl\ThlBase;
use tpl\enum\TplResultEnum;
use tpl\enum\WechatParEnum;
use tpl\Exception\TplException;
use tpl\login\weChat\abstracts\WeChatConfigAbstract;
use tpl\login\weChat\interfaces\WeChatTplInterface;

class WechatLogin extends ThlBase implements WeChatTplInterface
{

    /**
     * @var WeChatConfigAbstract null
     */
    protected static $weChatConfig = null;

    /**
     * @var  null
     */
    protected static $tplUrlConfig = null;

    /**
     * WechatLogin constructor.
     * @param $weChatConfig
     * @throws TplException
     * @throws \thl\exception\ThlResultException
     */
    function __construct($weChatConfig)
    {
        parent::__construct();
        if (empty(self::$weChatConfig)){
            self::$weChatConfig = $weChatConfig;
        }

        self::$tplUrlConfig = YmlTool::getParameters("wechat","tpl/config/tplUrlConfig.yml");
        if (empty(self::$tplUrlConfig)){
            throw new TplException(
                ThlResultEnum::PARAM_PARSE_ERROR_CODE,
                ThlResultEnum::PARAM_PARSE_ERROR_MSG
            );
        }
    }

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
        if (isset($access_token_data['access_token']) || isset($access_token_data['openid'])){
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



    function returnRewrite($result)
    {
        if(!array_key_exists('errcode',$result)){
            return ResultUtil::success($result);
        }

        $errCode = $result['errcode'];
        switch ($errCode){
            case 0:
                return ResultUtil::success($result);
            case -1:
                $message = '系统繁忙，此时请开发者稍候再试';
                break;

            case 40001:
                $message = '获取 access_token 时 AppSecret 错误，或者 access_token 无效。请开发者认真比对 AppSecret 的正确性，或查看是否正在为恰当的公众号调用接口';
                break;

            case 40002:
                $message = '不合法的凭证类型';
                break;

            case 40003:
                $message = '不合法的 OpenID ，请开发者确认 OpenID （该用户）是否已关注公众号，或是否是其他公众号的 OpenID';
                break;

            case 40013:
                $message = '不合法的 AppID ，请开发者检查 AppID 的正确性，避免异常字符，注意大小写';
                break;

            case 40014:
                $message = '不合法的 access_token ，请开发者认真比对 access_token 的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口';
                break;

            case 41008:
                $message = '缺少oauth code';
                break;

            case 41009:
                $message = '缺少 openid';
                break;

            case 42001:
                $message = 'access_token 超时，请检查 access_token 的有效期，请参考基础支持 - 获取 access_token 中，对 access_token 的详细机制说明';
                break;

            case 42002:
                $message = 'refresh_token 超时';
                break;

            case 50005:
                $message = '用户未关注公众号';
                break;

            case 61451:
                $message = '参数错误 (invalid parameter)';
                break;

            case 9001006:
                $message = '获取 OpenID 失败';
                break;
            default:
                $message = '分享错误';
                break;
        }

        return new ThlResult(
            "message:{$message},WXerrcode:{$result['errcode']},WXErrmsg:{$result['errmsg']}",
            ThlResultEnum::ERROR_CODE
        );
    }
}