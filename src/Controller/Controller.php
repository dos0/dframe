<?php

namespace Dos0\Framework\Controller;

use Dos0\Framework\Application;
use Dos0\Framework\Render\Render;

/**
 * Base Controller
 *
 * Class Controller
 * @package Dos0\Framework\Controller
 */
abstract class Controller
{

    // @todo перенести функции renderPartial в Render

    /**
     * @var Render
     */
    private $renderer;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->renderer = new Render(Application::getConfig()['systemViewPath']);
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
        $output = $this->renderPartial($viewFileName, $params);

        $output = $this->renderPartial('layout.html.php', ['content' => $output]);

        return $output;
    }

    /**
     * Renders content with template
     *
     * @param string $viewFileName
     * @param array $params
     * @return string
     */
    public function renderPartial(string $viewFileName, array $params = []): string
    {
        $output = $this->renderer->render($viewFileName, $params);

        return $output;
    }
}