# Reglas de Internacionalización (i18n)

## 🌍 FILOSOFÍA DE INTERNACIONALIZACIÓN

### Principio Fundamental:
**NUNCA hardcodear textos en las vistas o controladores**

### Razones:
1. **Cambios centralizados**: Un cambio en un archivo afecta todas las vistas
2. **Multiidioma**: Preparado para agregar idiomas sin tocar código
3. **Mantenibilidad**: Fácil encontrar y cambiar textos
4. **Escalabilidad**: Agregar idiomas es trivial
5. **Consistencia**: Mismos textos en toda la aplicación

---

## 🚨 REGLA CRÍTICA: NO HARDCODEAR TEXTOS

### ❌ NUNCA hacer esto:
```blade
<!-- ❌ MAL - Texto hardcodeado -->
<h1>Productos</h1>
<button>Agregar Producto</button>
<a href="/productos">Ver Productos</a>
<th>Producto</th>
<td>Cliente</td>
```

### ✅ SIEMPRE hacer esto:
```blade
<!-- ✅ BIEN - Usar traducciones -->
<h1>{{ __('models.product.plural') }}</h1>
<button>{{ __('actions.create') }} {{ __('models.product.singular') }}</button>
<a href="/productos">{{ __('navigation.menu.products') }}</a>
<th>{{ __('models.product.singular') }}</th>
<td>{{ __('models.client.singular') }}</td>
```

---

## 📁 ESTRUCTURA DE ARCHIVOS DE IDIOMA

### Organización obligatoria:
```
resources/lang/
├── es/                         # Español (idioma principal)
│   ├── models.php             # Nombres de modelos
│   ├── navigation.php         # Menús y navegación
│   ├── actions.php            # Acciones (crear, editar, etc.)
│   ├── messages.php           # Mensajes generales
│   ├── validation.php         # Mensajes de validación
│   └── auth.php               # Mensajes de autenticación
└── en/                        # Inglés (opcional, futuro)
    └── (misma estructura)
```

---

## 📝 ARCHIVO: models.php

### Propósito:
Definir nombres de todos los modelos de la aplicación.

### Estructura obligatoria:
```php
<?php

return [
    // Cada modelo debe tener:
    // - singular: nombre en singular
    // - plural: nombre en plural
    // - article: artículo definido singular (el/la)
    // - article_plural: artículo definido plural (los/las)
    
    'product' => [
        'singular' => 'Producto',
        'plural' => 'Productos',
        'article' => 'el',
        'article_plural' => 'los',
    ],
    
    'client' => [
        'singular' => 'Cliente',
        'plural' => 'Clientes',
        'article' => 'el',
        'article_plural' => 'los',
    ],
    
    'invoice' => [
        'singular' => 'Factura',
        'plural' => 'Facturas',
        'article' => 'la',
        'article_plural' => 'las',
    ],
    
    'user' => [
        'singular' => 'Usuario',
        'plural' => 'Usuarios',
        'article' => 'el',
        'article_plural' => 'los',
    ],
];
```

### Cuándo actualizar:
- Al crear un nuevo modelo
- Al cambiar el nombre de un modelo existente

---

## 📝 ARCHIVO: navigation.php

### Propósito:
Definir textos de menús, breadcrumbs y navegación.

### Estructura obligatoria:
```php
<?php

return [
    // Menú principal
    'menu' => [
        'dashboard' => 'Dashboard',
        'products' => 'Productos',
        'clients' => 'Clientes',
        'invoices' => 'Facturas',
        'users' => 'Usuarios',
        'settings' => 'Configuración',
    ],
    
    // Breadcrumbs
    'breadcrumbs' => [
        'home' => 'Inicio',
        'products' => 'Productos',
        'products.create' => 'Crear Producto',
        'products.edit' => 'Editar Producto',
        'invoices' => 'Facturas',
        'invoices.create' => 'Nueva Factura',
    ],
    
    // Títulos de página
    'titles' => [
        'dashboard' => 'Panel de Control',
        'products.index' => 'Lista de Productos',
        'products.create' => 'Crear Producto',
        'products.edit' => 'Editar Producto',
    ],
];
```

---

## 📝 ARCHIVO: actions.php

### Propósito:
Definir acciones comunes (CRUD y más).

