<?php

use Framework\Middleware\CsrfMidlleware;
use Framework\Router\RouterFactory;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Framework\Twig\CsrfExtension;
use Framework\Twig\FlashExtension;
use Framework\Twig\FormExtension;
use Framework\Twig\PagerFantaExtension;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router\RouterTwigExtension;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;

return [
    'env' =>  \DI\env('ENV', 'production'),
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => 'root',
    'database.name' => 'monsupersite',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class),
        \DI\get(PagerFantaExtension::class),
        \DI\get(TextExtension::class),
        \DI\get(TimeExtension::class),
        \DI\get(FlashExtension::class),
        \DI\get(FormExtension::class),
        \DI\get(CsrfExtension::class)
    ],
    SessionInterface::class => \DI\object(PHPSession::class),
    CsrfMidlleware::class => \DI\object()->constructor(\DI\get(SessionInterface::class)),
    \Framework\Router::class => \DI\Factory(RouterFactory::class),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    PDO::class => function (\Psr\Container\ContainerInterface $c){
        return new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
];