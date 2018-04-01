
# Laravel Eloquent Status

[![Latest Version on Packagist](https://img.shields.io/packagist/v/makeabledk/laravel-eloquent-status.svg?style=flat-square)](https://packagist.org/packages/makeabledk/laravel-eloquent-status)
[![Build Status](https://img.shields.io/travis/makeabledk/laravel-eloquent-status/master.svg?style=flat-square)](https://travis-ci.org/makeabledk/laravel-eloquent-status)
[![StyleCI](https://styleci.io/repos/102474433/shield?branch=master)](https://styleci.io/repos/102474433)

Most models has some sort of status or state to it. Few examples

- Post: draft, private, published, 
- Job: applied, accepted, declined, completed, cancelled
- Approval: pending, reviewing, approved

Traditionally you may find yourself having `scopeAccepted` and then additionally a `ìsAccepted` helper method to check the instance it self.

This package offers a very handy way of dealing with statuses like these without cluttering you model.

Makeable is web- and mobile app agency located in Aarhus, Denmark.

## Install

You can install this package via composer:

``` bash
composer require makeabledk/laravel-eloquent-status
```

## Example usage

Given our Approval example from earlier we may have the following model attributes:

- tutor_approved_at
- teacher_approved_at
- assessor_approved_at

Let's start out by creating a status class that holds our status definitions

### Creating a status class

We will define all our valid statuses as public function in our status class. 

You have the full power of the Eloquent Query Builder at your disposal.

````php
<?php

class ApprovalStatus extends \Makeable\EloquentStatus\Status
{
    public function pending($query)
    {
        return $query
            ->whereNull('tutor_approved_at')
            ->whereNull('teacher_approved_at')
            ->whereNull('assessor_approved_at');
    }

    public function reviewing($query)
    {
        return $query
            ->whereNotNull('tutor_approved_at')
            ->whereNull('assessor_approved_at');
    }
    
    public function approved($query)
    {
        return $query
            ->whereNotNull('tutor_approved_at')
            ->whereNotNull('teacher_approved_at')
            ->whereNotNull('assessor_approved_at');
    }
}
````

Tip: we recommend that your statuses has unambiguous definitions, meaning that a model can only pass one definition at a time.

This will come in handy in the next few steps.

### Querying our model

Before we can query our model we need to add the `HasStatus` trait.

```php
<?php 

class Approval extends Model 
{
    use \Makeable\EloquentStatus\HasStatus;
}
```

Now we can query our database:

```php
Approval::status(new ApprovalStatus('pending'))->get();
```

Notice how this is very close to just calling a scope like we're used to: `Approval::pending()`.

However, there are som benefits to this new approach. For instance, we can only query against valid statuses.

```php
Approval::status(new ApprovalStatus('something-else'))->get(); // throws exception
```

This makes it convenient and safe to accept a raw status from a GET filter in your controller and return the result with no further validation or if-switches.

### Checking model status

Even more importantly we can actually use the same status definitions to check a single instance of our model.

````php
Approval::first()->checkStatus(new ApprovalStatus('reviewing')); // true / false
````
This sorcery is achieved by the magical powers of [makeabledk/laravel-query-kit](https://github.com/makeabledk/laravel-query-kit).

**Note:** While QueryKit supports most QueryBuilder syntaxes such as closures and nested queries, it *does not* support SQL language such as joins and selects. 

### Guessing model status

What if you wanted to know *which* status a model is from its attributes? Well you're in luck.

````php
<?php 

class Approval extends Model 
{
    use \Makeable\EloquentStatus\HasStatus;

    public function getStatusAttribute()
    {
        return ApprovalStatus::guess($this);
    }
}
````

Now `Approval::first()->status` would attempt resolve the approval status from your definitions.

**Note:** The status is guessed by checking each definition one-by-one until one passes. 
You should be careful not to load relations in your definitions or if so consider a caching-strategy for performance reasons.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

You can run the tests with:

```bash
composer test
```

## Contributing

We are happy to receive pull requests for additional functionality. Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Rasmus Christoffer Nielsen](https://github.com/rasmuscnielsen)
- [All Contributors](../../contributors)

## License

Attribution-ShareAlike 4.0 International. Please see [License File](LICENSE.md) for more information.