# 🧠 LaravelQuery

**A lightweight, composable, and testable query builder for Laravel.**  
![Version](https://img.shields.io/badge/version-0.1.0-blue.svg)


It adds support for filtering, sorting, includes, and field selection using structured arrays — ideal for APIs, admin panels, or dynamic queries.

---

## ✨ Features

✅ Elegant and readable service-based query composition  
✅ Automatic operator handling (`equals`, `gt`, `like`, `between`, etc.)  
✅ Field-level whitelisting  
✅ Nested relationship validation (e.g., `author.posts.comments`)  
✅ Optional strict mode for development safety  
✅ Framework-agnostic, easily testable, and extendable

---

## 🚀 Installation (coming soon)**  

```bash
composer require jadismael/laravel-query
```

### Optional: Publish Config (coming soon)**  

Laravel will automatically discover the package. No configuration is required.  
If you'd like to alias it for convenience:

```php
// config/app.php

'aliases' => [
    'LaravelQuery' => \Jadismael\LaravelQuery\Facades\LaravelQuery::class,
],
```

---

## 📦 Usage

```php
use App\Models\Project;
use LaravelQuery;

$projects = LaravelQuery::fetch(
    Project::class,
    filters: ['status:equals' => 'active'],
    sorts: ['name' => 'asc'],
    includes: [],
    fields: ['name', 'description']
);
```

> `fetch()` returns an Eloquent query builder, allowing further chaining or pagination.

---

## 🛠 API

### ✅ `LaravelQuery::fetch(Model::class, ...)`

| Parameter   | Type     | Description |
|-------------|----------|-------------|
| `filters`   | `array`  | Filters using format `column:operator => value` |
| `sorts`     | `array`  | Sort directions, e.g. `['name' => 'desc']` |
| `includes`  | `array`  | Eager-load relations (validated), e.g. `['user', 'comments.author']` |
| `fields`    | `array`  | Only include top-level fields. Cannot be used with includes. |

---

## ✅ Supported Filters

| Operator      | Example Filter                      | SQL Equivalent                  |
|---------------|-------------------------------------|----------------------------------|
| `equals`      | `'name:equals' => 'John'`           | `where name = 'John'`           |
| `like`        | `'name:like' => 'Jo%'`              | `where name like 'Jo%'`         |
| `gt`, `lt`    | `'score:gt' => 5`                   | `where score > 5`               |
| `between`     | `'created_at:between' => [...]`     | `where between (...)`           |
| `in`, `notin` | `'status:in' => ['open', 'closed']` | `whereIn(status, [...])`        |
| `null`        | `'archived:null' => true`           | `whereNull('archived')`         |

All operators are validated via `OperatorRegistry`, which can be extended.

---

## ⚠️ Limitations (v0.1)

| Constraint                     | Notes                                                                 |
|-------------------------------|-----------------------------------------------------------------------|
| Fields + Includes             | Cannot use `fields` when using `includes`                            |
| Relationship Filters          | Filtering by related models (e.g., `author.name`) is not yet supported |
| Fields from Relationships     | Selecting nested fields like `user.name` is not supported             |
| Strict Mode                   | By default, invalid filters are ignored. Strict mode will throw exceptions. |

---

## 🔐 Strict Mode

Enable strict mode to **throw exceptions** on invalid filters, sorts, or includes:

```php
$service = new ResourceQueryService(
    builderFactory: new ResourceQueryBuilderFactory(strict: true),
    executor: new ResourceQueryExecutor()
);
```

---

## 🧪 Testing

This package is fully tested using [orchestra/testbench](https://github.com/orchestral/testbench) and SQLite.

To run tests:

```bash
# In the package root
touch database/testing.sqlite
vendor/bin/phpunit --coverage-text
```

GitHub Actions is preconfigured to run on every push and PR.

---

## 🧩 Extending

You can extend:

- `OperatorRegistry` to add custom operators
- `FilterParser` for custom filter parsing logic
- `ModelInspector` if your columns come from elsewhere

---

## 📁 File Structure

```
src/
  Services/
    Query/
      ├── ResourceQueryService.php
      ├── ResourceQueryBuilder.php
      ├── QueryFilters.php
      ├── QuerySort.php
      ├── QueryInclude.php
      ├── FilterParser.php
      └── OperatorRegistry.php
```

---

## 💡 Example: Full Query

```php
LaravelQuery::fetch(
    Project::class,
    filters: [
        'status:equals' => 'open',
        'priority:gt' => 3,
        'created_at:between' => ['2024-01-01', '2024-12-31'],
    ],
    sorts: ['name' => 'desc'],
    includes: ['user.tasks'],
    fields: [] // must be empty if using includes
)->paginate(10);
```

---

## 🤝 Contributing

Pull requests are welcome! Feel free to fork the repo, improve or test something, and submit a PR.

### To Run Locally

```bash
composer install
cp phpunit.xml.dist phpunit.xml
vendor/bin/phpunit
vendor/bin/ecs check
```

---

## 📄 License

MIT © [Jad Ismail](https://www.linkedin.com/in/jad-ismail/)
