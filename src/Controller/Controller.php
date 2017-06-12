<?php

namespace Dos0\Framework\Controller;

use Dos0\Framework\Render\Render;

/**
 * Base Controller
 *
 * Class Controller
 * @package Dos0\Framework\Controller
 */
abstract class Controller
{
    /**
     * @var Render
     */
    private $renderer;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        // $todo вынести ссылку на вьюхи в файл конфигурации
        $this->renderer = new Render(__DIR__ . '/../views');
    }

    /**
     * Renders content with pointed and Layout templates
     *
     * @param string $viewFileName
     * @param array $params
     * @return string
     */
    public function render(string $viewFileName, array $params = []): string
    {
        $output = $this->renderPartital($viewFileName, $params);

        $output = $this->renderPartital('layout.html.php', ['content' => $output]);

        return $output;
    }

    /**
     * Renders content with template
     *
     * @param string $viewFileName
     * @param array $params
     * @return string
     */
    public function renderPartital(string $viewFileName, array $params = []): string
    {
        $output = $this->renderer->render($viewFileName, $params);

        return $output;
    }
}