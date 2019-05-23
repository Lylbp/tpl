<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2019/5/22
 * Time: 下午12:14
 */
namespace thl\interfaces;

interface ThlInterface
{
    /**
     * 重写返回值
     *
     * @param $result
     * @return mixed
     */
    function returnRewrite($result);
}