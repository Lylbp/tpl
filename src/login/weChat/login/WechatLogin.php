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
     * AppLogin constructor.
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


        self::$tplUrlConfig = YmlTool::getParameters("wechat",dirname(dirname(dirname(dirname(__dir__))))."/config/tplUrlConfig.yml");
        if (empty(self::$tplUrlConfig)){
            throw new TplException(
                ThlResultEnum::PARAM_PARSE_ERROR_CODE,
                ThlResultEnum::PARAM_PARSE_ERROR_MSG
            );
        }
    }


    function returnRewrite($result)
    {
        if (empty($result) || !is_array($result)){
            return ResultUtil::error();
        }

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
                $message = '三方交互错误';
                break;
        }

        return new ThlResult(
            "message:{$message},WXerrcode:{$result['errcode']},WXErrmsg:{$result['errmsg']}",
            ThlResultEnum::ERROR_CODE
        );
    }
}