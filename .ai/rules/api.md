---
paths:
  - 'resources/js/api/**'
---

# Api

## Структура клиентского SDK в resources/js/api
`resources/js/api/schema.d.ts` — авто-генерируется из `api/openapi/openapi.yaml` (`npm run sdk:build`), не править руками. `resources/js/api/client.ts` — hand-written типизированный fetch-клиент; базовый URL через `import.meta.env.VITE_API_BASE_URL` (по умолчанию относительные пути, тот же origin). `resources/js/api/smoke.ts` — НЕ импортируется из app.js, существует только как цель для `npm run typecheck`. Новый фронтенд-код должен импортировать API через `./api` (index.ts), а не напрямую из schema/client.
