# Contexto del proyecto — Articulos compuestos

## Resumen

Laravel 12 + **Filament** (`/admin`) para **productos compuestos**: componentes con precio por unidad, composicion, costo/precio de venta, historial, stock por insumo y sync saliente a **e-factura**. Rubro inicial joyeria; el modelo sirve para cualquier negocio con lista de materiales.

Repo: [articuloscompuestos](https://github.com/diegoforichi/articuloscompuestos). Hosting compartido: subir `vendor/` y `public/build/` compilado en local; **no** Composer/NPM en servidor.

## Estado operativo actual

| Area | Comportamiento |
|------|----------------|
| Tenants | Alta con onboarding opcional de admin; edicion sin onboarding; usuarios en **Usuarios del tenant**; badge **Sin usuarios** |
| Integracion | Una configuracion por `tenant_id` (UI + unique BD); solo superadmin |
| Stock | Entradas, movimientos (`Entrada #id` / `Consumo producto codigo`), saldos, consumo con preview; export CSV |
| Productos | Composicion, recalculo manual/automatico por tenant, sync e-factura |
| Roles | Superadmin sin `tenant_id` opera sobre tenant `default`; admin tenant solo su tenant |

Prueba local: `docs/UAT_LOCAL_MULTITENANT.md`. Pendientes y fases: `docs/PLAN_MAESTRO_IMPLEMENTACION.md`.

## Stack y restricciones

- **Laravel** 12, **PHP** 8.2, **MySQL** en produccion, **Filament** 4, **Tailwind** v4.
- Produccion: MySQL obligatorio; no SQLite legacy; no Node en servidor; assets compilados en local.
- Cola: `php artisan queue:work` o cron con `--stop-when-empty` (recalculo automatico).

## Estructura relevante

```
articuloscompuestos/
├── app/Filament/Resources/   # Panel CRUD
├── app/Services/             # StockService, EFacturaService
├── app/Support/              # StockMovementReferenceFormatter
├── database/migrations/
├── docs/
└── vendor/                   # Subir completo en deploy
```

## Convenciones del proyecto

- Textos de UI: `__('...')` — ver `resources/lang/es/` y `AGENTS.md`.
- Reglas de asistente y deploy: `.cursor/rules/`, `docs/DEPLOY_SHARED_HOSTING.md`.

## Documentacion vigente

- `README.md` — que hace hoy el sistema e instalacion
- `docs/ARTICULOS_MVP_SPEC.md` — reglas de negocio y modelo de datos
- `docs/ARTICULOS_FUNCIONALIDADES_CLIENTE.md` — lenguaje de negocio
- `docs/DEPLOY_SHARED_HOSTING.md` — deploy cPanel
- `CHANGELOG.md` — cambios versionados

**Ultima revision documental:** 2026-05-22
