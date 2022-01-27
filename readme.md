
# Laravel Eloquent Status

[![Latest Version on Packagist](https://img.shields.io/packagist/v/makeabledk/laravel-eloquent-status.svg?style=flat-square)](https://packagist.org/packages/makeabledk/laravel-eloquent-status)
[![Build Status](https://img.shields.io/github/workflow/status/makeabledk/laravel-eloquent-status/Run%20tests?label=Tests)](https://github.com/makeabledk/laravel-eloquent-status/actions)
[![StyleCI](https://styleci.io/repos/102474433/shield?branch=master)](https://styleci.io/repos/102474433)

**Check out the blog post explaining the concepts of this package:**

https://medium.com/@rasmuscnielsen/an-eloquent-way-of-handling-model-state-c9aa372e9cb8
___

Most models has some sort of status or state to it. Few examples could be

- Post: draft, private, published, 
- Job: applied, accepted, declined, completed, cancelled
- Approval: pending, reviewing, approved

Traditionally you may find yourself having a `scopeAccepted` and then additionally a `ìsAccepted` helper method to test a model-instance against a specific status.

This package offers a very handy way of dealing with statuses like these without cluttering you model.

When you've successfully setup this package you'll be able to achieve syntax like

````php
Approval::status('approved')->get(); // Collection
````

````php
$model->checkStatus('approved'); // bool
````

___

Makeable is web- and mobile app agency located in Aarhus, Denmark.

## Installation

You can install this package via composer:

``` bash
composer require makeabledk/laravel-eloquent-status
```

## Example usage

Given our Approval example from earlier we may have the following database fields:

- id
- *... (some foreign keys)*
- tutor_approved_at
- teacher_approved_at
- assessor_approved_at
- created_at
- updated_at

Let's start out by creating a status class that holds our status definitions

### Getting started

#### 1. Create a status class

We will define all our valid statuses as public functions in a dedicated status class. 

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

Notice how the statuses are defined just like regular `scope` functions. While this example is super simple, you have the full power of the Eloquent Query Builder at your disposal!

**🔥 Tip:** We recommend that your statuses has unambiguous definitions, meaning that a model can only pass one definition at a time.
This will come in handy in the next few steps.

#### 2. Apply trait on the model

```php
<?php 
use \Makeable\EloquentStatus\HasStatus;

class Approval extends Eloquent 
{
    use HasStatus;
}
```

### Querying the database

Now we can query our database for approvals using the defined statuses:

```php
Approval::status(new ApprovalStatus('pending'))->get();
```

Again, notice how this is very close to just calling a scope like we're used to: `Approval::pending()`.

However there are som benefits to this new approach. 

- We've defined and encapsulated all our statuses in one place de-cluttering our model
- We can only query against valid statuses

```php
Approval::status(new ApprovalStatus('something-else'))->get(); // throws exception
```

For instance this makes it convenient and safe to accept a raw status from a GET filter in your controller and return the result with no further validation or if-switches.


### 🔥 Checking model status

The real magic of the package!

We can actually use the same status definitions to check if a model instance adheres to a given status.

````php
$approval->checkStatus(new ApprovalStatus('reviewing')); // true or false
````

This sorcery is powered by our other package [makeabledk/laravel-query-kit](https://github.com/makeabledk/laravel-query-kit).

**Note:** Make sure to see the *Limitations* section of this readme.

### Guessing model status

What if you wanted to know *which* status a model is from its attributes? Well you're in luck.

````php

<?php 
use \Makeable\EloquentStatus\HasStatus;

class Approval extends Eloquent 
{
    use HasStatus;
    
    public function getStatusAttribute()
    {
        return ApprovalStatus::guess($this);
    }
}
````

Now `$approval->status` would attempt resolve the approval status from your definitions.

**Note:** The status is guessed by checking each definition one-by-one until one passes. This is why you may consider unambiguous definitions.

Also you should be careful not to load relations in your definitions and generally consider a caching-strategy for large query-sets.

Furthermore see the *Limitations* section of this readme.

### Binding a default status to a model

Rather than passing an instance of a status class each time you perform a check, you may bind a default status class to your model:

````php
use Makeable\EloquentStatus\StatusManager;

StatusManager::bind(Approval::class, ApprovalStatus::class);
````

Now you may simply type name of the status

```php
$approval->checkStatus('accepted'); 
```
Other status classes than the default can still be used when passed explicitly.

You may bind the status classes in the `boot` function of your `AppServiceProvider` or create a separate service provider if you wish.


## Limitations

This package is an abstraction on top of [makeabledk/laravel-query-kit](https://github.com/makeabledk/laravel-query-kit).

QueryKit provides a mocked version of the native QueryBuilder, allowing to run a scope function against a model instance.

This approach ensures great performance with no DB-queries needed, but introduces certain limitations. 

While QueryKit supports most QueryBuilder syntaxes such as closures and nested queries, it *does not* support SQL language such as joins and selects. These limitation only applies to `checkStatus()` and `guess()` functions.

Check out the **Limitations** section in the [makeabledk/laravel-query-kit documentation](https://github.com/makeabledk/laravel-query-kit) for more information.


## Available methods on `HasStatus`

**- scopeStatus($status)**

```php
Approval::status(new ApprovalStatus('approved'))->get(); // Collection

// Or when default status is defined
Approval::status('approved')->get(); // Collection
```

**- scopeStatusIn($statuses)**

```php
Approval::statusIn(['pending', 'reviewing'])->get(); // Collection
```

**- checkStatus**

```php
$approval->checkStatus('approved'); // bool
```

**- checkStatusIn**

```php
$approval->checkStatusIn(['pending', 'reviewing']); // bool
```

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
