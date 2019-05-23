<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/4/19
 * Time: 下午4:55
 */

namespace thl;

class ThlBase
{
    public function __construct()
    {
        require_once  dirname(__DIR__) . '/app/bootstrap.php';
    }
}