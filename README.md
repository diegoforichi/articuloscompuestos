# Articulos compuestos

Aplicacion Laravel + Filament para negocios que venden productos armados por componentes (insumos + mano de obra), con calculo de costos/precio y sincronizacion hacia e-factura.

## Que hace hoy el sistema

Panel `/admin` (Filament) para un negocio multi-tenant:

| Area | Operacion |
|------|-----------|
| Catalogo | Tipos de componente, componentes, productos compuestos, historial de precios |
| Precios | Recalculo manual o automatico por tenant; job en cola para modo automatico |
| Stock | Entradas (IN), movimientos trazables, saldos por componente, consumo desde producto (OUT) |
| Integracion | Una configuracion e-factura por tenant (superadmin); sync de productos y logs |
| Tenants | Alta con onboarding opcional de admin; edicion y usuarios en pestaña **Usuarios del tenant** |
| Exportacion | CSV en listados de movimientos de stock, saldos de componentes y productos |

Roles: **superadmin** (`is_super_admin`, sin `tenant_id`) gestiona tenants e integraciones; opera datos del tenant `default` si no elige otro. **Admin tenant** solo ve su tenant.

## Stack y restricciones

- Laravel 12, PHP 8.2, Filament 4, MySQL en produccion.
- Hosting compartido: no ejecutar Composer/NPM en servidor.
- Deploy por paquete: subir `vendor/` completo y `public/build/` compilado en local.

## Documentacion del proyecto

- `docs/INDEX.md`: mapa rapido de la documentacion vigente.
- `docs/PLAN_MAESTRO_IMPLEMENTACION.md`: fuente unica de estado, backlog y criterios de cierre.
- `docs/CONTEXT.md`: contexto tecnico-operativo del proyecto.
- `docs/ARTICULOS_MVP_SPEC.md`: reglas funcionales y de negocio.
- `docs/ARTICULOS_FUNCIONALIDADES_CLIENTE.md`: explicacion funcional para cliente.
- `docs/DEPLOY_SHARED_HOSTING.md`: procedimiento de despliegue probado.
- `docs/UAT_LOCAL_MULTITENANT.md`: checklist de prueba local antes de hosting.
- `CHANGELOG.md`: historial de cambios reales del proyecto.

## Operacion de cola en hosting compartido

- Comando recomendado:
  - `php artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=120`
- Cron recomendado (sin Supervisor, cada minuto):
  - `* * * * * cd /home/USUARIO/public_html && php artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=120 --stop-when-empty >> /dev/null 2>&1`
- Verificacion de salud:
  - revisar `storage/logs/laravel.log` para eventos `recalculate-products.start|finish|failed`;
  - validar que la cola no acumule jobs pendientes;
  - ejecutar smoke test de cambio de precio en tenant automatico.

## Prueba local antes de hosting

Seguir `docs/UAT_LOCAL_MULTITENANT.md` (roles, alta de tenants, integracion, stock y recalculo).

## Pendiente antes de produccion

- Ejecutar `docs/UAT_LOCAL_MULTITENANT.md` en MySQL de staging/produccion.
- Confirmar cron/worker de cola en hosting (`queue:work --stop-when-empty`).
- Credenciales e-factura reales por tenant (RUT en integracion, no en slug del tenant).

## Instalacion local

```bash
git clone https://github.com/diegoforichi/articuloscompuestos.git
cd articuloscompuestos
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
composer run dev
```

## Testing y calidad

```bash
php artisan test
vendor/bin/pint
```

## Tipos de componente

Luego de `php artisan migrate`, quedan tipos iniciales (`metal`, `gem`, `labor`, `other`) para compatibilidad historica. Desde el panel `Tipos de componente` se pueden crear/editar/activar/desactivar categorias por rubro.

## Repositorio

- GitHub: [diegoforichi/articuloscompuestos](https://github.com/diegoforichi/articuloscompuestos)
