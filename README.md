# codeception-module-mailcare

MailCare module for Codeception 

## Installation

```
composer require "mailcare/codeception-module-mailcare"
```

## Documentation

Module for testing receiving emails using [MailCare](https://mailcare.io).

### Configuration

* url *required* - API url of your mailcare server

#### Example
```
modules:
    enabled
        - MailCare:
            url: 'https://mailix.xyz/api'
```

### Actions

#### seeEmail

Checks that the given email exists.
Waits up to $timeout seconds for the given email to be received.

```php
$I->seeEmail([
    'inbox' => 'john@example.org',
    'sender' => 'no-reply@company.com',
    'subject' => 'Welcome John!',
    'since' => 'P2M',
], 30);
```

 * `param array`    $criterias
 * 'param int`      $timeout (seconds)
 
