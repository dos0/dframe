<?php
/**
 * Default framework config
 */
return [
    'render' => [
        'systemViewPath' => __DIR__ . '/../Views',
    ],

    'serviceMap' => require('services.php'),
    'middlewareMap' => require('middleware.php'),
];