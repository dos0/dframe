<?php
/**
 * Default framework config
 */
return [
    'render' => [
        'systemViewPath' => __DIR__ . '/../Views',
    ],

    'services' => [
        'Foo'   => 'Dos0\\Framework\\Foo\\Foo',
        'Foo2'   => 'Dos0\\Framework\\Foo\\Foo2',

        'Request'   => 'Dos0\\Framework\\Request\\Request',
        'Router'    => 'Dos0\\Framework\\Router\\Router',
        'Render'    => 'Dos0\\Framework\\Render\\Render',
        'ResponsePrepare'    => 'Dos0\\Framework\\Response\\ResponsePrepare',
        'Database'  => 'Dos0\\Framework\\Database\\Database',
        'QueryBuilder'  => 'Dos0\\Framework\\Model\\QueryBuilder',

    ],

    'database' => [
        'driver' => 'pgsql',
        'host' => 'localhost',
        'port' => '5432',
        'name' => 'framework',
        'user' => 'do_s',
        'pass' => '',
    ],

    'foo' => [
        'paramA' => 'aaConfig',
        'paramB' => 'bbConfig',
        'paramArr' => [
            'any' => 'any bar',
        ],
    ],

    'foo2' => [
        'a' => 'It is A from config',
    ]

];