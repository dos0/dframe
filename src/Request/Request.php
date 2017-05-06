<?php
namespace Dos0\Framework\Request;

/**
 * Class Request
 * @package Dos0\Framework\Request
 */
class Request
{
    /**
     * Array of all headers
     *
     * @var array
     */
    private $headers = [];

    /**
     * Array only HTTP headers
     *
     * @var array
     */
    private $httpHeaders = [];

    /**
     * Request constructor.
     */
    public function __construct()
    {

        foreach ($_SERVER as $k => $v) {
            if (preg_match('/^HTTP_/', $k)) {
                $k = substr($k, 5);
                $k = ucwords(strtolower($k));
                $k = str_replace('_', '-', $k);
                $this->headers[$k] = $v;
                $this->httpHeaders[$k] = $v;
            } else {
                $this->headers[$k] = $v;
            }
        }
    }

    /**
     * Gets all headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get only HTTP headers
     *
     * @return array
     */
    public function getHttpHeaders(): array
    {
        return $this->httpHeaders;
    }

    /**
     * Returns Request Method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Gets URI
     *
     * @return string
     */
    public function getUri(): string
    {
        return explode('?', $_SERVER['REQUEST_URI'])[0];
    }

    /**
     * Returns the header value
     *
     * @param string $name
     * @return string|null
     */
    public function getHeader(string $name)
    {
        return
            isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function __get(string $name)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
    }

    /**
     * @param string $name
     * @return string
     */
    public function __isset(string $name): string
    {
        return isset($_REQUEST[$name]);
    }

    /**
     * Checks the query is json
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }


}