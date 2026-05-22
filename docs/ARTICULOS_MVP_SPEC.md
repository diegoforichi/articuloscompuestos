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

- `component_types`
- `components`
- `component_price_histories`
- `products`
- `product_components`
- `product_price_histories`
- `integration_settings`
- `sync_logs`

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

## Recalculo de precios

- Modo manual (actual).
- Modo automatico por tenant (base tecnica iniciada).
- En automatico, al cambiar costo de componente se encola job de recálculo.

## Criterio de implementacion

- Simplicidad y mantenibilidad.
- Laravel nativo + Filament.
- Compatibilidad con hosting compartido.
- Trazabilidad de cambios (historial, logs, movimientos).
