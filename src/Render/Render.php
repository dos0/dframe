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
    /**
     * Contents path to view
     *
     * @var string
     */
    private $viewPath = '';


    public function __construct(string $viewPath)
    {
        if (!is_dir($viewPath)) {
            throw new ViewDirNotFoundException('View Dir ' . $viewPath . ' Not Found Exception');
        }
        $this->viewPath = $viewPath;
    }

    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    public function setViewPath(string $viewPath)
    {
        $this->viewPath = $viewPath;
    }

    public function render(string $viewFileName, array $params = []): string
    {
        $viewFile = $this->viewPath . '/' . $viewFileName;

        if (!file_exists($viewFile)) {
            throw new ViewFileNotFoundException('View File ' . $viewFile . ' Not Found Exception');
        }

        $output = file_get_contents($viewFile);

        foreach ($params as $key => $value) {
            $output = str_replace('{' . $key . '}', $value, $output);
        }

        return $output;
    }

}