# 📦 Laravel JSON Object Toolkit

> Structured, castable, schema-aware JSON objects for Eloquent models
> With `php artisan make:json`

---

## 1️⃣ Package Overview

Laravel JSON Object Toolkit provides a **first-class JSON domain layer** for Eloquent models.

It allows developers to:

* Define structured JSON objects
* Cast them directly on Eloquent models
* Safely read/write nested attributes
* Keep full compatibility with native JSON queries
* Generate JSON classes via Artisan commands

---

## 2️⃣ Why This Package Exists

Laravel treats JSON columns as:

* arrays
* collections
* raw values

This package introduces:

* **strong typing**
* **schema awareness**
* **encapsulation**
* **future-proof evolution**

Without affecting:

* query builder
* migrations
* existing apps

---

## 3️⃣ Installation

```bash
composer require vendor/laravel-json-object
```

Publish config (optional):

```bash
php artisan vendor:publish --tag=json-object-config
```

---

## 4️⃣ Directory Structure

```
app/
 └── Json/
     └── AttributeJson.php
```

Configurable via `config/json-object.php`.

---

## 5️⃣ Artisan Command

### Create JSON Object

```bash
php artisan make:json AttributeJson
```

With model binding:

```bash
php artisan make:json AttributeJson --model=Product --column=attributes
```

---

## 6️⃣ Generated Class (Default Stub)

```php
namespace App\Json;

use Illuminate\Database\Eloquent\Casts\JsonObject;

class AttributeJson extends JsonObject
{
    public static function from(array $value): static
    {
        return new static($value);
    }

    public function __construct(
        protected array $attributes = []
    ) {}

    public function toArray(): array
    {
        return $this->attributes;
    }
}
```

---

## 7️⃣ Schema & Casting (Optional)

```php
class AttributeJson extends JsonObject
{
    protected array $schema = [
        'color',
        'weight',
        'dimensions.width',
        'dimensions.height',
    ];

    protected array $casts = [
        'weight' => 'float',
    ];
}
```

---

## 8️⃣ Model Integration

```php
class Product extends Model
{
    protected $casts = [
        'attributes' => AttributeJson::class,
    ];
}
```

---

## 9️⃣ Usage Examples

### Reading Values

```php
$product->attributes->get('color');
```

### Writing Values

```php
$product->attributes->set('color', 'Red');
$product->save();
```

### Nested Access

```php
$product->attributes->get('dimensions.width');
```

---

## 🔟 Query Compatibility (Guaranteed)

```php
Product::where('attributes->color', 'Red')->get();
```

✔ Works exactly as before
✔ Package does not intercept queries

---

## 1️⃣1️⃣ JSON Serialization

```php
return $product->attributes->toArray();
```

Or automatically via model serialization.

---

## 1️⃣2️⃣ Configuration

```php
return [
    'path' => app_path('Json'),
    'namespace' => 'App\\Json',
];
```

---

## 1️⃣3️⃣ Package Internals

### Core Classes

| Class           | Responsibility           |
| --------------- | ------------------------ |
| JsonObject      | Base abstraction         |
| JsonCaster      | Eloquent integration     |
| MakeJsonCommand | Artisan generator        |
| JsonSchema      | Optional schema handling |

---

## 1️⃣4️⃣ Testing Strategy

* Feature tests for casting
* Unit tests for JSON mutation
* Integration tests for query safety

---

## 1️⃣5️⃣ Roadmap

✔ v1.0 – Core JSON object
✔ v1.1 – Schema validation
✔ v1.2 – Dirty tracking
✔ v2.0 – Draft approval integration

---

# 🤖 AI PROMPTS (IMPORTANT)

These prompts are designed to:

* generate new JSON classes
* refactor JSON logic
* assist code reviews
* enforce package conventions

---

## Prompt 1 — Generate JSON Object

> **Prompt**

```
Create a Laravel JSON Object class using laravel-json-object package.

Name: ProductAttributes
Fields:
- color (string)
- weight (float)
- dimensions.width (float)
- dimensions.height (float)

Include schema and casts.
```

---

## Prompt 2 — Refactor Array JSON to JsonObject

```
Refactor this Laravel model JSON array into a JsonObject class
using laravel-json-object conventions.

Ensure query compatibility is preserved.
```

---

## Prompt 3 — Add Model Cast Automatically

```
Given this Laravel model, add a JSON Object cast
using laravel-json-object package without breaking existing casts.
```

---

## Prompt 4 — Review JSON Object

```
Review this JsonObject class for:
- schema correctness
- cast safety
- backward compatibility
- Laravel coding standards
```

---

## Prompt 5 — Migration Safety Check

```
Analyze whether this JsonObject change affects
existing JSON queries or database migrations.
Explain clearly.
```

---

## 1️⃣6️⃣ Laravel Core Alignment Statement

This package:

* does not override Laravel behavior
* uses existing cast mechanisms
* can be partially upstreamed into core
* follows Laravel contribution standards

---

## 🏁 Final Notes

This package is:
✔ framework-aligned
✔ core-compatible
✔ future-proof
✔ RFC-ready

---
