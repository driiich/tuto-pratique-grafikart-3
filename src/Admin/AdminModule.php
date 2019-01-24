<?php
namespace App\Admin;

use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRenderer;
use Framework\Router;

class AdminModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';

    /**
     * AdminModule constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param AdminTwigExtension $adminTwigExtension
     * @param string $prefix
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        AdminTwigExtension $adminTwigExtension,
        string $prefix
    ) {
        $renderer->addPath('admin', __DIR__ . '/views');
        $router->get($prefix, DashboardAction::class, 'admin');
        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($adminTwigExtension);
        }
    }
}
