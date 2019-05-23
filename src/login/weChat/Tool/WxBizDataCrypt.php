<?php
namespace tpl\login\weChat\Tool;
use thl\common\ResultUtil;
use thl\entity\ThlResult;
use tpl\enum\TplResultEnum;

/**
 * Created by PhpStorm.
 * User: Alex
 * DateTime: 19-4-24下午5:57
 */
class WxBizDataCrypt
{
    private $appid;
    private $sessionKey;

    /**
     * 构造函数
     * @param $sessionKey string 用户在小程序登录后获取的会话密钥
     * @param $appid string 小程序的appid
     */
    public function __construct( $appid, $sessionKey)
    {
        $this->sessionKey = $sessionKey;
        $this->appid = $appid;
    }



    public function decryptData( $encryptedData, $iv )
    {
        if (strlen($this->sessionKey) != 24) {
            return new ThlResult(
                TplResultEnum::APPLET_DATA_CRYPT_ILLEGAL_AES_KEY_MSG,
                TplResultEnum::APPLET_DATA_CRYPT_ILLEGAL_AES_KEY_MSG
            );
        }

        $aesKey=base64_decode($this->sessionKey);
        if (strlen($iv) != 24) {
            return new ThlResult(
                TplResultEnum::APPLET_DATA_CRYPT_ILLEGAL_IV_MSG,
                TplResultEnum::APPLET_DATA_CRYPT_ILLEGAL_IV_CODE
            );
        }
        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj=json_decode( $result );
        if( $dataObj  == NULL )
        {
            return new ThlResult(
                TplResultEnum::APPLET_DATA_CRYPT_ILLEGAL_BUFFER_MSG,
                TplResultEnum::APPLET_DATA_CRYPT_ILLEGAL_BUFFER_CODE
            );
        }
        if( $dataObj->watermark->appid != $this->appid )
        {
            return new ThlResult(
                TplResultEnum::APPLET_DATA_CRYPT_ILLEGAL_BUFFER_MSG,
                TplResultEnum::APPLET_DATA_CRYPT_ILLEGAL_BUFFER_CODE
            );
        }

        return ResultUtil::success(array('decrypt_str'=>$result));
    }

}