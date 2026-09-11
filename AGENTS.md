# Календарь звонков

Сервис записи на звонки фиксированной длительности (30 мин) по мотивам Cal.com. Гость выбирает свободный слот и занимает его; способ связи — вне сервиса.

Стек: Laravel 13 (PHP 8.3) + Blade + Tailwind v4 (Vite) + SQLite; PHPUnit 12, Laravel Pint; TypeSpec → OpenAPI 3.1 с генерацией серверного кода и TS-схемы.

## Сущности

Каноничный глоссарий доменных терминов (звонок, запись, слот, организатор, гость, уведомление, подтверждение) — в `CONTEXT.md`. Используй термины оттуда, без синонимов из списка «избегать».

Модели Eloquent → таблицы БД:

| Модель          | Таблица           | Назначение                                                              |
| --------------- | ----------------- | ----------------------------------------------------------------------- |
| `User`          | `users`           | Организатор (логинится; на MVP единственный)                            |
| `Booking`       | `bookings`        | Запись гостя на слот (`slot_start_at` unique, `invitee_name`, `invitee_email`) |
| `Availability`  | `availabilities`  | Окно доступности (`weekday`, `start_time`, `end_time`)                  |

Служебные таблицы фреймворка: `cache`, `jobs`.

## Команды

### Установка и разработка

- `composer run setup` — полный bootstrap (composer/npm install, .env, key, migrate, build)
- `composer install` — PHP-зависимости
- `npm install` — JS-зависимости
- `composer run dev` — dev-сервер PHP + Vite (http://localhost:8000)
- `npm run dev` — Vite dev-сервер с HMR
- `npm run build` — production-сборка ассетов

### Тесты и качество кода

- `composer test` — тесты (PHPUnit)
- `php artisan test --filter=Name` — конкретный тест
- `composer lint` — Pint, проверка стиля без правок
- `composer format` — автоформатирование (Pint)
- `npm run typecheck` — проверка TS-типов (`tsc --noEmit`)

### Базы данных

- `php artisan migrate` — миграции (SQLite, `database/database.sqlite`)
- `php artisan make:migration` — новая миграция
- В тестах БД — in-memory

### TypeSpec / OpenAPI

- `npm run spec` — полная цепочка: `tsp:build` + `tsp:smoke` + `server:deterministic` + `typecheck`. Главная команда при правке контракта.
- `npm run tsp:build` — компиляция `typespec/main.tsp` в OpenAPI 3.1 + генерация серверного кода и TS-схемы
- `npm run tsp:smoke` — smoke-тест контракта (компиляция + проверки структуры)
- `npm run server:check` — регенерация серверного кода + проверка, что коммит совпадает (`git diff --exit-code`)
- `npm run server:deterministic` — проверка детерминизма генератора
- `npm run typecheck` — `tsc --noEmit`

### Прочее

- Health-роут: `/up` (используется в деплое, не удалять).

## Структура проекта

- `routes/web.php` — веб-роуты
- `routes/api.php` — **сгенерировано из TypeSpec, не править руками**
- `app/Http/Controllers/` — веб-контроллеры
- `app/Http/Controllers/Api/` — **сгенерированные стабы**: реализуй тело методов, не трогая сигнатуры
- `app/Http/Requests/` — веб Form Requests (`StoreBookingRequest`, `Store/UpdateAvailabilityRequest`)
- `app/Http/Requests/Api/` — **сгенерировано из TypeSpec, не править руками**
- `app/Models/` — Eloquent-модели
- `app/Dto/`, `app/Dto/Enums/` — **сгенерированные DTO, не править руками**
- `database/migrations/` — миграции
- `database/factories/` — фабрики для тестов
- `resources/views/` — Blade-шаблоны
- `resources/css/app.css`, `resources/js/app.js` — точки входа Vite
- `resources/js/api/` — **сгенерировано** (`schema.d.ts` — TS-схема из OpenAPI, `client.ts`, `index.ts`, `smoke.ts`)
- `tests/Feature/`, `tests/Unit/` — тесты PHPUnit
- `typespec/` — API-контракт (`main.tsp`, `tspconfig.yaml`, `scripts/smoke.mjs`)
- `api/openapi/openapi.yaml` — **сгенерированная OpenAPI 3.1 спецификация** (коммитится)
- `config/booking.php` — конфиг звонков (`host_email`, `host_name`, `slot_minutes`, `horizon_days`)
- `vite.config.js` — конфиг Vite (+ Tailwind v4 через `@tailwindcss/vite`)
- `Dockerfile`, `docker/` — multi-stage сборка для Railway (Node → PHP-FPM + Nginx)

## Рабочие правила

### Обязательные (hard)

- **Сгенерированный код не править руками** — только `typespec/main.tsp` + `npm run spec`. Список: `routes/api.php`, `app/Http/Controllers/Api/`, `app/Http/Requests/Api/`, `app/Dto/`, `app/Dto/Enums/`, `api/openapi/openapi.yaml`, `resources/js/api/schema.d.ts`. API-контроллеры — стабы: реализуй тело методов, не трогая сигнатуры/DTO/Requests.
- **Перед завершением — `composer test && composer lint`.** После правки TypeSpec — `npm run spec` + коммит обновлённых артефактов (`api/openapi/openapi.yaml`, сгенерированный код).
- **Новые PHP-файлы — через Artisan** (`php artisan make:...`).
- **Не менять `composer.json`/`package.json` без одобрения.**
- **Не создавать `*.md` без явной просьбы.**
- **Миграции:** не редактировать закоммиченные — создавай новую через `php artisan make:migration`. БД — SQLite; в тестах in-memory.
- **Валидация — через Form Requests**, не в контроллере.
- **Доменные термины — из `CONTEXT.md`**, без синонимов из списка «избегать».
- **Следовать конвенциям соседних файлов.**

### Рекомендации (soft)

- В моделях — `$fillable` + `casts()` (как `Booking`, `Availability`).
- Двойное бронирование — `UniqueConstraintViolationException` на unique `slot_start_at`.
- Уведомления — `Mail` гостю и `config('booking.host_email')`.
- Не удалять `/up` (используется в деплое).

## Коммиты — Conventional Commits

Все коммиты (включая от агента) идут по [Conventional Commits](https://www.conventionalcommits.org/):
`feat:`, `fix:`, `test:`, `ci:`, `docs:`, `refactor:`, `chore:` и т.д.
Используй `feat!:`, `fix!:` для breaking changes.

> **Примечание:** релизы через [release-please](https://github.com/googleapis/release-please-action) — запланировано (конфиг `.github/` будет добавлен). Формат коммитов обязателен, от него зависит автогенерация релизов.

## Agent skills

### Issue tracker

Issues живут в GitHub Issues для `flythq/php-project-386` (использует `gh`). Подробнее: `docs/agents/issue-tracker.md`.

### Triage labels

Пять канонических меток, как есть: `needs-triage`, `needs-info`, `ready-for-agent`, `ready-for-human`, `wontfix`. Подробнее: `docs/agents/triage-labels.md`.

### Domain docs

- `CONTEXT.md` в корне — каноничный глоссарий доменных терминов.
- `docs/adr/` — ADR (пока пусто, создаётся через `/domain-modeling`).
- `docs/agents/*.md` — конфигурация скиллов (`issue-tracker.md`, `triage-labels.md`, `domain.md`).