<?php

namespace Dos0\Framework\Middleware\Rules;

use Dos0\Framework\Request\Request;

/**
 * Class LogMiddleware
 * @package Dos0\Framework\Middleware\Rules
 */
class LogMiddleware implements MiddlewareInterface
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request)
    {
        \Dos0\Framework\debug('middleware>:' . __METHOD__);
    }
}