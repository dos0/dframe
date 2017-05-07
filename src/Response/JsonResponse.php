<?php

namespace Dos0\Framework\Response;

/**
 * Class JsonResponse
 * @package Dos0\Framework\Response
 */
class JsonResponse extends Response
{
    /**
     * JsonResponse constructor.
     *
     * @param string $content
     * @param int $code
     */
    public function __construct($content, $code = 200)
    {
        parent::__construct($content, $code);

        $this->addHeader('Content-Type','application/json');
    }

    /**
     * Sends json encode content
     */
    public function sendContent(){

        echo json_encode($this->data);
    }
}