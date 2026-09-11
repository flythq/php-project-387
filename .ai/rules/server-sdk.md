---
paths:
  - 'app/Dto/**'
  - 'app/Http/Requests/Api/**'
  - 'app/Http/Controllers/Api/**'
  - 'routes/api.php'
  - 'app/Console/Commands/SpecGenerateServer.php'
---

# Server SDK

## Регенерация серверных артефактов после правки контракта
`app/Dto/`, `app/Http/Requests/Api/`, `app/Http/Controllers/Api/`, `routes/api.php` — генерируются из `api/openapi/openapi.yaml` командой `php artisan spec:generate-server` (входит в `npm run spec` цепочку). Не править руками — перегенерируются. После правки TypeSpec-контракта: `npm run spec` — полная цепочка (компиляция TypeSpec → openapi.yaml → schema.d.ts → серверные артефакты, smoke контракта, проверка детерминизма генератора, typecheck); коммитить все сгенерированные файлы. Проверка синхронности с закоммиченным состоянием (для CI): `npm run server:check` (регенерирует + `git diff --exit-code` против HEAD).

## Генератор
`app/Console/Commands/SpecGenerateServer.php` парсит OpenAPI YAML через `symfony/yaml` и эммитит DTO (объекты), PHP enum'ы (string-enums), Form Requests (query/path/body params → Laravel rules), контроллеры-стабы (RESTful по HTTP-методу + пути) и `routes/api.php`. Идемпотентен: повторный запуск не даёт diff. Вывод проходит `composer format` (Pint) без правок.
