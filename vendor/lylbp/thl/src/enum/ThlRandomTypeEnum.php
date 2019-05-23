<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/4/29
 * Time: 下午9:09
 */

namespace thl\enum;

use MyCLabs\Enum\Enum;

/**
 * 验证码类型
 *
 * Class RandomType
 * @package thl\enum
 */
class ThlRandomTypeEnum extends Enum
{
    const RANDOM_NUMBER = 1;

    const RANDOM_CAPTCHA = 2;
}