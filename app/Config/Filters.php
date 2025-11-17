<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
    public $aliases = [
        'csrf'     => CSRF::class,
        'toolbar'  => DebugToolbar::class,
        'honeypot' => Honeypot::class,
        'auth'     => \App\Filters\AuthFilter::class,
        'role'     => \App\Filters\RoleFilter::class,
        'guest'    => \App\Filters\GuestFilter::class,
    ];

    public $globals = [
        'before' => [
            // 'honeypot',
            'csrf',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
        ],
    ];

    public $methods = [];

    public $filters = [
        'auth' => [
            'before' => [
                'dashboard/*',
                'posts/create',
                'posts/edit/*',
                'posts/delete/*',
                'profile/*'
            ]
        ],
        'guest' => [
            'before' => [
                'login',
                'register'
            ]
        ]
    ];
}