# Especificacion base - Articulos compuestos

## Proposito

Definir el comportamiento base de la app para gestionar productos compuestos, calcular precios y sincronizar el producto final con e-factura.

## Objetivo funcional

La app permite:

- administrar tipos de componente (`component_types`);
- administrar componentes (codigo, tipo, unidad, moneda, precio);
- armar productos por composicion de componentes;
- calcular costo y precio de venta;
- guardar historial de precios;
- sincronizar productos a e-factura y actualizar precio remoto.

## Reglas de negocio vigentes

- Un producto compuesto debe tener al menos 2 componentes.
- Producto y componentes deben compartir moneda (`UYU` o `USD`).
- `price_minor` es el valor enviado a e-factura.
- `cost_minor` es referencia interna para margen/utilidad.
- Si un producto sincronizado cambia precio, pasa a `dirty`.
- Estados de sync: `draft`, `synced`, `dirty`, `error`.

## Integracion e-factura

- Seguridad base:
  - `Authorization: Bearer <token>`
  - `RUTEmisor: <rut>`
- Seguridad adicional opcional:
  - `Auth`
  - `Origin`
- Alta: `POST /api/extsys/addArticle`
- Actualizacion: `PUT /api/extsys/updateArticle`
- Fuente de verdad: local (no hay GET remoto habilitado por proveedor).

### Trazabilidad del contrato demo

- Referencia historica del contrato literal del proveedor: [Integracion inicial Zafiro/efactura](d239c990-2ac7-4f65-b197-38e1596e78d7).
- En documentacion versionada no se guardan tokens reales; usar placeholders en nuevos documentos.
- Diferencias proveedor vs comportamiento validado (payload update, headers opcionales) se mantienen en:
  - `docs/PLAN_MAESTRO_IMPLEMENTACION.md`.

## Modelo de datos vigente

### Catalogo y precios

- `component_types`
- `components`
- `component_price_histories`
- `products`
- `product_components`
- `product_price_histories`

### Multi-tenant y acceso

- `tenants` (`recalculation_mode`: `manual` | `automatic`)
- `users` (`tenant_id`, `is_super_admin`)

### Integracion y auditoria

- `integration_settings` (una fila activa por `tenant_id`, indice unique en BD)
- `sync_logs`

### Stock

- `stock_entries`, `stock_entry_items`
- `stock_movements` (`reference_type`, `reference_id`, `notes`)
- `component_stocks`

### Infraestructura Filament (exportacion CSV)

- `exports`, `imports`, `failed_import_rows` (requieren `php artisan migrate`)

## Stock (operativo)

- Entradas: `stock_entries` + `stock_entry_items` (alta manual en panel).
- Movimientos: `stock_movements` (`IN` por entrada, `OUT` por consumo de producto).
- Saldos: `component_stocks` (materializado).
- Consumo: accion **Consumir stock** en producto; cantidad por unidad = composicion × unidades.
- Referencias en UI: `Entrada #<id>` o `Consumo producto <codigo>`.
- Regla: sin stock negativo.

## Multi-tenant (operativo)

- Tabla `tenants`; datos de negocio con `tenant_id`.
- Superadmin: tenants, integraciones (una por tenant), acceso global con contexto `default`.
- Usuarios tenant: alta en create (onboarding opcional) o en **Usuarios del tenant** al editar.
- `User::canAccessPanel()`: superadmin o usuario con tenant activo.

## Recalculo de precios (operativo)

- Cada tenant define `recalculation_mode` en **Tenants**:
  - **manual**: cambio de precio de componente no encola recalculo; el usuario recalcula productos desde el panel.
  - **automatic**: al actualizar precio de un componente se encola `RecalculateProductsForComponentJob` (cola `default`).
- Recalculo manual por producto o en lote sigue disponible en todo momento.
- Requiere worker/cron de cola en hosting para el modo automatico (ver `README.md`).

## Exportacion y UX operativa

- CSV en listados: **Movimientos de stock**, **Saldos de componentes**, **Productos** (Filament `ExportAction`, solo CSV).
- Referencias legibles en movimientos: `Entrada #<id>`, `Consumo producto <codigo>` (ref. tecnica opcional en columnas ocultas).
- Integracion: mensajes claros si el tenant ya tiene configuracion; boton **Crear** oculto cuando no hay tenants libres.

## Criterio de implementacion

- Simplicidad y mantenibilidad.
- Laravel nativo + Filament.
- Compatibilidad con hosting compartido.
- Trazabilidad de cambios (historial, logs, movimientos).
