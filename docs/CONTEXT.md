# Contexto del proyecto — Articulos compuestos

## Resumen ejecutivo

Laravel 12 con **Filament** para administrar **productos compuestos**: insumos con precio por unidad (**componentes**), armado del producto final, cálculo de costo y precio de venta (utilidad %), historial de precios e integración saliente con **e-factura**. El caso inicial fue una **joyeria**, pero el diseño apunta a **cualquier negocio** que necesite listas de materiales y categorias propias.

Los **tipos de componente** (ej. metal, harina, empaque) son un **catálogo editable** en el panel (`component_types`). Las **unidades** por componente son texto libre (kg, g, hora, etc.).

El proyecto esta optimizado para **hosting compartido** (sin Node en servidor, subir `vendor/` completo). Repo: [articuloscompuestos](https://github.com/diegoforichi/articuloscompuestos).

Estado operativo (panel `/admin`):

- **Tenants**: crear con onboarding opcional de admin; editar sin onboarding; usuarios en relation manager; badge si no hay usuarios.
- **Integracion**: una configuracion por `tenant_id` (validacion UI + unique en BD).
- **Stock**: entradas, movimientos con referencia legible, saldos, consumo desde producto con preview de composicion; export CSV.
- **Productos**: composicion, recalculo manual/automatico por tenant, sync e-factura del producto final.
- **Roles**: superadmin sin `tenant_id` usa tenant `default` (`Tenant::DEFAULT_SLUG`) para datos operativos.

Checklist de prueba local: `docs/UAT_LOCAL_MULTITENANT.md`.

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

## Estado de evolucion acordada

### Implementado

- Entradas por documento con detalle de componentes y costo.
- Libro de movimientos (`IN`, `OUT`) y snapshot de saldos por componente.
- Validaciones transaccionales para bloqueo de stock negativo.
- Flujo de consumo de stock desde productos.

### Pendiente para declarar listo en produccion

- Ejecutar checklist UAT integral en entorno real.
- Ajustar runbook final de cola segun cron/limits del hosting.
- Documentar procedimiento operativo por rol (superadmin y admin tenant).

---

## Fuente de verdad de pendientes

El backlog consolidado, criterios de aceptacion y checklist de cierre de fase se mantienen en:

- `docs/PLAN_MAESTRO_IMPLEMENTACION.md`

---

## 🎨 Estructura del Proyecto

```
zafirosCalc/   (nombre local; repo GitHub: articuloscompuestos)
├── app/
│   ├── Filament/Resources/   # Panel: productos, componentes, tipos, integración…
│   ├── Models/
│   └── Services/             # p. ej. EFacturaService
├── database/migrations/
├── docs/                     # MVP, deploy, cliente
├── public/
└── vendor/                   # Subir completo en hosting compartido
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

**Última actualización**: 2026-05-18

Para más detalles, consulta los archivos en `.cursor/rules/`.

