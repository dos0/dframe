<?php

namespace Dos0\Framework\Validator;

use Dos0\Framework\Validator\Exception\ValidatorFieldNameNotExistException;
use Dos0\Framework\Validator\Exception\ValidatorFieldRuleNotExistException;
use Dos0\Framework\Validator\Exception\ValidatorRuleClassNotExistException;
use Dos0\Framework\Validator\Exception\ValidatorRuleNotExistException;
use Dos0\Framework\Validator\Rules\AbstractValidationRule;

/**
 * Class Validator
 * @package Dos0\Framework\Validator
 */
class Validator
{
    /**
     * @var
     */
    private $model;

    /**
     * It is Model
     *
     * @var array
     */
    private $errors = [];

    /**
     * Rules from model
     *
     * @var array
     */
    private $modelRules = [];

    /**
     * Array of errors from validation process
     *
     * @var array
     */
    private $modelErrors = [];

    /**
     * Array of Rule contractors
     *
     * @var array
     */
    private $ruleContractors = [
        'required' => 'Dos0\\Framework\\Validator\\Rules\\RequiredRule',
        'length' => 'Dos0\\Framework\\Validator\\Rules\\LengthRule',
        'numerical' => 'Dos0\\Framework\\Validator\\Rules\\NumericalRule',
        'email' => 'Dos0\\Framework\\Validator\\Rules\\EmailRule',
        'regexp' => 'Dos0\\Framework\\Validator\\Rules\\RegexpRule',
    ];

    /**
     * Validator constructor.
     *
     * Example:
     * [
     * 'list of fields', // If one parameter - use string, else - use array of parameters
     * 'validator and parameters', // If one parameter - use string, else - use array of parameters
     * 'message'=>'Error message'
     * ]
     *
     * $validator = new Validator($request, [
     * [ 'username, password', 'required', 'The field must be filled!'],
     * [ 'username', ['length', 'min'=>3, 'max'=> 20], 'The field must be between {min} and {max} characters in length'],
     * ]);
     *
     */
    public function __construct($model, array $modelRules)
    {
        $this->model = $model;
        $this->modelRules = $modelRules;
    }

    /**
     * Validates specified object by rules
     *
     * @var $validationClass AbstractValidationRule
     * @return bool
     * @throws ValidatorFieldNameNotExistException
     * @throws ValidatorFieldRuleNotExistException
     * @throws ValidatorRuleClassNotExistException
     * @throws ValidatorRuleNotExistException
     */
    public function validate(): bool
    {
        $result = true;

        // пройти каждое указанное правило в рулейсах
        foreach ($this->modelRules as $modelRuleArray) {
            // Fields
            $modelFields = array_shift($modelRuleArray);
            if (empty($modelFields)) {
                throw new ValidatorFieldNameNotExistException('Validator Field Name Not Exist Exception');
            }
            // Rule and params
            $modelRule = array_shift($modelRuleArray);
            if (is_array($modelRule)) {
                $modelRuleKey = array_shift($modelRule);
                $modelRuleParams = $modelRule;
            } else {
                $modelRuleKey = $modelRule;
                $modelRuleParams = [];
            }
            if (empty($modelRuleKey)) {
                throw new ValidatorFieldRuleNotExistException('Validator Field Name Not Exist Exception');
            }
            // Model errors
            $this->modelErrors = !empty($modelRuleArray[0]) ? $modelRuleArray[0] : '';

            if (!array_key_exists($modelRuleKey, $this->ruleContractors)) {
                throw new ValidatorRuleNotExistException('Validator Rule ' . $modelRuleKey . ' Not Exist Exception');
            }

            if (!class_exists($this->ruleContractors[$modelRuleKey])) {
                throw new ValidatorRuleClassNotExistException(
                    'Validator Rule Class ' . $this->ruleContractors[$modelRuleKey] . ' Not Exist Exception');
            }

            // Get concrete validator
            $validationClass = new $this->ruleContractors[$modelRuleKey];

            // Validate fields
            if (is_array($modelFields)) {
                $modelFields = implode(', ', $modelFields);
            }
            $modelFields = explode(',', str_replace(' ', '', $modelFields));

            foreach ($modelFields as $modelFieldName) {

                $modelFieldValue = isset($this->model->$modelFieldName) ? $this->model->$modelFieldName : null;

                if (!$validationClass->check($modelFieldName, $modelFieldValue, $modelRuleParams)) {

                    $result = false;

                    $this->errors[$modelFieldName][] = $validationClass->getError(
                        $modelFieldName, $modelFieldValue, $modelRuleParams, $this->modelErrors
                    );
                }
            }

        }

        return $result;
    }

    /**
     * Gets all errors array
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


    /**
     * Adds new validation rules
     *
     * @param string $key
     * @param string $classNamespace
     * @return bool
     */
    public function addValidationRule(string $key, string $classNamespace): bool
    {
        if (class_exists($classNamespace)) {
            $this->ruleContractors[$key] = $classNamespace;
            return true;
        }
        return false;
    }

}