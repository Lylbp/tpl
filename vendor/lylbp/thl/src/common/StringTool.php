<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * DateTime: 18-9-7下午3:28
 */

namespace thl\common;

use thl\enum\ThlParamterTypeEnum;
use thl\enum\ThlRandomTypeEnum;

class StringTool
{
    /**
     * 产生随机码(默认长度为4)[可选]
     *
     * @param $type
     * @param $length
     * @return string
     */
    public static function generateRandom($type, $length = 4)
    {
        $chars = '';
        if ($type === ThlRandomTypeEnum::RANDOM_CAPTCHA) {
            $chars = 'abcdefghijklmnpqrstuvwxyzABCDEFGHJKLMNPQEST0123456789';
        } else if ($type === ThlRandomTypeEnum::RANDOM_NUMBER) {
            $chars = '0123456789';
        }

        $randomStr = '';
        $len = strlen($chars);
        for ($i=0; $i < $length; $i++){
            $randomStr .= $chars[rand(0,$len-1)];
        }

        return $randomStr;
    }
}