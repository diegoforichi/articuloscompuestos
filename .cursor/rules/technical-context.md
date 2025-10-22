# Contexto Técnico del Proyecto

## 📊 INFORMACIÓN DEL SERVIDOR

### Hosting Compartido:
- **Proveedor**: JustPro
- **Servidor**: just2059
- **IP**: 173.254.29.47

### Software del Servidor:
- **Sistema Operativo**: Linux (Kernel 4.19.286-203.ELK.el7.x86_64)
- **Arquitectura**: x86_64
- **cPanel**: 110.0 (build 77)
- **Apache**: 2.4.59
- **PHP**: 8.2.12
- **MySQL**: 5.7.23-23
- **Perl**: 5.16.3

### Recursos del Servidor:
- **CPUs**: 20 cores
- **Memoria**: 37.08% usado
- **Swap**: 14.24% usado
- **Load**: 0.04

---

## 🚨 LIMITACIONES IMPORTANTES

### SQLite:
- ⚠️ **VERSIÓN ANTIGUA** instalada en servidor
- ❌ **NO usar en producción**
- ✅ **Solo para desarrollo local**
- 🔧 **Alternativa**: MySQL 5.7.23

### MySQL:
- ✅ **Versión**: 5.7.23-23
- ✅ **Disponible** vía phpMyAdmin
- ✅ **Usar en producción**
- 📝 **Crear bases de datos** manualmente

### Node.js:
- ❌ **NO disponible** en servidor
- ❌ **NO ejecutar** npm en producción
- ✅ **Compilar localmente** y subir assets

### Composer:
- ⚠️ **Limitado** en servidor compartido
- ❌ **NO ejecutar** composer install en servidor
- ✅ **Subir vendor/** completo

---

## 🛠️ STACK TECNOLÓGICO

### Backend:
- **Laravel**: 12.34.0
- **PHP**: 8.2.12
- **MySQL**: 5.7.23
- **Eloquent ORM**: Nativo de Laravel

### Frontend:
- **Tailwind CSS**: v4.0.0
- **Blade Templates**: Nativo de Laravel
- **Alpine.js**: Opcional, solo si es necesario
- **Filament**: Panel administrativo

### Herramientas de Desarrollo:
- **Laravel Boost**: v1.4 (MCP para desarrollo)
- **Laravel Pint**: v1.24 (formateo de código)
- **Laravel Sail**: v1.41 (solo desarrollo local)
- **PHPUnit**: v11.5.3 (testing)
- **Vite**: v7.0.7 (bundling)

---

## 📁 ESTRUCTURA DEL PROYECTO

```
panel-facturas/
├── app/
│   ├── Http/Controllers/
│   │   ├── FacturaController.php
│   │   ├── ClienteController.php
│   │   └── DashboardController.php
│   ├── Models/
│   │   ├── Factura.php
│   │   ├── Cliente.php
│   │   └── User.php
│   └── Services/
│       └── FacturaService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   └── api.php
├── vendor/                  # Subir completo
├── public/
│   └── build/              # Assets compilados
├── storage/                # Permisos 755
├── bootstrap/cache/        # Permisos 755
├── .env                    # Producción
├── .env.example           # Template
├── artisan
├── composer.json
└── composer.lock
```

---

## 🗄️ ESTRUCTURA DE BASE DE DATOS

### Tablas Principales:
- **users**: Usuarios del sistema
- **clientes**: Clientes de facturas
- **facturas**: Facturas emitidas
- **productos**: Productos/servicios
- **sessions**: Sesiones de usuarios
- **cache**: Cache de aplicación

### Convenciones:
- **IDs**: Auto-incrementales
- **Timestamps**: created_at, updated_at
- **Soft Deletes**: deleted_at (opcional)
- **Relaciones**: Usar Eloquent

---

## 🔧 CONFIGURACIÓN DE ENTORNOS

### Desarrollo Local:
```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql  # o sqlite para pruebas rápidas
DB_HOST=localhost
DB_DATABASE=panel_facturas_local
```

### Producción:
```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=panel_facturas
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 📦 DEPENDENCIAS

### PHP (Composer):
- laravel/framework: ^12.0
- laravel/tinker: ^2.10.1
- filament/filament: (a instalar)

### JavaScript (NPM):
- @tailwindcss/vite: ^4.0.0
- axios: ^1.11.0
- laravel-vite-plugin: ^2.0.0
- tailwindcss: ^4.0.0
- vite: ^7.0.7

---

## 🎯 SERVICIOS DISPONIBLES

### En el Servidor:
- ✅ cpanellogd
- ✅ cpsrvd
- ✅ ftpd
- ✅ imap
- ✅ named
- ✅ queueprocd
- ✅ spamd

### Rutas de Sistema:
- **Sendmail**: /usr/sbin/sendmail
- **Perl**: /usr/bin/perl

---

## ⚙️ CONFIGURACIONES IMPORTANTES

### PHP.ini (estimado):
```ini
memory_limit = 256M
max_execution_time = 30
upload_max_filesize = 10M
post_max_size = 10M
```

### Apache:
- **Versión**: 2.4.59
- **Módulos**: mod_rewrite (para Laravel)
- **SSL**: Incluido

---

## 🚀 COMANDOS ÚTILES

### En Local:
```bash
composer install
npm install
npm run build
php artisan serve
php artisan migrate
php artisan tinker
```

### En Servidor (vía SSH si disponible):
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 755 storage/ bootstrap/cache/
```

---

## 📋 RECORDATORIOS

1. **SQLite es VIEJO** - NO usar en producción
2. **MySQL 5.7.23** - SÍ usar en producción
3. **Subir vendor/** completo - No ejecutar composer
4. **Compilar assets** localmente - No ejecutar npm
5. **Hosting compartido** - Recursos limitados
6. **PHP 8.2.12** - Versión específica
7. **Auto-contenido** - Sin dependencias externas

