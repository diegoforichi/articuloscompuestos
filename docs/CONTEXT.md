# Contexto del Proyecto - Panel de Facturas Laravel

## 🎯 Resumen Ejecutivo

Este es un proyecto Laravel 12 para gestión de facturas, optimizado para hosting compartido con restricciones específicas.

---

## 📋 Información Rápida

### Tecnologías:
- **Laravel**: 12.34.0
- **PHP**: 8.2.12
- **MySQL**: 5.7.23 (producción)
- **Tailwind CSS**: v4.0.0
- **Filament**: Panel administrativo

### Servidor:
- **Tipo**: Hosting compartido (JustPro)
- **Apache**: 2.4.59
- **Recursos**: Limitados
- **Sin**: Node.js, Docker, Composer global

### Limitaciones Críticas:
- ❌ SQLite VIEJO - NO usar en producción
- ❌ NO Node.js en servidor
- ❌ NO compilar en servidor
- ✅ Subir vendor/ completo
- ✅ Compilar assets localmente

---

## 📁 Reglas del Proyecto

Todas las reglas están en `.cursor/rules/`:

### 1. `project-rules.md`
- Filosofía del proyecto
- Limitaciones del servidor
- Stack tecnológico
- Enfoque de soluciones

### 2. `deployment-rules.md`
- Proceso de despliegue
- Qué subir al servidor
- Configuración de producción

### 3. `technical-context.md`
- Información del servidor
- Estructura del proyecto
- Dependencias

### 4. `code-conventions.md`
- Comentarios obligatorios
- Estructura de código
- Buenas prácticas

---

## 🚀 Flujo de Trabajo

### Desarrollo Local:
```bash
# 1. Instalar dependencias
composer install
npm install

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Base de datos
php artisan migrate
php artisan db:seed

# 4. Desarrollar
composer run dev  # Laravel Boost
```

### Despliegue a Producción:
```bash
# 1. Preparar (local)
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Comprimir
tar -czf laravel-app.tar.gz \
  --exclude=node_modules \
  --exclude=.git \
  app/ bootstrap/ config/ database/ public/ resources/ routes/ storage/ vendor/ .env artisan

# 3. Subir al servidor
# - Descomprimir
# - Configurar permisos
# - Configurar .env
# - Ejecutar migraciones si es necesario
```

---

## 🎨 Estructura del Proyecto

```
panel-facturas/
├── app/
│   ├── Http/Controllers/     # Controladores
│   ├── Models/               # Modelos Eloquent
│   └── Services/             # Lógica de negocio
├── database/
│   ├── migrations/           # Estructura BD
│   └── seeders/              # Datos iniciales
├── resources/
│   ├── views/                # Vistas Blade
│   ├── css/                  # Estilos
│   └── js/                   # JavaScript mínimo
├── routes/
│   ├── web.php               # Rutas web
│   └── api.php               # Rutas API
├── vendor/                   # Subir completo
├── public/build/             # Assets compilados
├── .cursor/rules/            # Reglas del proyecto
└── .env                      # Configuración
```

---

## 🚨 Recordatorios Importantes

### SIEMPRE:
- ✅ Documentar código con PHPDoc
- ✅ Usar Eloquent para consultas
- ✅ Blade/Livewire para frontend
- ✅ MySQL en producción
- ✅ Subir vendor/ completo
- ✅ Compilar assets localmente
- ✅ **Usar sistema de traducciones `__('models.xxx')`**
- ✅ **NUNCA hardcodear textos en vistas**

### NUNCA:
- ❌ SQLite en producción
- ❌ JavaScript complejo
- ❌ Ejecutar composer en servidor
- ❌ Ejecutar npm en servidor
- ❌ Dependencias externas no incluidas
- ❌ **Hardcodear textos en vistas o controladores**

---

## 📚 Recursos

### Documentación:
- [Laravel 12](https://laravel.com/docs/12.x)
- [Filament](https://filamentphp.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)

### Herramientas:
- Laravel Boost (MCP para desarrollo)
- Laravel Pint (formateo de código)
- PHPUnit (testing)

---

## 🤝 Asistente de IA

El asistente tiene configurado contexto persistente en `.cursor/rules/` y:

- ✅ Ofrece múltiples soluciones
- ✅ Explica pros y contras
- ✅ Prioriza simplicidad
- ✅ Considera limitaciones del servidor
- ✅ Documenta todo el código
- ✅ Usa Laravel nativo

---

## 📞 Notas

**Última actualización**: 2025-10-16

Para más detalles, consulta los archivos en `.cursor/rules/`.

