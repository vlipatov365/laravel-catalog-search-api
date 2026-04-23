# Этап 8 — Controller и роут

## Цель

Связать всё вместе: контроллер принимает запрос, передаёт в сервис, пагинирует, возвращает ресурс.

---

## Концепции

### Как Laravel инжектит FormRequest в контроллер?

Когда в сигнатуре метода контроллера указан тип `ProductIndexRequest` — Laravel автоматически:
1. Создаёт экземпляр этого класса
2. Запускает валидацию
3. Если валидация провалилась — возвращает `422` до того, как метод вообще выполнится
4. Если прошла — передаёт объект в метод

Нам не нужно вручную вызывать `$request->validate()` — FormRequest делает это сам.

---

### Что такое Dependency Injection в Laravel?

Laravel умеет автоматически создавать объекты и передавать их туда, где они нужны.

```php
public function index(ProductIndexRequest $request, ProductFilterService $service)
```

Laravel видит типы параметров, создаёт нужные объекты и передаёт их в метод. Мы не пишем `new ProductFilterService()` вручную.

---

### N+1 проблема и eager loading

N+1 — это когда для списка из N товаров делается N дополнительных запросов к БД (по одному на каждую категорию).

```
SELECT * FROM products          -- 1 запрос
SELECT * FROM categories WHERE id = 1  -- запрос для товара 1
SELECT * FROM categories WHERE id = 2  -- запрос для товара 2
... и так для каждого товара
```

`->with('category')` — решает проблему одним дополнительным запросом:

```
SELECT * FROM products          -- 1 запрос
SELECT * FROM categories WHERE id IN (1, 2, 3, ...)  -- 1 запрос на всех
```

---

### `routes/api.php` vs `routes/web.php`

`web.php` — для браузерных маршрутов: сессии, CSRF-защита, куки.  
`api.php` — для API: без сессий, без CSRF, автоматический префикс `/api`.

Роут `GET /api/products` регистрируется в `api.php` как `GET /products` — префикс `/api` добавляется автоматически.

---

## Прогресс этапа

- [ ] Концепции объяснены и понятны
- [ ] `ProductController` создан в `app/Http/Controllers/Api/`
- [ ] Метод `index()` реализован
- [ ] Eager loading через `->with('category')`
- [ ] Роут зарегистрирован в `routes/api.php`
- [ ] `php artisan route:list` показывает роут
