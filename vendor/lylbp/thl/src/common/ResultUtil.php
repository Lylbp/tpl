<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/4/29
 * Time: 下午9:21
 */

namespace thl\common;


use thl\entity\ThlResult;
use thl\enum\ResultEnum;
use thl\enum\ThlResultEnum;

class ResultUtil
{
    public static function success($data = array()){
        return new ThlResult(ThlResultEnum::SUCCESS_MSG, ThlResultEnum::SUCCESS_CODE, $data);
    }

    public static function error($data = array()){
        return new ThlResult(ThlResultEnum::ERROR_MSG, ThlResultEnum::ERROR_CODE, $data);
    }
}