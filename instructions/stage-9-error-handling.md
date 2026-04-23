# Этап 9 — Обработка ошибок

## Цель

Убедиться, что API всегда отвечает JSON и понятными кодами ошибок — никакого HTML.

---

## Концепции

### Как работает exception handling в Laravel 13?

В старых версиях Laravel был файл `app/Exceptions/Handler.php`. В Laravel 13 всё регистрируется в `bootstrap/app.php` через метод `->withExceptions()`.

```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (ModelNotFoundException $e, Request $request) {
        return response()->json(['message' => 'Not found'], 404);
    });
})
```

`render()` — регистрирует обработчик для конкретного типа исключения.

---

### Почему Laravel может отдавать HTML вместо JSON?

Laravel смотрит на заголовок `Accept` в запросе. Если он не содержит `application/json` — Laravel решает, что клиент ждёт HTML, и отдаёт страницу с ошибкой.

В браузере или через curl без заголовков — придёт HTML.

Решение: в `withExceptions()` указать, что все запросы к `/api/*` должны получать JSON:

```php
$exceptions->shouldRenderJsonWhen(function (Request $request) {
    return $request->is('api/*');
});
```

---

## Прогресс этапа

- [ ] Концепции объяснены и понятны
- [ ] `shouldRenderJsonWhen()` добавлен в `bootstrap/app.php`
- [ ] Обработчик `ModelNotFoundException` зарегистрирован
- [ ] Проверено: запрос без заголовка `Accept` возвращает JSON, не HTML
- [ ] Проверено: несуществующий `category_id` → `422`
