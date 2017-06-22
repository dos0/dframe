<?php

namespace Dos0\Framework\Middleware\Rules;

use Dos0\Framework\Request\Request;

/**
 * Interface MiddlewareInterface
 * @package Dos0\Framework\Middleware\Rules
 */
interface MiddlewareInterface
{
    /**
     * Middleware handle method
     *
     * @param Request $request
     */
    public function handle(Request $request);
}