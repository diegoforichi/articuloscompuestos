# Reglas de Despliegue

## 🚀 PROCESO DE DESPLIEGUE

### Desarrollo Local:
1. Instalar dependencias localmente: `composer install`
2. Compilar assets localmente: `npm run build`
3. Probar todo localmente
4. **NO ejecutar** composer o npm en servidor

### Preparación para Producción:
```bash
# 1. Instalar dependencias PHP (local)
composer install --optimize-autoloader --no-dev

# 2. Compilar assets (local)
npm run build

# 3. Optimizar Laravel (local)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Comprimir todo el proyecto
tar -czf laravel-app.tar.gz \
  --exclude=node_modules \
  --exclude=.git \
  --exclude=storage/logs/*.log \
  --exclude=.env.local \
  app/ bootstrap/ config/ database/ public/ resources/ routes/ storage/ vendor/ .env artisan composer.json composer.lock
```

### Subir al Servidor:
1. Subir archivo comprimido
2. Descomprimir en directorio del servidor
3. Configurar permisos: `chmod -R 755 storage/ bootstrap/cache/`
4. Configurar .env de producción
5. Ejecutar migraciones si es necesario: `php artisan migrate --force`

---

## 📦 DIRECTORIOS COMPLETOS A SUBIR

### CRÍTICO - SUBIR COMPLETOS:
- **vendor/** - Todas las dependencias PHP (no ejecutar composer en servidor)
- **public/build/** - Assets compilados (no ejecutar npm en servidor)
- **storage/** - Con permisos 755
- **bootstrap/cache/** - Con permisos 755

### Por qué subir vendor/ completo:
1. **Sin composer en servidor**: Hosting compartido no tiene composer
2. **Sin instalaciones**: No depender de instalaciones externas
3. **Reproducibilidad**: Mismo código en local y producción
4. **Sin sorpresas**: No problemas de versiones o dependencias

### Por qué compilar assets localmente:
1. **Sin Node.js en servidor**: Hosting compartido no tiene Node.js
2. **Sin npm en servidor**: No disponible en producción
3. **Sin compilación en servidor**: Recursos limitados
4. **Assets listos**: Todo compilado y optimizado

---

## 🔧 CONFIGURACIÓN DE PRODUCCIÓN

### Archivo .env de Producción:
```env
# Aplicación
APP_NAME="Panel de Facturas"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com/webapp

# Base de datos (OBLIGATORIO: MySQL)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=panel_facturas
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

# Cache y sesiones (file para shared hosting)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Límites del servidor
MEMORY_LIMIT=256M
MAX_EXECUTION_TIME=30
```

---

## ⚠️ RESTRICCIONES DEL SERVIDOR

### NO ejecutar en servidor:
- ❌ `composer install` - Subir vendor/ completo
- ❌ `composer update` - Actualizar en local
- ❌ `npm install` - No disponible
- ❌ `npm run build` - Compilar en local
- ❌ Instalaciones de dependencias
- ❌ Compilación de assets

### SÍ ejecutar en servidor (si es necesario):
- ✅ `php artisan migrate --force` - Solo si hay nuevas migraciones
- ✅ `php artisan config:cache` - Optimizar configuración
- ✅ `php artisan route:cache` - Optimizar rutas
- ✅ `php artisan view:cache` - Optimizar vistas
- ✅ `chmod` para permisos de directorios

---

## 📋 CHECKLIST DE DESPLIEGUE

### Antes de subir:
- [ ] Dependencias instaladas localmente
- [ ] Assets compilados localmente
- [ ] Laravel optimizado localmente
- [ ] Base de datos creada en servidor (phpMyAdmin)
- [ ] .env de producción configurado
- [ ] Todo comprimido y listo para subir

### Después de subir:
- [ ] Archivos descomprimidos
- [ ] Permisos configurados (storage/, bootstrap/cache/)
- [ ] .env de producción en su lugar
- [ ] Migraciones ejecutadas (si es necesario)
- [ ] Cache optimizado
- [ ] Aplicación funcionando
- [ ] SSL verificado

---

## 🎯 RESUMEN

**Estrategia de despliegue:**
1. **TODO en local**: Instalar, compilar, optimizar
2. **Subir completo**: vendor/, assets compilados, todo listo
3. **Sin instalaciones en servidor**: No composer, no npm
4. **Auto-contenido**: Proyecto funciona sin dependencias externas
5. **Reproducible**: Mismo código en local y producción

