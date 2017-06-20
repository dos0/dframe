<?php

namespace Dos0\Framework\Render;

use Dos0\Framework\Render\Exeption\ViewDirNotFoundException;
use Dos0\Framework\Render\Exeption\ViewFileNotFoundException;

/**
 * Class Render
 * @package Dos0\Framework\Render
 */
class Render
{

    // @todo перенести функции renderPartial из Controller

    private $systemViewPath = '';
    private $userViewPath = '';

    public function __construct(array $viewPaths = [])
    {
        if (
            !array_key_exists('systemViewPath', $viewPaths)
            || !is_dir($viewPaths['systemViewPath'])
        ) {
            throw new ViewDirNotFoundException('Framework View Dir ' . $viewPaths['systemViewPath'] . ' Not Found Exception');
        }

        if (
            array_key_exists('userViewPath', $viewPaths)
            && !is_dir($viewPaths['userViewPath'])
        ) {
            throw new ViewDirNotFoundException('Application View Dir ' . $viewPaths['userViewPath'] . ' Not Found Exception');
        }

        $this->systemViewPath = $viewPaths['systemViewPath'];
        $this->userViewPath = empty($viewPaths['userViewPath']) ? '' : $viewPaths['userViewPath'];
    }

    public function getUserViewPath(): string
    {
        return $this->userViewPath;
    }

    public function setUserViewPath(string $userViewPath)
    {
        $this->userViewPath = $userViewPath;
    }

    public function getSystemViewPath(): string
    {
        return $this->systemViewPath;
    }

    public function render(string $viewFileName, array $params = []): string
    {
        $userViewFile = $this->userViewPath . '/' . $viewFileName;
        $systemViewFile = $this->systemViewPath . '/' . $viewFileName;

        if (file_exists($userViewFile)) {
            $output = file_get_contents($userViewFile);

        } elseif (file_exists($systemViewFile)) {
            $output = file_get_contents($systemViewFile);

        } else {
            throw new ViewFileNotFoundException('View File ' . $userViewFile . ' Not Found Exception');
        }

        foreach ($params as $key => $value) {
            $output = str_replace('{' . $key . '}', $value, $output);
        }

        return $output;
    }

}