# Checklist UAT local (multi-tenant)

Usar este checklist en local antes de subir a hosting. URL base: `http://127.0.0.1:8000/admin`.

## Usuarios de prueba sugeridos

| Rol | Email | Contexto |
|-----|-------|----------|
| Superadmin | `superadmin@compuestos.local` | Gestiona tenants e integraciones; datos operativos en tenant `default` |
| Tenant demo | `demo@compuestos.local` | Solo tenant asignado (historico / demo) |

## 1. Acceso y roles

- [ ] Superadmin entra y ve menu **Tenants** e **Integraciones**
- [ ] Tenant admin entra y **no** ve Tenants ni Integraciones
- [ ] Usuario de tenant inactivo no entra al panel

## 2. Tenants

- [ ] En **Tenants** aparece boton **Crear**
- [ ] Crear tenant nuevo (nombre + slug unico)
- [ ] Completar onboarding admin (nombre, email, password) o dejar vacio y crear usuario despues
- [ ] Editar tenant: **no** aparece seccion de onboarding; usuarios solo en **Usuarios del tenant**
- [ ] Listado marca **Sin usuarios** en rojo cuando `Usuarios = 0`
- [ ] Editar tenant y validar `recalculation_mode` (`manual` / `automatic`)

## 3. Integracion e-factura

- [ ] Superadmin crea integracion para tenant nuevo (campo **Tenant** obligatorio)
- [ ] Solo una integracion por tenant (boton **Crear** oculto si no hay tenants libres)
- [ ] Intento de duplicar tenant en integracion muestra error claro
- [ ] Sync de producto deja registro en **Logs de sincronizacion** del tenant correcto

## 4. Aislamiento de datos

- [ ] Con usuario tenant A solo se ven productos/componentes de A
- [ ] Con usuario tenant B solo se ven productos/componentes de B
- [ ] Superadmin sin `tenant_id` opera sobre tenant `default` (slug `default`)

## 5. Stock

- [ ] En **Entradas de stock** aparece boton **Crear**
- [ ] Alta de entrada actualiza **Movimientos** y **Saldos**
- [ ] Movimientos muestran referencia legible (`Entrada #ID`, `Consumo producto <codigo>`)
- [ ] Consumo desde producto muestra vista previa por composicion
- [ ] Consumo desde producto bloquea stock negativo
- [ ] Exportar CSV en **Movimientos**, **Saldos** y **Productos** descarga archivo operativo

## 6. Recalculo

- [ ] Tenant en `manual`: cambio de precio de componente no dispara cola
- [ ] Tenant en `automatic`: cambio de precio dispara recalculo de productos afectados

## 7. Cierre local

- [ ] `php artisan test` en verde
- [ ] Sin errores relevantes en `storage/logs/laravel.log`
- [ ] Backup/export de base local listo antes de deploy

## Consulta rapida de base (opcional)

```bash
php artisan tinker
```

```php
\App\Models\Tenant::all(['id','name','slug','is_active']);
\App\Models\User::all(['email','tenant_id','is_super_admin']);
\App\Models\IntegrationSetting::all(['tenant_id','rut_emisor','is_active']);
```
