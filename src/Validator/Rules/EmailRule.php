<?php

namespace Dos0\Framework\Validator\Rules;

/**
 * Class EmailRule
 * @package Dos0\Framework\Validator\Rules
 */
class EmailRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    function check(string $modelFieldName, $modelFieldValue, array $modelRuleParams): bool
    {
        if (empty($modelFieldValue)) {
            return true;
        }
        return preg_match('/^(\S+)@([a-z0-9-]+)(\.)([a-z]{2,4})(\.?)([a-z]{0,4})+$/i', $modelFieldValue);
    }

    /**
     * @inheritdoc
     */
    public function getError(string $modelFieldName, $modelFieldValue, array $modelRuleParams, string $modelErrors): string
    {
        return (empty($modelErrors))
            ? "Field $modelFieldName is incorrect e-mail format"
            : $modelErrors;
    }
}