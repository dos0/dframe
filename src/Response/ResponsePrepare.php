<?php

namespace Dos0\Framework\Response;

use Dos0\Framework\Request\Request;
use Dos0\Framework\Response\Exception\ResponseClassIsNotFoundException;
use Dos0\Framework\Response\Exception\ResponseTypeIsNotFoundException;

/**
 * Class ResponsePrepare
 * @package Dos0\Framework\Response
 */
class ResponsePrepare
{
    /**
     * Contracts the response keys to the response classes
     *
     * @var array
     */
    static $contract = [
        'json' => 'JsonResponse',
        'html' => 'Response',
        'redirect' => 'RedirectResponse'
    ];

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string Type of response
     */
    private $type;

    /**
     * @var int Code of response
     */
    private $code = 200;

    /**
     * @var string
     */
    private $data = '';

    /**
     * ResponsePrepare constructor.
     *
     * @param Request $request
     * @param string $type
     */
    public function __construct(Request $request, string $type = 'html')
    {
        $this->setType($type);
        $this->request = $request;
    }

    /**
     * @param string $type
     */
    public function setType(string $type = 'html')
    {
        $this->type = $type;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code = 200)
    {
        $this->code = $code;
    }

    /**
     * @param string $data
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }

    /**
     * Makes the Response class from type
     *
     * @param string $type
     * @return mixed
     * @throws ResponseClassIsNotFoundException
     * @throws ResponseTypeIsNotFoundException
     */
    public function make(string $type = '')
    {

        if (empty($type)) {

            if ($this->request->isJson()) {
                $type = 'json';
            } else {
                $type = $this->type;
            }
        }

        if (!array_key_exists($type, self::$contract)) {
            throw new ResponseTypeIsNotFoundException("Response Type '{$type}' Is Not Found");
        }

        $responseClassName = __NAMESPACE__ . '\\' . self::$contract[$type];
        if (!class_exists($responseClassName)) {
            throw new ResponseClassIsNotFoundException("Response Class '" . self::$contract[$type] . "' Is Not Found");
        }

        return new $responseClassName($this->data, $this->code);
    }

}