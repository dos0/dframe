<?php

namespace Dos0\Framework\Validator\Rules;

/**
 * Class AbstractValidationRule
 * @package Dos0\Framework\Validator\Rules
 */
abstract class AbstractValidationRule
{
    /**
     * Checks validation rule
     *
     * Format modelRuleParams: ['min'=>3, 'max'=> 20, ...]
     *
     * @param string $modelFieldName field name
     * @param $modelFieldValue current value
     * @param array $modelRuleParams rule parameters
     * @return bool
     */
    abstract function check(string $modelFieldName, $modelFieldValue, array $modelRuleParams): bool;

    /**
     * Gets text of errors
     *
     * @param string $modelFieldName
     * @param $modelFieldValue
     * @param array $modelRuleParams
     * @param string $modelErrors
     * @return string
     */
    public function getError(string $modelFieldName, $modelFieldValue, array $modelRuleParams, string $modelErrors): string
    {
        return "Field $modelFieldName validation error";
    }
}