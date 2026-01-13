# Introducing Laravel JSON Object: Tame Your JSON Columns with Structured, Typed Objects

If you've ever found yourself wrestling with unstructured JSON data in your Eloquent models, you're not alone. While Laravel's native array casting is powerful, it often leaves you with "magic" arrays that are hard to validate, difficult to type-hint, and prone to silent errors.

Today, we're excited to introduce [**Laravel JSON Object**](https://github.com/amadul/json-object), a new package that transforms your JSON columns into strongly-typed, schema-aware objects.

## The Problem with Arrays

We've all written code like this:

```php
// In your controller...
$product->attributes = [
    'color' => 'red',
    'size' => '42', // String or Int?
    'meta' => [
        'author' => 'John' 
    ]
];

// Later...
if ($product->attributes['meta']['author'] ?? false) {
    // Manually digging through nested arrays 😫
}
```

This approach lacks structure. What if `size` must be an integer? What if `color` is required? What if you typo a key?

## The Solution: Structured JSON Objects

**Laravel JSON Object** allows you to define a dedicated class for your JSON structure, complete with validation rules, type casting, and accessors.

### 1. Define Your Object

You can scaffold a new JSON object using the Artisan command:

```bash
php artisan make:json ProductAttributes
```

Then, define your schema, casts, and validation rules:

```php
namespace App\Json;

use Laravel\JsonObject\JsonObject;
use Laravel\JsonObject\Concerns\HasAccessors;
use Laravel\JsonObject\Concerns\HasValidation;
use Laravel\JsonObject\Contracts\ValidatesJson;

class ProductAttributes extends JsonObject implements ValidatesJson
{
    use HasAccessors, HasValidation;

    // 🛡️ Whitelist allowed fields
    protected array $schema = [
        'color',
        'size',
        'meta', 
    ];

    // 🔄 Type casting (supports nested objects!)
    protected array $casts = [
        'size' => 'integer',
        'meta' => ProductMeta::class, // Nested JSON Object
    ];

    // ✅ Standard Laravel Validation
    public function rules(): array
    {
        return [
            'color' => 'required|string|in:red,blue,green',
            'size'  => 'required|integer|min:0',
        ];
    }
}
```

### 2. Cast in Your Model

Integrate it seamlessly into your Eloquent model:

```php
use App\Json\ProductAttributes;

class Product extends Model
{
    protected $casts = [
        'attributes' => ProductAttributes::class,
    ];
}
```

### 3. Enjoy the Power 💪

Now your JSON column behaves like a first-class citizen:

```php
$product = new Product();

// Assign data (auto-casted!)
$product->attributes = [
    'color' => 'red',
    'size'  => '42', // Casts to integer 42
    'meta'  => ['author' => 'Trae']
];

// Validate on demand
$product->attributes->validate(); 

// Access nested data easily
echo $product->attributes->meta->author; // "Trae"
echo $product->attributes->get('meta.author'); // Dot notation support

// Dirty tracking
if ($product->attributes->dirty()) {
    // Handle changes...
}

$product->save();
```

## Key Features

*   **🛡️ Strongly Typed**: No more guessing array keys.
*   **🔄 Nested Casting**: Create deep hierarchies of typed objects.
*   **✅ Validation**: Uses Laravel's powerful Validator under the hood.
*   **🔍 Dirty Tracking**: Know exactly what changed inside your JSON.
*   **⚡ Artisan Integration**: `make:json` command for rapid development.
*   **🔌 Macroable**: Extend functionality easily.

## Getting Started

You can install the package via Composer:

```bash
composer require laravel/json-object
```

Check out the [documentation on GitHub](https://github.com/laravel/json-object) to learn more about advanced features like custom casters, logging, and more.

---
*Transform your messy JSON arrays into structured, reliable objects today with Laravel JSON Object.*