### Estructura obligatoria:
```php
<?php

return [
    // Acciones básicas CRUD
    'create' => 'Crear',
    'edit' => 'Editar',
    'update' => 'Actualizar',
    'delete' => 'Eliminar',
    'view' => 'Ver',
    'show' => 'Mostrar',
    'save' => 'Guardar',
    'cancel' => 'Cancelar',
    'back' => 'Volver',
    'search' => 'Buscar',
    'filter' => 'Filtrar',
    'export' => 'Exportar',
    'import' => 'Importar',
    
    // Acciones con parámetros
    'create_model' => 'Crear :model',
    'edit_model' => 'Editar :model',
    'delete_model' => 'Eliminar :model',
    'view_model' => 'Ver :model',
    
    // Confirmaciones
    'confirm_delete' => '¿Estás seguro de eliminar este registro?',
    'confirm_delete_model' => '¿Estás seguro de eliminar :article :model?',
    
    // Mensajes de éxito
    'created' => 'Creado exitosamente',
    'updated' => 'Actualizado exitosamente',
    'deleted' => 'Eliminado exitosamente',
    'saved' => 'Guardado exitosamente',
];
```

---

## 🎯 USO EN LAS VISTAS

### Controlador (index):
```blade
{{-- resources/views/products/index.blade.php --}}
<x-layout>
    {{-- Título de página --}}
    <h1 class="text-2xl font-bold mb-6">
        {{ __('models.product.plural') }}
    </h1>
    
    {{-- Botón crear --}}
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        {{ __('actions.create') }} {{ __('models.product.singular') }}
    </a>
    
    {{-- Tabla --}}
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('models.product.singular') }}</th>
                <th>Precio</th>
                <th>{{ __('actions.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>${{ $product->price }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}">
                            {{ __('actions.edit') }}
                        </a>
                        <button onclick="confirmDelete()">
                            {{ __('actions.delete') }}
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layout>
```

### Formulario (create/edit):
```blade
{{-- resources/views/products/create.blade.php --}}
<x-layout>
    <h1>
        {{ __('actions.create_model', ['model' => __('models.product.singular')]) }}
    </h1>
    
    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name">{{ __('models.product.singular') }}</label>
            <input type="text" name="name" id="name" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="price">Precio</label>
            <input type="number" name="price" id="price" class="form-control">
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">
                {{ __('actions.save') }}
            </button>
            
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                {{ __('actions.cancel') }}
            </a>
        </div>
    </form>
</x-layout>
```

---

## 🛠️ HELPERS PERSONALIZADOS

### Crear archivo: `app/Helpers/TranslationHelper.php`

```php
<?php

namespace App\Helpers;

/**
 * Helper para traducciones
 * 
 * Simplifica el acceso a traducciones de modelos
 * y genera textos comunes de forma consistente.
 */
class TranslationHelper
{
    /**
     * Obtener nombre singular de un modelo
     * 
     * @param string $model Nombre del modelo (product, client, etc.)
     * @return string
     */
    public static function modelSingular(string $model): string
    {
        return __('models.' . $model . '.singular');
    }
    
    /**
     * Obtener nombre plural de un modelo
     * 
     * @param string $model Nombre del modelo
     * @return string
     */
    public static function modelPlural(string $model): string
    {
        return __('models.' . $model . '.plural');
    }
    
    /**
     * Obtener artículo de un modelo
     * 
     * @param string $model Nombre del modelo
     * @param bool $plural Si es plural o singular
     * @return string
     */
    public static function modelArticle(string $model, bool $plural = false): string
    {
        $key = $plural ? 'article_plural' : 'article';
        return __('models.' . $model . '.' . $key);
    }
    
    /**
     * Crear texto completo con artículo
     * 
     * @param string $model Nombre del modelo
     * @param bool $plural Si es plural o singular
     * @return string Ejemplo: "el producto" o "los productos"
     */
    public static function withArticle(string $model, bool $plural = false): string
    {
        $article = self::modelArticle($model, $plural);
        $name = $plural ? self::modelPlural($model) : self::modelSingular($model);
        return $article . ' ' . $name;
    }
    
    /**
     * Generar texto de acción con modelo
     * 
     * @param string $action Acción (create, edit, delete, etc.)
     * @param string $model Nombre del modelo
     * @return string Ejemplo: "Crear Producto"
     */
    public static function actionWithModel(string $action, string $model): string
    {
        return __('actions.' . $action) . ' ' . self::modelSingular($model);
    }
}
```

### Uso de helpers:
```blade
{{-- En lugar de esto --}}
{{ __('actions.create') }} {{ __('models.product.singular') }}

{{-- Usar esto --}}
{{ TranslationHelper::actionWithModel('create', 'product') }}
```

