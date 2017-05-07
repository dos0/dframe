<?php

namespace Dos0\Framework\Response;

class Response
{
    /**
     * Default code status
     *
     * @var int
     */
    protected $code = 200;

    /**
     * Default headers
     *
     * @var array
     */
    protected $headers = [
        'Content-Type' => 'text/html',
    ];

    /**
     * @var string
     */
    protected $data = '';

    /**
     * Status code messages
     */
    const STATUS_MSGS = [
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '207' => 'Multi-Status',
        '208' => 'Already Reported',
        '226' => 'IM Used',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '306' => 'Switch Proxy',
        '307' => 'Temporary Redirect',
        '308' => 'Permanent Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Payload Too Large',
        '414' => 'URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '418' => 'I\'m a teapot',
        '421' => 'Misdirected Request',
        '422' => 'Unprocessable Entity',
        '423' => 'Locked',
        '424' => 'Failed Dependency',
        '426' => 'Upgrade Required',
        '428' => 'Precondition Required',
        '429' => 'Too Many Requests',
        '431' => 'Request Header Fields Too Large',
        '451' => 'Unavailable For Legal Reasons',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Time-out',
        '505' => 'HTTP Version Not Supported',
        '506' => 'Variant Also Negotiates',
        '507' => 'Insufficient Storage',
        '508' => 'Loop Detected',
        '510' => 'Not Extended',
        '511' => 'Network Authentication Required',
    ];

    /**
     * Response constructor.
     *
     * @param string $data
     * @param int $code
     */
    public function __construct(string $data = '', int $code = 200)
    {
        $this->data = $data;
        $this->code = $code;
    }

    /**
     * @param string $key
     * @param string|int $value
     */
    public function addHeader(string $key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function removeHeader(string $key)
    {
        if (array_key_exists($key, $this->headers)) {
            unset($this->headers[$key]);
        }
    }

    /**
     * Sets response content
     *
     * @param string $data
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code)
    {
        $this->code = $code;
    }

    /**
     * Send Response
     *
     * @return string
     */
    public function send(): string
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * Send Headers
     */
    public function sendHeaders()
    {
        header($_SERVER['SERVER_PROTOCOL'] . " " . $this->code . " " . self::STATUS_MSGS[$this->code]);
        if (!empty($this->headers)) {
            foreach ($this->headers as $key => $value) {
                header($key . ": " . $value);
            }
        }
    }

    /**
     * Send some Content
     */
    public function sendContent()
    {
        echo "Hi!";
    }

}