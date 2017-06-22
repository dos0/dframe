<?php
namespace Dos0\Framework\Session;

/**
 * Class Session
 * @package Dos0\Framework\Session
 */
class Session
{
    /**
     * Instance of Session
     *
     * @var null
     */
    private static $instance = null;

    /**
     * Gets Session class instance
     *
     * @return Session
     */
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Private method!
     *
     * Session constructor.
     */
    private function __construct()
    {
        session_start();
    }

    /**
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * @param string $name
     * @return null
     */
    public function get(string $name)
    {
        return (!empty($_SESSION[$name])) ? $_SESSION[$name] : null;
    }

    /**
     * Unsets concrete session name
     *
     * @param string $name
     */
    public function unset(string $name)
    {
        if (array_key_exists($name, $_SESSION)) {
            $this->unset($_SESSION[$name]);
        }
    }

    /**
     * Unset all session
     */
    public function unsetAll()
    {
        session_unset();
    }


    /**
     * Must be empty
     */
    private function __clone()
    {
    }
    /**
     * Must be empty
     */
    private function __wakeup()
    {
    }
}