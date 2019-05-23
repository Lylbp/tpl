<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/4/28
 * Time: 下午4:51
 */

namespace thl\enum;


use MyCLabs\Enum\Enum;

/**
 * 参数类型
 *
 * Class ParamterTypeEnum
 * @package thl\enum
 */
class ThlParamterTypeEnum extends Enum
{
    //三方配置
    const THIRD_CONFIG_PARAM_TYPE = 1;

    //三方接口路径配置
    const THIRD_API_URL_PARAM_TYPE = 2;

}