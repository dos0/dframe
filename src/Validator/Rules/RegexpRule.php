<?php

namespace Dos0\Framework\Validator\Rules;

/**
 * Class RegexpRule
 * @package Dos0\Framework\Validator\Rules
 */
class RegexpRule extends AbstractValidationRule
{
    /**
     * Доп параметры:
     * regexp, используется полный формат
    */
    private $regexp;

    private $errors = '';

    /**
     * @inheritdoc
     */
    function check(string $modelFieldName, $modelFieldValue, array $modelRuleParams): bool
    {
        if (empty($modelFieldValue)) {
            return true;
        }

        foreach ($modelRuleParams as $key => $param) {

            if ($key == 'regexp' && !preg_match($param, $modelFieldValue)) {

                $this->regexp = $param;
                $this->errors .= 'The value of a corresponds to the pattern: "' . $this->regexp . '"';

                return false;
            };
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getError(string $modelFieldName, $modelFieldValue, array $modelRuleParams, string $modelErrors): string
    {
        if (!empty($modelErrors)) {

            return str_replace('{regexp}', $this->regexp, $modelErrors);

        } else {

            return $this->errors;
        }
    }
}