# 🧱 Feature Expansion Strategy (Laravel-Aligned)

## Golden Rule

> **`JsonObject` stays small.
> Features live in traits, contracts, and optional modules.**

This is exactly how Laravel itself evolves.

---

## 1️⃣ Feature Classification (Do This First)

Before adding anything, classify it:

| Feature Type      | Goes Where        |
| ----------------- | ----------------- |
| Core behavior     | Base `JsonObject` |
| Optional behavior | Trait             |
| Heavy logic       | Module / Service  |
| Opinionated       | Separate package  |
| App-specific      | Userland          |

If you skip this step, the package will become unmaintainable.

---

## 2️⃣ Use Traits for Features (Primary Method)

### Example: Getter / Setter API

```php
trait HasAccessors
{
    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->toArray(), $key, $default);
    }

    public function set(string $key, mixed $value): static
    {
        data_set($this->attributes, $key, $value);
        return $this;
    }
}
```

Usage:

```php
class AttributeJson extends JsonObject
{
    use HasAccessors;
}
```

✔ Opt-in
✔ Non-breaking
✔ Composable

---

## 3️⃣ Contracts for Feature Boundaries

### Example: Validation Contract

```php
interface ValidatesJson
{
    public function rules(): array;
}
```

Trait:

```php
trait HasValidation
{
    public function validate(): void
    {
        if (! $this instanceof ValidatesJson) {
            return;
        }

        validator($this->toArray(), $this->rules())->validate();
    }
}
```

✔ Feature exists only if implemented
✔ Clean boundaries

---

## 4️⃣ Feature Modules (Advanced Features)

For heavier logic, use **modules**:

```
src/
 ├── JsonObject.php
 ├── Traits/
 ├── Modules/
 │    ├── DirtyTracking/
 │    ├── Diffing/
 │    └── Drafting/
```

### Example: Dirty Tracking

```php
trait TracksDirtyAttributes
{
    protected array $original = [];

    protected function syncOriginal(): void
    {
        $this->original = $this->toArray();
    }

    public function dirty(): array
    {
        return array_diff_assoc($this->toArray(), $this->original);
    }
}
```

---

## 5️⃣ Feature Flags (Never Break Users)

```php
config/json-object.php

return [
    'features' => [
        'validation' => false,
        'dirty_tracking' => false,
    ],
];
```

Your package must be **safe by default**.

---

## 6️⃣ Versioning Strategy (Critical)

Follow **semantic versioning strictly**:

| Change          | Version |
| --------------- | ------- |
| New trait       | MINOR   |
| New module      | MINOR   |
| Behavior change | MAJOR   |
| Bug fix         | PATCH   |

Laravel users **expect this**.

---

## 7️⃣ Add Features via Artisan (DX Upgrade)

### Extend `make:json`

```bash
php artisan make:json AttributeJson --schema --casts --validation
```

Stub system:

```
stubs/
 ├── json.basic.stub
 ├── json.schema.stub
 ├── json.validation.stub
```

This keeps:

* generated code clean
* features explicit

---

## 8️⃣ Feature Examples (Safe Additions)

### ✅ Safe to Add

* `get() / set()`
* schema whitelist
* value casting
* nested object support
* array helpers
* serialization hooks

### ⚠️ Add Carefully

* validation
* dirty tracking
* diffing
* draft approval

### ❌ Never Add

* query helpers
* database mutation logic
* migrations
* admin UI

---

## 9️⃣ Backward Compatibility Checklist

Before merging any feature:

* [ ] Does this change `toArray()`?
* [ ] Does this change constructor signature?
* [ ] Does this affect casting?
* [ ] Does this affect queries?
* [ ] Is it opt-in?

If **any answer is “yes”**, stop.

---

## 🔟 Testing Strategy for New Features

Every new feature must have:

1. Unit test
2. Integration test
3. Regression test

Example:

```php
it('does not affect json queries', function () {
    Product::where('attributes->color', 'red')->exists();
});
```

---

## 1️⃣1️⃣ Roadmap-Driven Development

Do NOT add features randomly.

Use this order:

1. Accessors
2. Schema
3. Casting
4. Validation
5. Dirty tracking
6. Drafting
7. AI helpers

---

## 🤖 AI-ASSISTED FEATURE DESIGN PROMPTS

### Prompt: Feature Proposal

```
Design a new feature for laravel-json-object package.

Constraints:
- Must be opt-in
- Must not affect queries
- Must not modify JsonObject core
- Must use traits or contracts
Return:
- Trait name
- Contract (if needed)
- Example usage
- Test cases
```

---

## 🏁 Final Advice (Very Important)

> **Laravel core contributions succeed because of restraint, not power.**

Your architecture is already **correct**.
Now just **grow it slowly and cleanly**.

---

