<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{
    public $defaultRole = 'reader';
    public $sessionKey = 'auth_user';
    public $hashCost = 10;
    public $loginAttempts = 5;
    public $lockoutTime = 900; // 15 minutes
}