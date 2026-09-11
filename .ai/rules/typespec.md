---
paths:
  - 'typespec/**'
---

# Typespec

## Регенерация клиентского SDK после правки контракта
После правки TypeSpec-контракта запускать `npm run spec` (полная цепочка генерации + проверки, включает компиляцию TypeSpec, `sdk:build` и typecheck) и коммитить оба артефакта: `api/openapi/openapi.yaml` (OpenAPI-спецификация) и `resources/js/api/schema.d.ts` (сгенерированные TS-типы для фронтенд-SDK). Не править `schema.d.ts` вручную — он авто-генерируется из openapi.yaml через openapi-typescript.
