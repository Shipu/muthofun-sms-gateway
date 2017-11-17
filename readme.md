# MUTHOFUN SMS Gateway

muthofun-sms-gateway is a PHP client for MUTHOFUN SMS Gateway API. This package is also support Laravel and Lumen.

## Installation

Go to terminal and run this command

```shell
composer require shipu/muthofun-sms-gateway
```

Wait for few minutes. Composer will automatically install this package for your project.

### For Laravel

Below **Laravel 5.5** open `config/app` and add this line in `providers` section

```php
Shipu\MuthoFun\MuthoFunServiceProvider::class,
```

For Facade support you have add this line in `aliases` section.

```php
'MUTHOFUN'   =>  Shipu\MuthoFun\Facades\MuthoFun::class,
```

Then run this command

```shell
php artisan vendor:publish --provider="Shipu\MuthoFun\MuthoFunServiceProvider"
```

## Configuration

This package is required two configurations.

1. username = your username which provide by MUTHOFUN.
2. password = your password which provide by MUTHOFUN

muthofun-sms-gateway is take an array as config file. Lets services

```php
use Shipu\MuthoFun\MUTHOFUN;

$config = [
    'username' => 'Your Username',
    'password' => 'Your Password'
];

$sms = new MUTHOFUN($config);
```
### For Laravel

This package is also support Laravel. For laravel you have to configure it as laravel style.

Go to `app\muthofun.php` and configure it with your credentials.

```php
return [
    'username' => 'Your Username',
    'password' => 'Your Password'
];
```

## Usages
Its very easy to use. This packages has a lot of functionalities and features.

#### Send SMS to a single user

**In PHP:**
```php
use \Shipu\MuthoFun\MuthoFun;

...

$sms = new MUTHOFUN($config);
$response = $sms->message('your text here !!!', '01606022000')->send(); // Guzzle Response with request data

// For another example please see below laravel section. 
 
return $response->autoParse(); // Getting only response contents.
```
**In Laravel:**
```php
use \Shipu\MuthoFun\Facades\MuthoFun;

...

$sms = MUTHOFUN::message('your text here !!!', '01606022000')->send(); // Guzzle Response with request data

// or

$sms = MUTHOFUN::message('your text here !!!')->to('01606022000')->send();

// or

$sms = MUTHOFUN::send(
    [
        'message' => "your text here",
        'to' => '01616022000'
    ]
);
return $sms->autoParse(); // Getting only response contents.
```

#### Send same message to all users
```php
$sms = MUTHOFUN::message('your text here !!!')
            ->to('01616022669')
            ->to('01845736124')
            ->to('01745987364')
            ->send();
            
// or you can try below statements also

$sms = MUTHOFUN::message('your text here !!!', '01616022669')
            ->to('01845736124')
            ->to('01745987364')
            ->send();
            
// or           

$users = [
    '01616022669',
    '01845736124',
    '01745987364'
];        
$sms = MUTHOFUN::message('your text here !!!',$users)->send(); 
```

#### Send SMS to more user
```php

```

#### Response Data
```php
SimpleXMLElement {#212 ▼
    +"sms": SimpleXMLElement {#216 ▼
        +"smsclientid": "713231739"
        +"messageid": "500930552"
        +"mobile-no": "+8801616022669"
}
```