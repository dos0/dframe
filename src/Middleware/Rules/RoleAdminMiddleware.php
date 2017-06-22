<?php

namespace Dos0\Framework\Middleware\Rules;

use Dos0\Framework\Request\Request;

/**
 * Class RoleAdminMiddleware
 * @package Dos0\Framework\Middleware\Rules
 */
class RoleAdminMiddleware implements MiddlewareInterface
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request)
    {
        \Dos0\Framework\debug('middleware>:' . __METHOD__);
    }
}