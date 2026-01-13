# Laravel JSON Object Toolkit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/amadul/json-object.svg?style=flat-square)](https://packagist.org/packages/amadul/json-object)
[![Total Downloads](https://img.shields.io/packagist/dt/amadul/json-object.svg?style=flat-square)](https://packagist.org/packages/amadul/json-object)
[![License](https://img.shields.io/packagist/l/amadul/json-object.svg?style=flat-square)](https://packagist.org/packages/amadul/json-object)

**Structured, castable, schema-aware JSON objects for Eloquent models.**

Transform your Eloquent JSON columns from messy arrays into strongly-typed, validated, and behavior-rich objects.

## 🚀 Features

- **🛡️ Strongly Typed**: Define classes for your JSON structures.
- **🔄 Eloquent Casting**: Seamless integration (feels like native attributes).
- **✅ Validation**: Built-in Laravel validation logic.
- **🔍 Dirty Tracking**: Know exactly what changed inside the JSON.
- **📝 Logging**: Track modifications automatically.
- **🔌 Extensible**: Plugin architecture via Macros.
- **⚡ Artisan Integration**: `make:json` command for rapid development.

---

## 📦 Installation

1. **Require the package** via Composer:

   ```bash
   composer require amadul/json-object
   ```

2. **Publish the configuration** (Optional):

   ```bash
   php artisan vendor:publish --tag=json-object-config
   ```

   This creates `config/json-object.php` where you can customize paths and global feature flags.

---

## 🛠️ Full Implementation Guide: Step-by-Step

Let's build a real-world example: **Product Attributes** for an E-commerce system. We want to store `color`, `size`, and `metadata` in a single JSON column but interact with them like structured data.

### Step 1: Create the JSON Object Class

Use the Artisan command to scaffold your class.

```bash
php artisan make:json ProductAttributes
```

This creates `app/Json/ProductAttributes.php`.

### Step 2: Define Structure & Behavior

Open the generated file. We will:
1.  Define the **Schema** (allowed fields).
2.  Define **Casts** (data types).
3.  Add **Traits** for extra power (Accessors, Validation, Dirty Tracking).

```php
namespace App\Json;

use Amadul\JsonObject\JsonObject;
use Amadul\JsonObject\Concerns\HasAccessors;
use Amadul\JsonObject\Concerns\HasValidation;
use Amadul\JsonObject\Concerns\TracksDirtyAttributes;
use Amadul\JsonObject\Concerns\HasLogging;
use Amadul\JsonObject\Contracts\ValidatesJson;

class ProductAttributes extends JsonObject implements ValidatesJson
{
    // 1. Add Capabilities
    use HasAccessors, 
        HasValidation, 
        TracksDirtyAttributes,
        HasLogging;

    /**
     * The whitelist of allowed attributes.
     * Any key not here will be filtered out on construction.
     */
    protected array $schema = [
        'color',
        'size',
        'sku',
        'tags',
        'metadata.manufactured_at',
    ];

    /**
     * Type casting for specific attributes.
     */
    protected array $casts = [
        'size' => 'integer',
        'tags' => 'array',
        'metadata.manufactured_at' => 'datetime', // Custom casting can be added
    ];

    /**
     * Validation rules using standard Laravel syntax.
     */
    public function rules(): array
    {
        return [
            'color' => 'required|string|in:red,blue,green',
            'size' => 'required|integer|min:0',
            'sku' => 'required|alpha_dash',
        ];
    }
}
```

### Step 3: Integrate with Eloquent Model

Open your `Product` model and cast the column (e.g., `attributes` or `data`) to your new class.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Json\ProductAttributes;

class Product extends Model
{
    protected $fillable = ['name', 'attributes'];

    protected $casts = [
        'attributes' => ProductAttributes::class,
    ];
}
```

### Step 4: Usage in Application

Now you can use it in your Controllers, Services, or Jobs.

#### Creating & Saving
```php
$product = new Product();
$product->name = 'T-Shirt';

// Initialize with array
$product->attributes = [
    'color' => 'red',
    'size' => '42', // Will be cast to int 42
    'sku' => 'TS-RED-42'
];

// Validate before saving (Optional but recommended)
try {
    $product->attributes->validate();
} catch (\Illuminate\Validation\ValidationException $e) {
    // Handle errors
}

$product->save();
```

#### Reading & Modifying
```php
$product = Product::find(1);

// Access with Dot Notation (via HasAccessors)
echo $product->attributes->get('color'); // "red"
echo $product->attributes->get('metadata.manufactured_at'); 

// Modify values
$product->attributes->set('color', 'blue');

// Check dirty state (via TracksDirtyAttributes)
if ($product->attributes->dirty()) {
    // Returns ['color' => 'blue']
    // Log is automatically triggered if HasLogging is enabled
}

$product->save(); // Eloquent handles serialization automatically
```

---

## 📚 Deep Dive: Core Concepts

### Schema Whitelisting
By defining `protected $schema`, you ensure strictly structured JSON. Any data passed to the constructor that isn't in the schema is discarded. This prevents "JSON pollution" with random keys.

### Type Casting
The `$casts` property works similarly to Eloquent casts but for internal JSON keys.
Supported types: `int`, `integer`, `float`, `real`, `double`, `string`, `bool`, `boolean`, `array`.

### Nested JSON Objects
You can cast attributes to other `JsonObject` classes, allowing for deep, structured JSON hierarchies.

```php
// App/Json/ProductMeta.php
class ProductMeta extends JsonObject
{
    protected $schema = ['author', 'year'];
}

// App/Json/ProductAttributes.php
class ProductAttributes extends JsonObject
{
    protected $casts = [
        'meta' => ProductMeta::class, // Nested casting
    ];
}

// Usage
$product->attributes = [
    'color' => 'red',
    'meta' => [
        'author' => 'John Doe',
        'year' => 2023
    ]
];

// Accessing nested objects
echo $product->attributes->get('meta')->get('author'); // "John Doe"
echo $product->attributes->get('meta.author'); // "John Doe" (via dot notation support)
```

### Validation
Implement `ValidatesJson` and define `rules()`. Call `$object->validate()` anywhere. It uses Laravel's `Validator` under the hood, so all standard rules work.

### Dirty Tracking
Include `TracksDirtyAttributes`.
- `dirty()`: Returns array of changed keys and new values.
- `syncOriginal()`: Called automatically on fetch, resets the baseline.

### Logging
Include `HasLogging`.
Configure the channel in `config/json-object.php`.
Use `$this->log('message', ['data'])` inside your JSON object methods to create audit trails of attribute changes.

---

## 🔌 Advanced: Plugins & Extensibility

The `JsonObject` class is `Macroable`. You can add methods at runtime, which is perfect for plugins or app-wide extensions.

**Example: Add an `approve()` method to all JSON objects.**

```php
// AppServiceProvider.php
use Amadul\JsonObject\JsonObject;

public function boot()
{
    JsonObject::macro('approve', function () {
        $this->set('status', 'approved');
        $this->set('approved_at', now()->toIso8601String());
        return $this;
    });
}

// Usage
$product->attributes->approve();
```

---

## ⚙️ Configuration

`config/json-object.php`

| Key | Description | Default |
| :--- | :--- | :--- |
| `path` | Directory for generated classes | `app_path('Json')` |
| `namespace` | Namespace for generated classes | `App\Json` |
| `features.validation` | Enable/Disable validation trait helpers | `true` |
| `features.dirty_tracking` | Enable/Disable dirty tracking | `true` |
| `features.logging` | Global logging toggle | `false` |
| `log_channel` | Laravel log channel to use | `stack` |

---

## 🧪 Testing Your JSON Objects

Since these are standard PHP classes, unit testing is straightforward.

```php
use App\Json\ProductAttributes;

it('validates product attributes', function () {
    $attr = ProductAttributes::from(['color' => 'invalid-color']);
    
    expect(fn() => $attr->validate())
        ->toThrow(\Illuminate\Validation\ValidationException::class);
});

it('casts size to integer', function () {
    $attr = ProductAttributes::from(['size' => '42']);
    expect($attr->get('size'))->toBe(42);
});
```

---

## 🤝 Contributing

1. Fork the repo.
2. Create your feature branch (`git checkout -b feature/amazing-feature`).
3. Commit your changes (`git commit -m 'Add amazing feature'`).
4. Push to the branch (`git push origin feature/amazing-feature`).
5. Open a Pull Request.

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
