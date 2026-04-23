# Catalog Search API

REST API для поиска и фильтрации товаров каталога.

## Стек

- **PHP** 8.4, **Laravel** 13
- **PostgreSQL** 16
- **Docker** + Docker Compose

## Запуск

```bash
git clone https://github.com/vlipatov365/catalog-search-api.git && cd catalog-search-api
cp .env.example .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

API доступен на `http://localhost:8080`.

## Эндпоинт

### `GET /api/products`

Возвращает пагинированный список товаров с фильтрацией и сортировкой.

| Параметр      | Тип     | Описание                                                       |
|---------------|---------|----------------------------------------------------------------|
| `q`           | string  | Поиск по названию товара                                       |
| `category_id` | integer | ID категории (должна существовать в БД)                        |
| `price_from`  | numeric | Минимальная цена                                               |
| `price_to`    | numeric | Максимальная цена (не меньше `price_from`)                     |
| `in_stock`    | boolean | Только товары в наличии (`true` / `false`)                     |
| `rating_from` | numeric | Минимальный рейтинг (0–5)                                      |
| `sort`        | string  | Сортировка: `price_asc`, `price_desc`, `rating_desc`, `newest` |
| `page`        | integer | Номер страницы (по умолчанию: 1)                               |
| `per_page`    | integer | Товаров на странице (1–100, по умолчанию: 15)                  |

### Пример запроса

```
GET /api/products?q=keyboard&price_to=5000&in_stock=true&sort=price_asc&per_page=2
```

### Пример ответа

```json
{
  "data": [
    {
      "id": 12,
      "name": "Wireless Keyboard",
      "price": "1299.00",
      "in_stock": true,
      "rating": 4.5,
      "category": {
        "id": 3,
        "name": "Peripherals"
      }
    },
    {
      "id": 47,
      "name": "Mechanical Keyboard RGB",
      "price": "3990.00",
      "in_stock": true,
      "rating": 4.8,
      "category": {
        "id": 3,
        "name": "Peripherals"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 2,
    "total": 5,
    "last_page": 3
  }
}
```

### Коды ответов

| Код | Описание                    |
|-----|-----------------------------|
| 200 | Успешный запрос             |
| 422 | Ошибка валидации параметров |
| 404 | Ресурс не найден            |

## Тесты

```bash
docker compose exec app php artisan test
```
