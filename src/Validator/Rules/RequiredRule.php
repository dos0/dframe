<?php

namespace Dos0\Framework\Validator\Rules;

/**
 * Class RequiredRule
 * @package Dos0\Framework\Validator\Rules
 */
class RequiredRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    function check(string $modelFieldName, $modelFieldValue, array $modelRuleParams): bool
    {
        return (isset($modelFieldValue) && $modelFieldValue !== '');
    }

    /**
     * @inheritdoc
     */
    public function getError(string $modelFieldName, $modelFieldValue, array $modelRuleParams, string $modelErrors): string
    {
        return (empty($modelErrors))
            ? "Field $modelFieldName is required"
            : $modelErrors;
    }
}