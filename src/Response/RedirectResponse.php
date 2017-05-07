<?php

namespace Dos0\Framework\Response;

/**
 * Class RedirectResponse
 * @package Dos0\Framework\Response
 */
class RedirectResponse extends Response
{
    /**
     * RedirectResponse constructor.
     *
     * @param string $redirect_uri
     * @param int $code
     */
    public function __construct(string $redirect_uri, int $code = 301)
    {
        parent::__construct('', $code);

        $this->code = $code;
        $this->addHeader('Location', $redirect_uri);
    }

    /**
     * Nothing to send
     */
    public function sendBody()
    {
    }
}