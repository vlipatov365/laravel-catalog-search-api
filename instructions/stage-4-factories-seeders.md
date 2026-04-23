# Этап 4 — Factories и Seeders

## Что такое Factory?

Factory — класс, который умеет генерировать фейковые данные для модели. Используется в тестах и для начального заполнения БД.

```php
// Создать одну категорию в БД:
Category::factory()->create();

// Создать 10 категорий:
Category::factory()->count(10)->create();
```

Фабрики лежат в `database/factories/`.

---

## Что такое Faker?

Faker — библиотека для генерации случайных реалистичных данных. Laravel встраивает её в фабрики через переменную `$this->faker` или хелпер `fake()`.

```php
fake()->name()         // "John Doe"
fake()->word()         // "laptop"
fake()->numberBetween(1, 100) // 42
fake()->boolean()      // true / false
```

---

## Что такое Seeder?

Seeder — класс, который запускает заполнение БД. Он вызывает фабрики и записывает данные.

```bash
php artisan db:seed   # запустить DatabaseSeeder
```

`DatabaseSeeder` — главный сидер, который вызывает остальные.

---

## Разница между `create()` и `make()`

| Метод | Что делает |
|-------|------------|
| `create()` | Создаёт запись **в БД** |
| `make()` | Создаёт объект **в памяти**, БД не трогает |

`make()` полезен в unit-тестах, где не нужна БД.

---

## Команды этапа

```bash
# Создать фабрику для Category
php artisan make:factory CategoryFactory

# Создать фабрику для Product
php artisan make:factory ProductFactory

# Запустить сидер
php artisan db:seed
```

---

## Прогресс этапа

- [ ] Концепции объяснены и понятны
- [ ] `CategoryFactory` создана с реалистичными названиями
- [ ] `ProductFactory` создана с рандомными полями
- [ ] `DatabaseSeeder` настроен: 10 категорий, 200 товаров
- [ ] `php artisan db:seed` выполнен без ошибок
