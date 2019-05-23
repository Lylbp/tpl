<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/4/29
 * Time: 下午4:21
 */

namespace thl\exception;


use Throwable;

class ThlException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}