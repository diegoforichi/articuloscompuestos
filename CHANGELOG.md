# Changelog

Historial de cambios reales del proyecto Articulos compuestos.

Formato basado en [Keep a Changelog](https://keepachangelog.com/es/1.0.0/),  
y este proyecto sigue [Semantic Versioning](https://semver.org/lang/es/).

## [Sin versionar] - 2026-05-22 (alineacion documental)

### Cambiado

- `docs/PLAN_MAESTRO_IMPLEMENTACION.md`, `docs/CONTEXT.md` y `docs/ARTICULOS_MVP_SPEC.md` alineados al estado operativo (sin narrativa de fases pendientes ya cerradas).
- README: nota sobre migraciones `exports` para CSV.

## [Sin versionar] - 2026-05-22 (cierre UX operativo)

### Agregado

- Migraciones Filament para exportacion (`exports`, `imports`, `failed_import_rows`).
- Exportacion CSV en listados de movimientos de stock, saldos de componentes y productos.
- Formateador de referencias legibles en movimientos (`StockMovementReferenceFormatter`).
- Tests unitarios de referencias de movimiento.

### Cambiado

- Onboarding de admin tenant solo en alta de tenant; edicion usa pestaña **Usuarios del tenant**.
- Indicador **Sin usuarios** en listado de tenants.
- Accion **Consumir stock** con vista previa de composicion por unidades.
- Mensajes operativos para integracion unica por tenant (formulario y listado).
- Documentacion orientada a operacion actual (README, UAT, MVP spec).

## [Sin versionar] - 2026-05-18 (flujo tenant default)

### Agregado

- Checklist UAT local: `docs/UAT_LOCAL_MULTITENANT.md`.
- Tests de resolucion de tenant (`TenantResolutionTest`).

### Cambiado

- Boton **Crear** habilitado en listados de Tenants y Entradas de stock.
- `Tenant::resolveCurrentTenantId()` y `resolveDefaultTenantId()` con fallback estable a tenant `default` activo.

### Documentacion

- Secuencia de actualizacion produccion single-tenant → multi-tenant en `docs/DEPLOY_SHARED_HOSTING.md`.
- Referencias en `README.md`, `docs/INDEX.md` y `docs/CONTEXT.md`.

## [Sin versionar] - 2026-05-18

### Agregado

- **Multi-tenant hardening**: aislamiento por `tenant_id` en resources de negocio (`components`, `component_types`, `products`, `sync_logs`) y validaciones de pertenencia tenant en composición de productos y stock.
- **Gestión de tenants**: nuevo `TenantResource` superadmin-only con flujo de onboarding básico de admin tenant.
- **Stock operativo en panel**: nuevos recursos de `stock_entries`, `stock_movements` y `component_stocks`; alta de entradas conectada a `StockService`.
- **Consumo de stock desde producto**: acción en productos para consumo proporcional de componentes con bloqueo por stock insuficiente.
- **Recalculo automático robusto**: `RecalculateProductsForComponentJob` con retries, backoff y logs de inicio/fin/error por tenant.
- **Cobertura de pruebas**: nuevos tests de aislamiento tenant, guardas superadmin, acceso a panel y validación tenant en stock/composición.

### Cambiado

- `User::canAccessPanel()` deja de permitir acceso universal y ahora valida rol superadmin o tenant activo.
- Integraciones e-factura quedan administrables por superadmin para múltiples tenants en un único panel.
- Índices únicos de códigos pasan a ser compuestos por tenant (`components`, `products`, `component_types`).

### Documentación

- `README.md`, `docs/CONTEXT.md` y `docs/PLAN_MAESTRO_IMPLEMENTACION.md` actualizados a estado real post-implementación.
- Se agrega runbook operativo de colas para hosting compartido.

## [Sin versionar] - 2026-05-15

### Agregado

- **Catálogo `component_types`**: migración + modelo + recurso Filament «Tipos de componente»; `components.component_type_id` sustituye al campo fijo `type`; datos previos se migran a los códigos `metal`, `gem`, `labor`, `other`.
- **Tests**: `ComponentTypeCatalogTest`, helper `metalComponentTypeId()` en `TestCase`.
- **Documentación**: README, `CONTEXT`, `ARTICULOS_MVP_SPEC`, `ARTICULOS_FUNCIONALIDADES_CLIENTE`, `INDEX` y `DEPLOY_SHARED_HOSTING` alineados a productos compuestos multi-rubro.

### Documentación

- Limpieza de documentación de plantilla y placeholders para dejar el repo con documentación de dominio real.
- `docs/ARTICULOS_MVP_SPEC.md`: corrección de modelo de datos (`component_type_id` + bloque de `component_types`).
- `README.md` y `docs/INDEX.md`: reescritos para reflejar alcance funcional actual y enlaces vigentes.
- Alineacion de nombre oficial del sistema a **Articulos compuestos** en README y documentos funcionales.
- Se documenta roadmap acordado: stock por componentes, multitenant con `tenant_id`, y opcion de recalculo automatico por jobs.
- `docs/PLAN_MAESTRO_IMPLEMENTACION.md`: nuevo documento unico para estado real, backlog y criterios de cierre.
- Alineacion cruzada de `README`, `CONTEXT`, `ARTICULOS_MVP_SPEC` e `INDEX` para eliminar contradicciones (superadmin/e-factura y estado de implementacion).

### Configurado
- Idioma por defecto: Español (es)
- Laravel 12.34.0
- Tailwind CSS v4
- Optimizado para hosting compartido

---

## Formato

Los tipos de cambios son:
- `Agregado` - Nueva funcionalidad
- `Cambiado` - Cambios en funcionalidad existente
- `Deprecado` - Funcionalidad que se eliminará pronto
- `Eliminado` - Funcionalidad eliminada
- `Corregido` - Corrección de bugs
- `Seguridad` - Vulnerabilidades corregidas

---

**Nota**: Las versiones se agregarán cuando se haga el primer release del proyecto.

