# CakePHP CAS Authentication

Provides CAS authentication component for CakePHP applications.

## Requirements

The master branch has the following requirements:

* CakePHP 2.2.0 or greater.
* PHP 5.4.0 or greater.

## Installation

_[Using [Composer](http://getcomposer.org/)]_

Add the plugin to your project's `composer.json`:

```javascript
{
  "require": {
    "asaladino/cakephp_cas_authentication": "1.0.*"
  }
}
```

Because this plugin has the type `cakephp-plugin` set in it's own `composer.json`, composer knows to install it inside your `/Plugin` directory, rather than in the usual vendors file. It is recommended that you add `/Plugin/CasAuthentication` to your .gitignore file. (Why? [read this](http://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).)

> Consider using "require-dev" if you only want to include DebugKit for your development environment.

_[Manual]_

* Download the [CasAuthentication archive](https://github.com/asaladino/cakephp_cas_authentication/zipball/master).
* Unzip that download.
* Rename the resulting folder to `CasAuthentication`
* Then copy this folder into `app/Plugin/`

_[Enable]_
```php
class UsersController extends AppController {
    public $components = [
        'Auth' => [
            'authenticate' => [
                'CasAuthentication.Cas' => [
                    'hostname' => 'cas.server.com',
                    'uri' => '/cas/',
                    'debug' => LOGS . 'log-file.log',
                    'disbled' => false
                ]
            ]
        ]
    ];
}
```