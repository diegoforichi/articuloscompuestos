# Plan maestro de implementacion y pendientes

## Objetivo

Concentrar en un solo documento el estado real del sistema, lo implementado hoy, lo pendiente para produccion y el criterio de cierre por fase.

## Estado actual confirmado (2026-05-22)

### Dominio y operacion

- Productos compuestos por componentes; costo y precio por composicion.
- Sync del producto final con e-factura; historial y estados `draft`, `synced`, `dirty`, `error`.
- Multi-tenant con aislamiento por `tenant_id` en recursos de negocio.
- Una integracion e-factura por tenant (formulario, UI y unique en BD).
- Stock: entradas, movimientos, saldos, consumo desde producto sin stock negativo.
- Recalculo manual y automatico por tenant (job en cola).
- Exportacion CSV en movimientos, saldos y productos.
- Tenants: onboarding admin solo en alta; usuarios en **Usuarios del tenant** al editar; badge **Sin usuarios**.

### Referencia operativa

- Checklist local: `docs/UAT_LOCAL_MULTITENANT.md`
- Deploy: `docs/DEPLOY_SHARED_HOSTING.md`
- Resumen ejecutivo: `README.md`

## Contrato e-factura: literal y validado

### Fuente historica (literal proveedor)

Referencia de chat con texto original del administrador: [Integracion inicial Zafiro/efactura](d239c990-2ac7-4f65-b197-38e1596e78d7).

Resumen literal recuperado:

- URL base demo: `https://abelen56-002-site2.gtempurl.com`
- Seguridad: `Authorization: Bearer + Token`, `RUTEmisor: rucemisor`
- Endpoints: `POST /api/extsys/addArticle`, `PUT /api/extsys/updateArticle`

Nota: en documentacion versionada no se replican tokens reales.

### Comportamiento implementado

- `Authorization` + `RUTEmisor`; `Auth` y `Origin` opcionales.
- `addArticle` y `updateArticle` en `EFacturaService`.
- Update con payload completo (no solo `{id, price}`) por robustez observada en pruebas.

## Matriz de diferencias: proveedor vs sistema

| Tema | Literal proveedor | Estado validado en sistema |
|---|---|---|
| Seguridad base | Authorization + RUTEmisor | Igual, mas `Auth` y `Origin` opcionales |
| Alta | `addArticle` | Implementado |
| Update | minimo `{id, price}` reportado | Payload completo en implementacion |
| GET remoto | no confirmado | Fuente de verdad local |

## Decisiones funcionales vigentes

- Integracion e-factura: solo superadmin; una configuracion por `tenant_id`.
- Panel operativo: `/admin` (Filament).
- Multi-tenant: `tenant_id` en tablas de negocio.
- Recalculo: `manual` o `automatic` por tenant (ambos operativos).
- Superadmin sin `tenant_id`: contexto de datos en tenant `default` (`Tenant::DEFAULT_SLUG`).

## Fases de implementacion (cerradas)

| Fase | Resultado |
|------|-----------|
| 1 Multi-tenant | Aislamiento por tenant y guardas por rol |
| 2 Tenants UI | `TenantResource`, onboarding en create, usuarios en relation manager |
| 3 Stock panel | Entradas, movimientos, saldos, consumo desde productos |
| 4 Recalculo y cola | Job con retries, logs, modo automatico por tenant |
| 5 Testing | Suite PHPUnit + Pint en verde |
| 6 Documentacion base | README, CONTEXT, MVP spec, INDEX, DEPLOY, UAT |
| 7 Cierre UX operativo | Referencias legibles, CSV, integracion unica con mensajes, docs alineados |

## Pendiente para produccion (no es deuda de codigo core)

- [ ] Ejecutar `docs/UAT_LOCAL_MULTITENANT.md` en MySQL de staging/produccion.
- [ ] Cron/worker de cola en hosting (`queue:work --stop-when-empty`).
- [ ] `php artisan migrate --force` en servidor (incluye tablas `exports` para CSV).
- [ ] Credenciales e-factura reales por tenant.
- [ ] Flujo de sincronizacion mas recuperable ante errores (reintentos UX, guia operador).
- [ ] Guia breve por rol para usuario final (superadmin vs admin tenant).

## Riesgos

- Contrato e-factura puede cambiar sin versionado formal.
- Hosting compartido: cola y timeouts limitan recalculo automatico masivo.
- Sin worker, el modo `automatic` no recalcula hasta procesar la cola.

## Criterios de aceptacion documental

- Sin contradicciones entre `README`, `CONTEXT`, `ARTICULOS_MVP_SPEC`, `INDEX`, `UAT`, `DEPLOY`.
- Este documento distingue: implementado hoy vs pendiente operativo.
- Contrato proveedor separado de comportamiento validado en codigo.
