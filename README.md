# Laravel Model Sanitize

A simple package to sanitize model data to create/update table records.

## Installation

Require the package using composer:

```bash
composer require touhidurabir/laravel-model-sanitize
```

## What is does ?
The `Sanitize` package sanitize the passed `attributes` to proper model fillables at create or update. 

A model has table schema based multiple attributes associated with it. When we try to create a new model record or update an existing model record, we must provide the an array attributes that is propelry mapped to those arrtibute or table columns names . For example 

```php
$user = User::create([
    'email' => 'somemail@test.com',
    'password' => Hash::make('password')
]);
```

The above code will run without any issue as both the `email` and `password` column presents in the users table . But for the following code

```php
User::create([
    'email' => 'somemail@test.com', 
    'password' => 'password', 
    'data' => 'some data'
]);
```

It will throw an `\Illuminate\Database\QueryException` if the `data` column not present in the users table.

```sql
Illuminate\Database\QueryException: SQLSTATE[HY000]: General error: 1 table users has no column named data (SQL: insert into "users" ("email", "password", "data", "updated_at", "created_at") values (somemail@mail.com, password, data, 2021-11-14 20:11:04, 2021-11-14 20:11:04))
```

The `Sanitize` package target to make it easier to handle such case as follow by including the `Sanitizable` trait in the models

```php
$data = [
    'email' => 'somemail@test.com', 
    'password' => 'password', 
    'data' => 'some data'
];

User::create($data);
```
The above code will work if the `Sanitizable` trait is used in the `User` model class. it will sanitize the passed attributed to model fillables and table columns, thus removing the `extra or non useable attributes` from it . 

## How it will be helpful ?

A great use case of this package is where one need to create multiple model instances from validated request data . For example

```php
$validated = $request->validated();

$user = User::create($validated);

$profile = $user->profile->create($validated);
```
I personally use this appraoch in many of my laravel apps . 

## Usage

Use the trait `Sanitizable` in model where uuid needed to attach

```php
use Touhidurabir\ModelSanitize\Sanitizable;
use Illuminate\Database\Eloquent\Model;

class User extends Model {
    
    use Sanitizable;
}
```

And thats all . it will automatically work for all the following methods 
- **updateOrCreate**
- **firstOrCreate**
- **firstOrNew**
- **create**
- **forceCreate**
- **update**

This package also includes some helper methods that can be used to handle the sanitization process manually. 

The `sanitize` static method will sanitize the given attributes list and retuen back the useable and valid attributes as an array 

```php
$data = [
    'email' => 'somemail@test.com', 
    'password' => 'password', 
    'data' => 'some data', 
    'name' => 'Test User'
];

User::sanitize($data);
```

This will return back as such : 
```php
[
    'email' => 'somemail@test.com', 
    'password' => 'password', 
    'name' => 'Test User'
]
```

The `gibberish` static method will sanitize the given attributes list and retuen back the gibberish/non userbale attributes as an array 

```php
$data = [
    'email' => 'somemail@test.com', 
    'password' => 'password', 
    'data' => 'some data', 
    'name' => 'Test User'
];

User::gibberish($data);
```

This will return back as such : 
```php
[
    'data' => 'some data', 
]
```

The `sanitize` and `gibberish` methods can be used to check or manually sanitize and evaluate the in valid data that can be passed to create/update model records.

It is also possible to `disable/enable` the sanitization process at the runtime using the static methods `disableSanitization` and `enableSanitization` . For example, 

```php
User::disableSanitization(); // disable the sanitization process
User::enableSanitization();  // enable the sanitization process if previously disabled
```

The same helpers are also available through the `ModelSanitize` facade, which takes the model class name (or a model instance) as the first argument. The model must use the `Sanitizable` trait.

```php
use Touhidurabir\ModelSanitize\Facades\ModelSanitize;

ModelSanitize::sanitize(User::class, $data);   // same as User::sanitize($data)
ModelSanitize::gibberish(User::class, $data);  // same as User::gibberish($data)
```

## Upgrade note: extending `SanitizableQueryBuilder`

This only affects apps that **subclass** `Touhidurabir\ModelSanitize\Builder\SanitizableQueryBuilder` and override `updateOrCreate`, `firstOrCreate` or `firstOrNew`.

To support Laravel 12 and 13, where these methods accept the values as `Closure|array`, the package declares the `$values` parameter **without a type**. A subclass that still declares it as `array $values` now fails with a fatal "declaration must be compatible" error, on every Laravel version. Drop the type in your override:

```php
// Before
public function firstOrCreate(array $attributes = [], array $values = [])

// After
public function firstOrCreate(array $attributes = [], $values = [])
```

`$values` may then be a `Closure` on Laravel 12+, so call it (or pass it on to `parent::`) before using it as an array.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENSE.md)
