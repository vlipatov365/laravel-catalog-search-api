# Этап 6 — ProductFilterService

## Зачем выносить фильтрацию в сервис?

Контроллер должен делать одно: принять запрос, вызвать нужные классы, вернуть ответ. Если добавить туда логику фильтрации — контроллер разрастётся и его станет сложно читать и тестировать.

Сервис — это обычный PHP-класс без наследования. Он отвечает за одну задачу: построить запрос к БД по заданным фильтрам.

---

## Что такое Builder?

`Builder` — это объект Eloquent, который хранит SQL-запрос, но ещё не выполняет его.

```php
$query = Product::query(); // Builder, SQL ещё не выполнен
$query->where('in_stock', true); // добавили условие
$results = $query->get(); // только здесь идёт запрос в БД
```

Сервис возвращает `Builder` (а не `get()` / коллекцию), потому что контроллер потом добавит к нему пагинацию — `->paginate()`. Если вернуть коллекцию, пагинировать уже нечего.

---

## Как работает `->when()`?

`when($condition, $callback)` — добавляет условие к запросу только если `$condition` истинно.

**Без `when()` (с if-блоками):**
```php
if (!empty($filters['q'])) {
    $query->where('name', 'like', '%' . $filters['q'] . '%');
}
if (!empty($filters['in_stock'])) {
    $query->where('in_stock', true);
}
```

**С `when()`:**
```php
$query
    ->when($filters['q'] ?? null, fn($q, $value) => $q->where('name', 'like', "%{$value}%"))
    ->when($filters['in_stock'] ?? null, fn($q, $value) => $q->where('in_stock', $value));
```

Результат одинаковый, но второй вариант цепочкой — читается как конвейер фильтров.

---

## Зачем `$sortMap` вместо `switch`?

`switch` — это логика. `$sortMap` — это данные. Данные проще читать и расширять.

```php
// switch — много кода
switch ($sort) {
    case 'price_asc': $query->orderBy('price', 'asc'); break;
    case 'price_desc': $query->orderBy('price', 'desc'); break;
    // ...
}

// $sortMap — декларативно
$sortMap = [
    'price_asc'    => ['price', 'asc'],
    'price_desc'   => ['price', 'desc'],
    'rating_desc'  => ['rating', 'desc'],
    'newest'       => ['created_at', 'desc'],
];
[$column, $direction] = $sortMap[$sort] ?? $sortMap['newest'];
$query->orderBy($column, $direction);
```

---

## Прогресс этапа

- [ ] Концепции объяснены и понятны
- [ ] Папка `app/Services/` создана
- [ ] Класс `ProductFilterService` создан
- [ ] Метод `apply(array $filters): Builder` реализован
- [ ] Все фильтры через `->when()`
- [ ] Сортировка через `$sortMap`
- [ ] Дефолтная сортировка — `newest`
