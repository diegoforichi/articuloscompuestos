# Articulos compuestos - Resumen funcional para cliente

## Que hace la aplicacion

Articulos compuestos centraliza la gestion de productos armados con componentes:

- composicion de producto por insumos;
- calculo de costo y precio final;
- historial de cambios;
- envio y actualizacion del producto final en e-factura.

## Funcionalidades actuales

1. Gestion de tipos de componente (catalogo editable).
2. Gestion de componentes (nombre, codigo, tipo, unidad, moneda, precio).
3. Gestion de productos compuestos.
4. Calculo de precios por composicion y utilidad.
5. Historial de precios por componente y producto.
6. Envio de productos a e-factura.
7. Actualizacion de precios en e-factura.
8. Alertas de sincronizacion y errores.

## Beneficios

- Orden y trazabilidad.
- Menos carga manual duplicada.
- Mejor control de costos y margen.
- Flujo consistente entre sistema local y e-factura.

## Evolucion acordada (proxima etapa)

- Stock por componentes (entradas, consumo y saldos).
- Multiempresa con aislamiento por tenant.
- Recálculo configurable por tenant: manual o automatico.

El detalle tecnico de implementado y faltante se mantiene en:

- `docs/PLAN_MAESTRO_IMPLEMENTACION.md`

## Objetivo del lanzamiento

Tener una herramienta simple y confiable para administrar productos compuestos y sincronizarlos con e-factura sin reprocesos manuales.
