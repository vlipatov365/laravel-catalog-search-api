# Этап 3 — Миграции и модели

## Что такое миграции?

Миграция — это PHP-файл, который описывает изменение структуры базы данных.

**Зачем миграции вместо ручного SQL?**

Без миграций ты пишешь SQL вручную в psql или в GUI. Это работает, но:
- коллеги не знают, что ты изменил в схеме
- на новой машине нужно вручную воспроизводить все изменения
- нет истории изменений схемы

С миграциями:
- каждое изменение схемы — это файл в git
- `php artisan migrate` воспроизводит все изменения по порядку на любой машине
- `php artisan migrate:rollback` отменяет последнее изменение

Миграции живут в папке `database/migrations/`.

---

## Что такое Eloquent-модель?

Eloquent — это ORM (Object-Relational Mapping) Laravel. Модель — это PHP-класс, который представляет одну таблицу в БД.

```php
// Вместо такого SQL:
SELECT * FROM products WHERE id = 1;

// Пишешь так:
Product::find(1);
```

Каждый экземпляр модели = одна строка в таблице.

---

## Что такое `$fillable`?

`$fillable` — список полей, которые разрешено заполнять массово (через `create()` или `fill()`).

Без него Laravel блокирует массовое присвоение — это защита от атаки Mass Assignment, когда злоумышленник передаёт лишние поля в запросе.

```php
protected $fillable = ['name', 'price', 'category_id'];

// Это работает:
Product::create(['name' => 'Телефон', 'price' => 999]);

// Поле 'is_admin' не в fillable — оно будет проигнорировано
```

---

## Как работают связи `belongsTo` / `hasMany`?

**`hasMany`** — «один ко многим». Категория имеет много товаров:
```php
// В модели Category:
public function products(): HasMany
{
    return $this->hasMany(Product::class);
}

// Использование:
$category->products; // коллекция товаров этой категории
```

**`belongsTo`** — «принадлежит». Товар принадлежит одной категории:
```php
// В модели Product:
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

// Использование:
$product->category; // объект Category
```

Laravel строит SQL-запрос автоматически, используя `category_id` как внешний ключ.

---

## Индексы — зачем они?

Индекс ускоряет поиск по колонке. Без индекса PostgreSQL читает всю таблицу при фильтрации.

В нашем случае индексы нужны на `price`, `rating`, `created_at`, `category_id` — потому что по ним будет фильтрация и сортировка.

---

## Команды этапа

```bash
# Создать миграцию для categories
php artisan make:migration create_categories_table

# Создать миграцию для products
php artisan make:migration create_products_table

# Создать модель Category
php artisan make:model Category

# Создать модель Product
php artisan make:model Product

# Применить миграции
php artisan migrate
```

---

## Структура таблицы products

| Поле          | Тип              | Особенности              |
|---------------|------------------|--------------------------|
| `id`          | bigIncrements    | PK, auto-increment       |
| `name`        | string           | index                    |
| `price`       | decimal(10, 2)   | index                    |
| `category_id` | foreignId        | FK → categories.id, index|
| `in_stock`    | boolean          | default true             |
| `rating`      | float            | index                    |
| `created_at`  | timestamp        | index (через orderBy)    |
| `updated_at`  | timestamp        |                          |

---

## Прогресс этапа

- [ ] Концепции объяснены и понятны
- [ ] Миграция `create_categories_table` создана и заполнена
- [ ] Миграция `create_products_table` создана и заполнена
- [ ] Модель `Category` создана со связью `hasMany`
- [ ] Модель `Product` создана со связью `belongsTo` и `$fillable`
- [ ] `php artisan migrate` выполнен без ошибок