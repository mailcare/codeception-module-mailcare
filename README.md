# codeception-module-mailcare

MailCare module for Codeception 

## Installation

```
composer require "mailcare/codeception-module-mailcare"
```

## Documentation

Module for testing receiving emails using [MailCare](https://mailcare.io).

### Configuration

* url *optional* - API url of your mailcare server (default: https://mailix.xyz/api)
* login *optional* - login of your mailcare server
* password *optional* - password of your mailcare server
* timeoutInSeconds *optional* - Waits up to n seconds for an email to be received (default: 30 seconds)

#### Example
```
modules:
    enabled
        - MailCare:
            url: 'https://mailix.xyz/api'
            login: 'https://mailix.xyz/api'
            password: 'https://mailix.xyz/api'
```

### Criterias

 * `inbox`      Filter by inbox (test@example.com).
 * `sender`     Filter by sender (test@example.com).
 * `subject`    Filter by subject (Welcome).
 * `since`      Filter by createdAt (2018-01-19T12:23:27+00:00 or ISO 8601 durations).
 * `search`     Search by inbox or sender or subject (matching).
 * `unread`     Filter only by unread (true).
 * `favorite`   Filter only by favorite (true).

All criterias can be found in the [API Documentation of MailCare](https://mailcare.docs.apiary.io) except for page and limit.

Examples of `since` with ISO 8601 durations:
* P1D: one-day duration
* PT1M: one-minute duration (note the time designator, T, that precedes the time value)

### Actions

#### seeEmailCount

Checks that the email count equals expected value.
Waits up to $timeout seconds for the given email to be received.

```php
$I->seeEmailCount(2, [
    'inbox' => 'john@example.org',
    'sender' => 'no-reply@company.com',
    'subject' => 'Welcome John!',
    'since' => 'PT2M',
], 30);
```

 * `param int`      $expectedCount
 * `param array`    $criterias
 * `param int`      $timeoutInSeconds (optional)

#### seeEmail

Checks that the given email exists.
Waits up to $timeout seconds for the given email to be received.

```php
$I->seeEmail([
    'inbox' => 'john@example.org',
    'sender' => 'no-reply@company.com',
    'subject' => 'Welcome John!',
    'since' => 'PT2M',
], 30);
```

 * `param array`    $criterias
 * `param int`      $timeoutInSeconds (optional)
 
#### dontSeeEmail

Opposite to seeEmail.

```php
$I->dontSeeEmail([
    'inbox' => 'john@example.org'
    'since' => 'PT2M',
], 30);
```

 * `param array`    $criterias
 * `param int`      $timeoutInSeconds (optional)

#### grabLinksInLastEmail

In the last email, grabs all the links
Waits up to $timeout seconds for the given email to be received.

```php
$I->grabLinksInLastEmail([
    'inbox' => 'john@example.org'
    'since' => 'PT2M',
], 30);
```

 * `param array`    $criterias
 * `param int`      $timeoutInSeconds (optional)
 * `return array`   ['https://google.fr', 'https://mailcare.io']


#### grabTextInLastEmail

In the last email, grabs all the text corresponding to a regex.
Waits up to $timeout seconds for the given email to be received.

```php
$I->grabTextInLastEmail($regex, [
    'inbox' => 'john@example.org'
    'subject' => 'Your credentials'
    'since' => 'PT2M',
], 30);
```

 * `param array`    $criterias
 * `param int`      $timeoutInSeconds (optional)
 * `return array`   matches from preg_match_all
