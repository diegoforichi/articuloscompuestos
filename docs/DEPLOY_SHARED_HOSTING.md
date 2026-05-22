# Despliegue en hosting compartido (cPanel)

Este proyecto puede vivir **en una sola carpeta** bajo el document root del subdominio (por ejemplo `public_html/website_xxx/`). El archivo `.htaccess` en la raíz del proyecto redirige el tráfico a `public/`.

## Requisitos en el servidor

- PHP 8.2+ con extensiones: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `intl`, `fileinfo`, `zip`
- MySQL (base y usuario creados en cPanel → MySQL® Databases)
- Terminal SSH o **Terminal** de cPanel (para Artisan)

## Archivo ZIP

En Windows, desde la raíz del proyecto:

```powershell
.\scripts\build-deploy-zip.ps1
```

Se genera `zafirosCalc-deploy.zip` en la raíz del proyecto (sin `node_modules`, `.git`, `tests`, `.cursor` ni `.env`). Si el archivo anterior está bloqueado por el Explorador de Windows, cerrá la carpeta o renombrá el zip viejo y ejecutá de nuevo.

Si ya generaste un paquete completo manualmente, puede existir `zafirosCalc-deploy-FULL.zip` (~20 MB con `vendor`); el script normal produce el mismo tipo de contenido.

Tras `php artisan migrate` en el servidor, la base incluye tablas de exportacion Filament (`exports`, etc.) necesarias para **Exportar CSV** en listados de stock y productos, ademas de **tipos de componente iniciales** (códigos `metal`, `gem`, `labor`, `other`) para compatibilidad con despliegues anteriores. Podés **crear y ordenar más tipos** desde el panel Filament (**Tipos de componente**) para adaptar el vocabulario a cada negocio (harinas, empaque, mano de obra, etc.).

## Pasos en cPanel

1. **Subir** el ZIP al directorio del subdominio y **extraer** ahí (debe quedar `app/`, `public/`, `vendor/`, `.htaccess`, etc. en la misma carpeta que apunta el subdominio).

2. **Crear `.env`** en la raíz del proyecto (junto a `artisan`):
   - Copiá `deploy-env-template.env` del ZIP y renombrá a `.env` (o copiá el contenido manualmente).
   - Completá `APP_KEY`, credenciales MySQL y revisá `APP_URL` (URL pública del subdominio, con `https://`).

3. **Generar clave de aplicación** (Terminal, estando en la carpeta del proyecto):

   ```bash
   php artisan key:generate
   ```

4. **Permisos** (si algo falla al escribir logs o caché):

   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

5. **Enlace de storage** (opcional si usás archivos públicos):

   ```bash
   php artisan storage:link
   ```

6. **Migraciones**:

   ```bash
   php artisan migrate --force
   ```

7. **Caché de producción**:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

8. **Usuario administrador Filament** (si aún no existe):

   ```bash
   php artisan make:filament-user
   ```

9. Abrí en el navegador: `https://tu-subdominio/admin`

10. **Integración e-factura (producción):** en Filament → **Integraciones**, cargá **URL base**, **token**, **RUT emisor**, categoría y demás campos por tenant. Si el proveedor exige el contrato extendido, completá también **Auth (seguridad adicional)** y **Origin (URL origen)**. Guardá como configuración **activa** y probá un alta de prueba; revisá `sync_logs`.  
   Esta configuración queda administrada solo por usuarios superadmin.

## Actualizacion desde version single-tenant (produccion existente)

Si hoy la app entra directo a un solo cliente/tenant, seguir este orden:

1. **Backup completo de base de datos** (obligatorio).
2. Subir codigo nuevo (incluye `vendor/` y `public/build/` compilados en local).
3. Ejecutar migraciones:

   ```bash
   php artisan migrate --force
   ```

4. Verificar tenant base:

   ```sql
   SELECT id, name, slug, is_active FROM tenants;
   ```

   Debe existir un tenant con `slug = default` y datos historicos con `tenant_id` asignado.

5. Crear o promover superadmin:

   ```bash
   php artisan make:filament-user
   ```

   ```sql
   UPDATE users SET is_super_admin = 1, tenant_id = NULL WHERE email = 'superadmin@tu-dominio.com';
   ```

6. Mantener usuario operativo del cliente en tenant `default` (`is_super_admin = 0`, `tenant_id` del default).

7. Smoke test por tenant:
   - login superadmin: alta de tenant + integracion
   - login admin tenant: productos/componentes/stock solo de su tenant
   - sync e-factura y revision de `sync_logs`

8. Cache de produccion:

   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

Checklist detallado de pruebas locales: `docs/UAT_LOCAL_MULTITENANT.md`.

## Filament, sesión y panel (hosting compartido)

En este proyecto la configuración del panel **está alineada con lo que probamos en local y en el subdominio demo** (cPanel). No es la única combinación válida en Filament a nivel global, pero **cambiarla sin volver a probar en ambos entornos** suele reproducir fallos difíciles de ver en logs (sesión / Livewire / login).

### Archivos involucrados

- `app/Providers/Filament/AdminPanelProvider.php` — registro del panel `admin` y **middleware del panel** (`EncryptCookies`, `StartSession`, `VerifyCsrfToken`, etc.) y `authMiddleware` (`Authenticate::class`).
- `app/Models/User.php` — el usuario implementa `FilamentUser` y define `canAccessPanel()`.

### Comportamiento elegido (no cambiar “por moda”)

1. **`AdminPanelProvider`**: los arrays `->middleware([...])` y `->authMiddleware([...])` **sin** el flag `isPersistent: true` de Filament y **sin** `->persistentMiddleware(['panel'])`.  
   En el demo, una variante con middleware persistente y/o ajustes extra del stack **dejó de funcionar** el acceso estable al panel (síntomas tipo **419 Page Expired**, pérdida de sesión en peticiones Livewire, o login que no “pega”).

2. **`User::canAccessPanel()`**: devuelve `true` para cualquier usuario autenticado.  
   Simplifica el acceso al panel cuando solo hay **cuentas de administración de confianza** (demo o equipo interno).

### Seguridad

Si en el futuro hay **varios usuarios** en la tabla `users` y no todos deben administrar Articulos compuestos, **no dejes** `canAccessPanel()` en `true` indefinidamente: restringi por panel (`$panel->getId() === 'admin'`), por rol, por email de dominio, etc., y proba de nuevo en hosting.

### Antes de tocar Filament o el login

1. Probar **login + una acción Livewire** (guardar un recurso) en local.  
2. Repetir en **HTTPS** del subdominio real (`APP_URL` correcto).  
3. Si algo falla, revisar `storage/logs/laravel.log` y la pestaña **Red** del navegador (código de la petición a `/livewire/update`).

## Notas

- `DB_HOST` en cPanel suele ser `localhost` o `127.0.0.1` (el panel de MySQL indica el host correcto).
- No subas el `.env` del entorno local; usá solo el template en el servidor.
- Tras cambiar `.env` en producción, ejecutá de nuevo `php artisan config:cache`.
