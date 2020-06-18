# Laravel Api Resource

This package provides an easy method implement API resource controllers without having to write similar code. This is helpful if you have several models that are accessed via API calls and don't have complex relations.

*Note: this readme is a work in progress*

## Install

Using Composer

```bash
$ composer require taylornetwork/laravel-api-resource
```

## Setup

Models that will use this package must extend `TaylorNetwork\LaravelApiResource\Models\ApiResourceModel`, your models will continue to work as normal as the `ApiResourceModel` class extends Laravel's eloquent model class.


If your models are in a different namespace than `App\` be sure to publish the config and change `model_namespace` to your namespace.

## Usage

When creating a new controller related to a specfic model, all you need to do is extend `TaylorNetwork\LaravelApiResource\Controllers\ApiResourceController` and that handles Laravel's resource calls.

### Example

For example if you have a model `App\Customer` and you want to access it via API calls. 

#### Model

The Customer model will have a required `name` field that we can add to the `$validationRules` property.

```php
// app/Customer.php

namespace App;

use TaylorNetwork\LaravelApiResource\Models\ApiResourceModel;

class Customer extends ApiResourceModel
{
	protected $validationRules = [
		'name' => 'required',
	];
	
	protected $fillable = [
		'name', 'email', 'address',
	];
}
```

#### Routes

The routes must be added to `routes/api.php`

```php
// routes/api.php

Route::apiResource('customer', 'Api\\CustomerController');
```

#### Controller

This is all the code required to handle the API calls, the `ApiResourceController` handles all related routes. 

```php
// app/Http/Controllers/Api/CustomerController.php

namespace App\Http\Controllers\Api;

use TaylorNetwork\LaravelApiResource\Controllers\ApiResourceController;

class CustomerController extends ApiResourceController
{

}
```

If you have a more complex relation for a specfic route, you can override that in your controller.

```php
// app/Http/Controllers/Api/CustomerController.php

namespace App\Http\Controllers\Api;

use TaylorNetwork\LaravelApiResource\Controllers\ApiResourceController;
use App\Customer;

class CustomerController extends ApiResourceController
{
	public function show($id)
	{
		return Customer::with('someRelation')->find($id);
	}
}
```