---

## 🔄 CAMBIO DE NOMBRES DE MODELOS

### Ejemplo práctico:

**Situación**: Quieres cambiar "Productos" por "Artículos"

### ✅ Solución (solo 1 archivo):
```php
// resources/lang/es/models.php
return [
    'product' => [
        'singular' => 'Artículo',      // ← Cambio aquí
        'plural' => 'Artículos',       // ← Cambio aquí
        'article' => 'el',
        'article_plural' => 'los',
    ],
];
```

### ✅ Resultado automático:
- Todas las vistas se actualizan automáticamente
- Menús cambian a "Artículos"
- Botones cambian a "Crear Artículo"
- Títulos cambian a "Artículos"
- Breadcrumbs cambian a "Artículos"
- **CERO cambios manuales** en vistas

---

## 📋 CHECKLIST PARA CADA NUEVO MODELO

Cuando crees un nuevo modelo, **SIEMPRE**:

- [ ] Agregar entrada en `resources/lang/es/models.php`
- [ ] Definir singular, plural y artículos
- [ ] Agregar entradas en `resources/lang/es/navigation.php`
- [ ] Usar traducciones en todas las vistas del modelo
- [ ] Usar traducciones en controladores (mensajes flash)
- [ ] **NO hardcodear** ningún texto en las vistas

---

## 🎯 COMPORTAMIENTO DEL ASISTENTE

### SIEMPRE:
- ✅ Usar `__('models.xxx')` en lugar de texto hardcodeado
- ✅ Crear archivos de idioma si no existen
- ✅ Agregar modelos a `models.php` al crearlos
- ✅ Usar traducciones en vistas, controladores y componentes
- ✅ Sugerir nombres apropiados en archivos de idioma
- ✅ Mantener consistencia en nombres

### NUNCA:
- ❌ Hardcodear textos en vistas
- ❌ Poner nombres de modelos directamente en HTML
- ❌ Omitir traducciones en nuevos controladores
- ❌ Crear vistas sin usar sistema de traducciones
- ❌ Ignorar este sistema "para ir más rápido"

---

## 💡 VENTAJAS DE ESTE SISTEMA

### Inmediatas:
1. **Cambios centralizados**: Un archivo, todos los cambios
2. **Consistencia**: Mismo texto en toda la app
3. **Mantenibilidad**: Fácil encontrar y cambiar textos

### A mediano plazo:
4. **Multiidioma**: Agregar inglés es copiar archivos
5. **Escalabilidad**: Crecer sin refactorizar
6. **Profesionalidad**: Sistema estándar de Laravel

### A largo plazo:
7. **Internacionalización**: Vender en otros países
8. **Reusabilidad**: Componentes con textos dinámicos
9. **Testing**: Fácil probar con diferentes idiomas

---

## 🚨 EJEMPLO COMPLETO

### Archivo de idioma:
```php
// resources/lang/es/models.php
'invoice' => [
    'singular' => 'Factura',
    'plural' => 'Facturas',
    'article' => 'la',
    'article_plural' => 'las',
],
```

### Vista usando traducciones:
```blade
<h1>{{ __('models.invoice.plural') }}</h1>
<button>{{ __('actions.create') }} {{ __('models.invoice.singular') }}</button>
<p>Editar {{ __('models.invoice.article') }} {{ __('models.invoice.singular') }}</p>
```

### Cambiar a "Comprobantes":
```php
// Solo cambiar en models.php
'invoice' => [
    'singular' => 'Comprobante',
    'plural' => 'Comprobantes',
    'article' => 'el',
    'article_plural' => 'los',
],
```

### Resultado: TODO cambia automáticamente ✅

---

## 📚 RECURSOS

### Documentación Laravel:
- [Localización](https://laravel.com/docs/localization)
- [Helpers de traducción](https://laravel.com/docs/helpers#method-__)

### Convención de nombres:
- `models.*` - Nombres de modelos
- `navigation.*` - Menús y navegación
- `actions.*` - Acciones CRUD
- `messages.*` - Mensajes generales
- `validation.*` - Validaciones
- `auth.*` - Autenticación

---

## 🎯 RESUMEN EJECUTIVO

**Regla de oro**: **NUNCA hardcodear textos**

**Proceso**:
1. Crear modelo → Agregar a `models.php`
2. Crear vista → Usar `__('models.xxx')`
3. Cambiar nombre → Editar `models.php` solamente

**Resultado**: Mantenibilidad, escalabilidad y profesionalismo 🚀

