# Plan maestro de implementacion y pendientes

## Objetivo

Concentrar en un solo documento el estado real del sistema, lo que falta implementar y el criterio de cierre por fase para evitar contradicciones entre documentos.

## Estado actual confirmado

### Dominio y operacion actual

- La app administra productos compuestos por componentes.
- Se calcula costo y precio de venta por composicion.
- Se sincroniza el producto final con e-factura.
- Existe historial de precios y estado de sincronizacion (`draft`, `synced`, `dirty`, `error`).

### Base tecnica implementada recientemente

- Base multi-tenant cerrada para prueba:
  - tabla `tenants`;
  - `tenant_id` agregado en tablas de negocio principales;
  - `is_super_admin` en `users`;
  - acceso de panel bloqueado para tenant inactivo o usuario sin tenant (excepto superadmin);
  - aislamiento de consultas por tenant en recursos de negocio.
- Base de stock por componentes operativa en panel:
  - `stock_entries`,
  - `stock_entry_items`,
  - `stock_movements`,
  - `component_stocks`,
  - consumo desde productos con bloqueo de stock insuficiente.
- Recalculo automatico endurecido:
  - `RecalculateProductsForComponentJob`;
  - disparo por cambio de precio de componente segun modo tenant;
  - retries, backoff y logs por tenant/componente.
- Restriccion superadmin consistente:
  - recurso de Integraciones visible para superadmin;
  - recurso de Tenants visible para superadmin.
- Flujo operativo estabilizado:
  - botones **Crear** en listados de Tenants y Entradas de stock;
  - resolucion de tenant con fallback a `default` activo;
  - checklist UAT local en `docs/UAT_LOCAL_MULTITENANT.md`;
  - guia de actualizacion single-tenant → multi-tenant en `docs/DEPLOY_SHARED_HOSTING.md`.

## Contrato e-factura: literal y validado

### Fuente historica (literal proveedor)

Referencia de chat con texto original del administrador: [Integracion inicial Zafiro/efactura](d239c990-2ac7-4f65-b197-38e1596e78d7).

Resumen literal recuperado:

- URL base demo: `https://abelen56-002-site2.gtempurl.com`
- Seguridad:
  - `Authorization: Bearer + Token`
  - `RUTEmisor: rucemisor`
- Endpoints informados:
  - `POST /api/extsys/addArticle`
  - `PUT /api/extsys/updateArticle`

Nota: por politica de seguridad, en documentacion versionada no se replican tokens reales. Los valores sensibles quedan solo en historial interno y entornos locales controlados.

### Comportamiento implementado/observado

- La app usa:
  - `Authorization` + `RUTEmisor`;
  - `Auth` y `Origin` opcionales para ambientes que lo requieren.
- `addArticle` y `updateArticle` estan implementados en `EFacturaService`.
- En pruebas previas se observaron diferencias entre payload de update "minimo" y payload efectivo requerido por la API.

## Matriz de diferencias: proveedor vs sistema

| Tema | Literal proveedor | Estado validado en sistema |
|---|---|---|
| Seguridad base | Authorization + RUTEmisor | Igual, mas `Auth` y `Origin` opcionales |
| Alta | `addArticle` | Implementado |
| Update | se reporto minimo `{id, price}` | Implementacion envia payload completo para robustez |
| GET remoto | no confirmado | sistema asume fuente de verdad local |

## Decisiones funcionales vigentes

- La integracion e-factura se configura solo por superadmin.
- El panel operativo principal sigue en `/admin`.
- Multi-tenant es estrategia base compartida con `tenant_id`.
- Recalculo puede evolucionar a manual/automatico por tenant.

## Backlog unico priorizado (actualizado)

### Fase 1 - Endurecimiento multi-tenant

- Estado: completada.
- Resultado: aislamiento por tenant y guardas por rol aplicados en recursos de negocio.

### Fase 2 - Gestion de tenants en UI

- Estado: completada.
- Resultado: `TenantResource` con CRUD y onboarding basico de admin tenant.

### Fase 3 - Stock operativo en panel

- Estado: completada.
- Resultado: recursos de entradas, movimientos y saldos + consumo desde productos.

### Fase 4 - Recalculo automatico y cola

- Estado: completada.
- Resultado: job endurecido con logs y retry controlado.

### Fase 5 - Testing gate

- Estado: completada.
- Resultado: pruebas unitarias/feature ejecutadas + suite completa en verde + Pint aplicado.

### Fase 6 - Cierre documental

- Estado: completada.
- Resultado: README, CONTEXT, PLAN_MAESTRO y CHANGELOG alineados.

## Riesgos y bloqueos

- Contrato externo e-factura puede cambiar sin versionado formal.
- Si no se completa aislamiento tenant en toda la UI, hay riesgo de mezcla de datos.
- En hosting compartido, jobs y tiempos de respuesta requieren estrategia controlada.

## Criterios de aceptacion documental

- No hay contradicciones entre `README`, `CONTEXT`, `ARTICULOS_MVP_SPEC`, `INDEX`.
- Este documento contiene el backlog completo y priorizado.
- Se separa claramente:
  - lo literal del proveedor,
  - lo validado por implementacion/pruebas.
- Queda explicito que esta implementado hoy y que falta.

## Checklist de cierre de fase

### Cierre Fase 1

- [x] Docs alineados y fechados.
- [x] Validacion funcional basica ejecutada.
- [x] Changelog actualizado.

### Cierre Fase 2

- [x] Tenants y stock con UI operativa.
- [x] Reglas tenant aplicadas en todo el panel.
- [x] No hay stock negativo por operaciones normales.

### Cierre Fase 3

- [x] Jobs estables y monitoreados.
- [ ] Flujo de sincronizacion recuperable ante errores.
- [ ] Checklist de deploy y post-deploy validado.
