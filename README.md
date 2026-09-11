# Календарь звонков

Сервис записи на звонки по мотивам Cal.com: владелец публикует доступные слоты по 30 минут, гость выбирает свободное время и бронирует встречу, владелец видит список предстоящих встреч.

### Hexlet tests and linter status:
[![Actions Status](https://github.com/flythq/php-project-386/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/flythq/php-project-386/actions)

## Технологии

- [Laravel](https://laravel.com) 13
- [Blade](https://laravel.com/docs/blade) + [Tailwind CSS](https://tailwindcss.com)
- [Vite](https://vitejs.dev)
- [PHPUnit](https://phpunit.de) / [Laravel Pint](https://laravel.com/docs/pint)
- SQLite

## Деплой

Приложение собрано в Docker-образ (multi-stage: Node.js для Vite-сборки + PHP-FPM 8.4 с Nginx) и развёрнуто на [Railway](https://railway.app).

Публичная ссылка: https://app-production-f2a7.up.railway.app

Архитектура:
- **Dockerfile** — multi-stage сборка: `node:20-alpine` (Vite) → `php:8.4-fpm-alpine` + Nginx
- **Запуск по PORT** — Nginx слушает `$PORT` (Railway передаёт порт через env)
- **SQLite на persistent volume** — БД на volume `/app/db-vol`, данные переживают редеплой
- **docker/start.sh** — envsubst-рендеринг nginx.conf, миграции, запуск PHP-FPM + Nginx

Health-чек: `/up`

## Запуск

```bash
composer install
npm install
npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev
```

Приложение откроется на http://localhost:8000.

## Docker-запуск

```bash
docker build -t call-calendar .
docker run --rm -e PORT=8000 -p 8000:8000 call-calendar
```

Приложение откроется на http://localhost:8000.

## Команды

- `composer run dev` — локальный dev-сервер (PHP + Vite)
- `composer test` — тесты
- `composer lint` — линтер (Pint, проверка стиля)
- `npm run dev` — Vite dev-сервер (HMR)
- `npm run build` — сборка ассетов для production
