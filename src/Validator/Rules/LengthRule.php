<?php

namespace Dos0\Framework\Validator\Rules;

/**
 * Class LengthRule
 * @package Dos0\Framework\Validator\Rules
 */
class LengthRule extends AbstractValidationRule
{
    /**
     * Доп параметры:
     * min
     * max
     * is
    */
    private $min;
    private $max;
    private $is;

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

            if ($key == 'min' && strlen($modelFieldValue) < (int)$param) {
                $this->min = (int)$param;
                $this->errors .= 'Length must be at most than ' . $this->min . ' ';

                return false;
            };
            if ($key == 'max' && strlen($modelFieldValue) > (int)$param) {
                $this->max = (int)$param;
                $this->errors .= 'Length must be at least than ' . $this->max . ' ';

                return false;
            };
            if ($key == 'is' && strlen($modelFieldValue) == (int)$param) {
                $this->max = (int)$param;
                $this->errors .= 'Length must be equal to ' . $this->is . ' ';

                return false;
            }

        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getError(string $modelFieldName, $modelFieldValue, array $modelRuleParams, string $modelErrors): string
    {
        if (!empty($modelErrors)) {

            return str_replace(['{min}', '{max}', '{is}'], [$this->min, $this->max, $this->is], $modelErrors);

        } else {

            return $this->errors;
        }
    }
}