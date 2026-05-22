# Panel de Facturas - ZafirosCalc

## Stack

- **Backend**: Laravel 12.34.0, PHP 8.2.12, MySQL 5.7.23 (prod) / SQLite (dev local)
- **Frontend**: Tailwind CSS v4, Blade, Alpine.js (solo si es necesario), Filament 4.0
- **Dev tools**: Laravel Boost (MCP), Laravel Pint, PHPUnit, Vite 7

## Hosting (compartido - JustPro)

- Apache 2.4.59, cPanel, recursos limitados
- **NO** Node.js, **NO** Docker/Sail, **NO** Composer/npm en servidor
- **SI** subir `vendor/` completo, **SI** compilar assets localmente y subir `public/build/`
- SQLite del servidor es VIEJO: **NO usar en produccion**, solo MySQL

## Regla CRITICA: i18n - NUNCA hardcodear textos

### SIEMPRE usar traducciones:

```blade
<!-- BIEN -->
<h1>{{ __('models.product.plural') }}</h1>
<button>{{ __('actions.create') }} {{ __('models.product.singular') }}</button>

<!-- MAL -->
<h1>Productos</h1>
<button>Crear Producto</button>
```

Estructura esperada:
```
resources/lang/es/
  models.php       # Nombres de modelos (singular, plural, article)
  navigation.php   # Menus y navegacion
  actions.php      # Acciones CRUD (crear, editar, eliminar...)
  messages.php     # Mensajes generales
  attributes.php   # Atributos/campos
```

Al crear un modelo nuevo: agregar entrada en `models.php` y usar `__('models.xxx')` en todas las vistas.

## Convenciones de codigo

- PHP 8.2+ con tipos estrictos cuando sea posible
- PHPDoc obligatorio en clases y metodos publicos
- Eloquent > SQL manual. Usar relaciones, scopes, mutators/accessors
- `Route::resource()` en vez de rutas manuales
- Validacion con Form Requests o `$request->validate()`
- Controladores simples, logica de negocio en Services o Actions
- Livewire > JavaScript complejo. Alpine.js solo para interacciones minimas (toggles, modales)
- Clases: PascalCase, metodos/variables: camelCase, tablas: snake_case plural, columnas: snake_case

## Despliegue

1. Local: `composer install --optimize-autoloader --no-dev`, `npm run build`
2. Cachear: `php artisan config:cache && php artisan route:cache && php artisan view:cache`
3. Comprimir todo (sin node_modules, .git, .env.local) y subir
4. En servidor: `chmod -R 755 storage/ bootstrap/cache/`, migraciones si necesario: `php artisan migrate --force`

Archivos que SIEMPRE se suben completos: `vendor/`, `public/build/`, `storage/`, `bootstrap/cache/`

## Filosofia

1. **Simplicidad primero**: usar lo nativo de Laravel antes que paquetes externos
2. **Auto-contenido**: todo incluido en el proyecto, cero instalaciones en servidor
3. **Mantenibilidad**: codigo claro, comentado, sin sobre-ingenieria
4. **Efectividad**: soluciones que funcionen sin complejidad innecesaria

## Comportamiento esperado del asistente

- Ofrecer siempre alternativas con pros/contras
- Justificar decisiones y priorizar simplicidad
- Considerar limitaciones del hosting compartido
- NUNCA hardcodear textos: usar `__()` siempre
- NUNCA sugerir compilacion o instalacion en servidor
- Verificar que las soluciones no requieran dependencias externas
