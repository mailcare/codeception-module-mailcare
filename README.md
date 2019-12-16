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

### Timeout

Waits up to $timeout seconds for an email to be received (30 seconds by default).

### Criterias

 * `inbox`      Filter by inbox (test@example.com).
 * `sender`     Filter by sender (test@example.com).
 * `subject`    Filter by subject (Welcome).
 * `since`      Filter by createdAt (2018-01-19T12:23:27+00:00).
 * `search`     Search by inbox or sender or subject (matching).
 * `unread`     Filter only by unread (true).
 * `favorite`   Filter only by favorite (true).

All criterias can be found in the [API Documentation of MailCare](https://mailcare.docs.apiary.io) except for page and limit.

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
 * `param int`      $timeoutInSecond (optional)
 
#### dontSeeEmail

Opposite to seeEmail.

```php
$I->dontSeeEmail([
    'inbox' => 'john@example.org',
    'sender' => 'no-reply@company.com',
    'subject' => 'Welcome John!',
    'since' => 'P2M',
], 30);
```

 * `param array`    $criterias
 * `param int`      $timeoutInSecond (optional)
