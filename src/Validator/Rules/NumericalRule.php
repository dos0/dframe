<?php

namespace Dos0\Framework\Validator\Rules;

/**
 * Class NumericalRule
 * @package Dos0\Framework\Validator\Rules
 */
class NumericalRule extends AbstractValidationRule
{
    /**
     * Доп параметры:
     * min
     * max
     * integerOnly
    */
    private $integerOnly;
    private $min;
    private $max;

    private $errors = '';

    /**
     * @inheritdoc
     */
    function check(string $modelFieldName, $modelFieldValue, array $modelRuleParams): bool
    {
        if (empty($modelFieldValue)) {
            return true;
        }

        if (!is_numeric($modelFieldValue)) {
            $this->errors .= 'The value must be an numeric. ';

            return false;
        }

        foreach ($modelRuleParams as $key => $param) {
            if ($key == 'integerOnly' && !is_integer($param)) {
                $this->integerOnly = true;
                $this->errors .= 'The value must be an integer. ';

                return false;
            }
            if ($key == 'min' && $modelFieldValue < (int)$param) {
                $this->min = (int)$param;
                $this->errors .= 'Value must be at most than ' . $this->min . ' ';

                return false;
            };
            if ($key == 'max' && $modelFieldValue > (int)$param) {
                $this->max = (int)$param;
                $this->errors .= 'The value must be at least than ' . $this->max . ' ';

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

            return str_replace(['{min}', '{max}'], [$this->min, $this->max], $modelErrors);

        } else {

            return $this->errors;
        }
    }
}