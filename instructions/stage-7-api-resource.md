# Этап 7 — API Resource (трансформация ответа)

## Цель

Создать слой трансформации между Eloquent-моделью и JSON-ответом.

---

## Концепции

### Что такое API Resource и зачем он нужен?

Когда контроллер возвращает `$product->toArray()` или просто модель — Laravel сериализует все поля модели как есть. Это плохо по нескольким причинам:

- Могут утечь служебные поля (`password_hash`, внутренние флаги)
- Формат ответа жёстко привязан к структуре таблицы
- Если переименуешь колонку в БД — сломается API

`API Resource` — это класс-трансформер. Он явно описывает, какие поля и в каком виде попадут в JSON. Модель может меняться, а формат ответа остаётся стабильным.

---

### Разница между `JsonResource` и `ResourceCollection`

`JsonResource` (`ProductResource`) — трансформирует **одну** модель:

```php
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'price'    => $this->price,
        ];
    }
}
```

`ResourceCollection` (`ProductCollection`) — обёртка для **коллекции** ресурсов. Автоматически применяет `ProductResource` к каждому элементу и добавляет `meta` с пагинацией.

---

### Зачем переопределять `paginationInformation()`?

По умолчанию Laravel добавляет в ответ громоздкий `meta` с полями типа `links`, `path`, `from`, `to` и т.д.

Нам нужен простой формат:

```json
{
  "current_page": 1,
  "per_page": 20,
  "total": 42,
  "last_page": 3
}
```

`paginationInformation()` — метод `ResourceCollection`, который отвечает за формирование этого блока. Переопределив его, мы контролируем что именно попадёт в `meta`.

---

## Прогресс этапа

- [ ] Концепции объяснены и понятны
- [ ] `ProductResource` создан
- [ ] `ProductCollection` создан
- [ ] `paginationInformation()` переопределён с нужным форматом `meta`
