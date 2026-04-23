# Этап 5 — Form Request (валидация)

## Что такое Form Request?

Form Request — это отдельный класс для валидации входящих HTTP-запросов. Вместо того чтобы писать валидацию прямо в контроллере, выносим её в отдельный класс.

**Без Form Request (плохо):**
```php
public function index(Request $request)
{
    $validated = $request->validate([
        'q' => 'nullable|string|min:1|max:255',
        // ... ещё 8 правил
    ]);
    // логика контроллера
}
```

**С Form Request (хорошо):**
```php
public function index(ProductIndexRequest $request)
{
    // валидация уже прошла, данные чистые
}
```

Контроллер остаётся тонким — только оркестрация, не валидация.

---

## Как работает `authorize()`?

Form Request содержит два метода:
- `authorize()` — можно ли этому пользователю делать этот запрос? Возвращает `true/false`.
- `rules()` — правила валидации.

Для публичного API (без авторизации) `authorize()` просто возвращает `true`.

---

## Что такое `prepareForValidation()`?

Метод, который запускается **до** валидации и позволяет нормализовать входящие данные.

Нам нужно потому, что HTTP-запрос передаёт `in_stock=true` как **строку** `"true"`, а правило `boolean` в Laravel ожидает `true/false/1/0/on/off`. Строки `"true"` и `"false"` он не примет — нужно преобразовать вручную.

```php
protected function prepareForValidation(): void
{
    if ($this->has('in_stock')) {
        $this->merge([
            'in_stock' => filter_var($this->in_stock, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }
}
```

`filter_var()` — встроенная PHP-функция. Преобразует `"true"` → `true`, `"false"` → `false`, невалидное значение → `null`.

---

## Как Laravel возвращает ошибки валидации?

Если валидация не прошла, Laravel автоматически:
1. Возвращает `422 Unprocessable Entity`
2. В теле ответа — JSON с описанием ошибок

```json
{
  "message": "The per page field must not be greater than 100.",
  "errors": {
    "per_page": ["The per page field must not be greater than 100."]
  }
}
```

Это происходит автоматически — ничего дополнительно писать не нужно.

---

## Команда этапа

```bash
php artisan make:request ProductIndexRequest
```

---

## Параметры и правила валидации

| Параметр      | Правила                                                        |
|---------------|----------------------------------------------------------------|
| `q`           | nullable, string, min:1, max:255                               |
| `price_from`  | nullable, numeric, min:0                                       |
| `price_to`    | nullable, numeric, min:0, gte:price_from                       |
| `category_id` | nullable, integer, exists:categories,id                        |
| `in_stock`    | nullable, boolean                                              |
| `rating_from` | nullable, numeric, min:0, max:5                                |
| `sort`        | nullable, string, in:price_asc,price_desc,rating_desc,newest   |
| `page`        | nullable, integer, min:1                                       |
| `per_page`    | nullable, integer, min:1, max:100                              |

---

## Прогресс этапа

- [ ] Концепции объяснены и понятны
- [ ] `ProductIndexRequest` создан
- [ ] Метод `rules()` заполнен всеми правилами
- [ ] Метод `prepareForValidation()` нормализует `in_stock`
- [ ] Проверено: невалидный запрос возвращает `422`
