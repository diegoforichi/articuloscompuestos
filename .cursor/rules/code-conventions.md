# Convenciones de Código

## 📝 COMENTARIOS OBLIGATORIOS

### PHPDoc en Clases:
```php
<?php

namespace App\Http\Controllers;

/**
 * Controlador para gestión de facturas
 * 
 * Este controlador maneja todas las operaciones CRUD de facturas
 * del sistema. Mantiene la lógica simple y delega tareas complejas
 * a servicios cuando es necesario.
 * 
 * @package App\Http\Controllers
 * @version 1.0.0
 */
class FacturaController extends Controller
{
    // Código aquí
}
```

### PHPDoc en Métodos:
```php
/**
 * Listar facturas con paginación
 * 
 * Obtiene todas las facturas del sistema con información
 * del cliente relacionado, ordenadas por fecha de creación
 * de forma descendente.
 * 
 * @return \Illuminate\View\View
 */
public function index()
{
    $facturas = Factura::with('cliente')
        ->latest()
        ->paginate(10);
        
    return view('facturas.index', compact('facturas'));
}
```

### PHPDoc con Parámetros:
```php
/**
 * Guardar nueva factura en el sistema
 * 
 * Valida los datos recibidos y crea una nueva factura
 * asociada al cliente especificado. Calcula automáticamente
 * el total basado en los productos/servicios incluidos.
 * 
 * @param \Illuminate\Http\Request $request Datos de la factura
 * @return \Illuminate\Http\RedirectResponse
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'fecha' => 'required|date',
        'total' => 'required|numeric|min:0',
    ]);
    
    $factura = Factura::create($validated);
    
    return redirect()
        ->route('facturas.show', $factura)
        ->with('success', 'Factura creada exitosamente');
}
```

---

## 🏗️ ESTRUCTURA DE CÓDIGO

### Controladores (Simple):
```php
<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;

/**
 * Controlador de facturas
 */
class FacturaController extends Controller
{
    /**
     * Listar facturas
     */
    public function index()
    {
        $facturas = Factura::with('cliente')
            ->latest()
            ->paginate(10);
            
        return view('facturas.index', compact('facturas'));
    }
    
    /**
     * Mostrar formulario de nueva factura
     */
    public function create()
    {
        return view('facturas.create');
    }
    
    /**
     * Guardar nueva factura
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'total' => 'required|numeric|min:0',
        ]);
        
        $factura = Factura::create($validated);
        
        return redirect()
            ->route('facturas.show', $factura)
            ->with('success', 'Factura creada exitosamente');
    }
}
```

### Modelos (Eloquent):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Factura
 * 
 * Representa una factura del sistema con toda su información
 * relacionada (cliente, productos, total, etc.)
 * 
 * @property int $id
 * @property int $cliente_id
 * @property string $numero
 * @property float $total
 * @property \Carbon\Carbon $fecha
 */
class Factura extends Model
{
    /**
     * Atributos asignables en masa
     */
    protected $fillable = [
        'cliente_id',
        'numero',
        'fecha',
        'total',
        'estado',
    ];
    
    /**
     * Casteo de atributos
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'total' => 'decimal:2',
        ];
    }
    
    /**
     * Relación con Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
```

---

## 🎨 FRONTEND (Blade + Tailwind)

### 🌍 INTERNACIONALIZACIÓN OBLIGATORIA

**REGLA CRÍTICA**: NUNCA hardcodear textos en vistas

### ❌ MAL - Texto hardcodeado:
```blade
<h1>Facturas</h1>
<button>Crear Factura</button>
<th>Número</th>
<th>Cliente</th>
```

### ✅ BIEN - Usar traducciones:
```blade
<h1>{{ __('models.invoice.plural') }}</h1>
<button>{{ __('actions.create') }} {{ __('models.invoice.singular') }}</button>
<th>{{ __('attributes.number') }}</th>
<th>{{ __('models.client.singular') }}</th>
```

### Vistas Blade (con traducciones):
```blade
{{-- resources/views/facturas/index.blade.php --}}
<x-layout>
    <div class="container mx-auto px-4 py-8">
        {{-- Título usando traducción --}}
        <h1 class="text-2xl font-bold mb-6">
            {{ __('models.invoice.plural') }}
        </h1>
        
        {{-- Botón crear usando traducciones --}}
        <div class="mb-4">
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                {{ __('actions.create') }} {{ __('models.invoice.singular') }}
            </a>
        </div>
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        {{-- Usar traducciones en headers --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('attributes.number') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('models.client.singular') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('attributes.total') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('actions.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($facturas as $factura)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $factura->numero }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $factura->cliente->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                ${{ number_format($factura->total, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('invoices.edit', $factura) }}">
                                    {{ __('actions.edit') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $facturas->links() }}
        </div>
    </div>
</x-layout>
```

### Formularios (con traducciones):
```blade
{{-- resources/views/invoices/create.blade.php --}}
<x-layout>
    {{-- Título con traducción y parámetro --}}
    <h1>{{ __('actions.create_model', ['model' => __('models.invoice.singular')]) }}</h1>
    
    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="client_id">{{ __('models.client.singular') }}</label>
            <select name="client_id" id="client_id" class="form-control">
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="total">{{ __('attributes.total') }}</label>
            <input type="number" name="total" id="total" class="form-control">
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">
                {{ __('actions.save') }}
            </button>
            
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                {{ __('actions.cancel') }}
            </a>
        </div>
    </form>
</x-layout>
```

---

## 🚫 JAVASCRIPT - RESTRICCIONES

### ❌ NO hacer:
```javascript
// ❌ Vanilla JavaScript complejo
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.btn-delete');
    buttons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            // Lógica compleja aquí
        });
    });
});

// ❌ jQuery
$('.btn-delete').on('click', function() {
    // ...
});

// ❌ Frameworks pesados sin necesidad
import React from 'react';
import ReactDOM from 'react-dom';
```

### ✅ SÍ hacer (Livewire):
```php
<?php

namespace App\Livewire;

use App\Models\Factura;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente Livewire para lista de facturas
 * 
 * Permite filtrar, buscar y paginar facturas sin JavaScript
 */
class FacturasList extends Component
{
    use WithPagination;
    
    public $search = '';
    
    /**
     * Renderizar componente
     */
    public function render()
    {
        $facturas = Factura::with('cliente')
            ->when($this->search, function ($query) {
                $query->where('numero', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
            
        return view('livewire.facturas-list', [
            'facturas' => $facturas,
        ]);
    }
}
```

### ✅ SÍ hacer (Alpine.js solo si es necesario):
```html
<!-- Solo para interacciones simples -->
<div x-data="{ open: false }">
    <button @click="open = !open" class="btn btn-primary">
        Toggle
    </button>
    
    <div x-show="open" class="mt-4">
        Contenido revelado
    </div>
</div>
```

---

## 📋 CONVENCIONES GENERALES

### Nombres:
- **Clases**: PascalCase (`FacturaController`, `ClienteModel`)
- **Métodos**: camelCase (`index()`, `createFactura()`)
- **Variables**: camelCase (`$facturaTotal`, `$clienteId`)
- **Constantes**: UPPER_SNAKE_CASE (`MAX_FACTURAS`)
- **Tablas BD**: plural snake_case (`facturas`, `clientes`)
- **Columnas BD**: snake_case (`cliente_id`, `fecha_emision`)

### Estructura de Archivos:
- Un controlador por archivo
- Un modelo por archivo
- Rutas agrupadas lógicamente
- Vistas organizadas por recurso

### Comentarios:
- PHPDoc obligatorio en clases y métodos públicos
- Comentarios inline solo para lógica compleja
- Explicar QUÉ hace, no CÓMO lo hace
- Mantener comentarios actualizados

---

## ✅ BUENAS PRÁCTICAS

### Consultas Eloquent:
```php
// ✅ Usar Eloquent
$facturas = Factura::with('cliente')
    ->where('estado', 'pendiente')
    ->latest()
    ->get();

// ❌ NO usar SQL manual
$facturas = DB::select('SELECT * FROM facturas WHERE estado = ?', ['pendiente']);
```

### Validación:
```php
// ✅ Usar Request Validation
$validated = $request->validate([
    'cliente_id' => 'required|exists:clientes,id',
    'fecha' => 'required|date',
    'total' => 'required|numeric|min:0',
]);

// ❌ NO validación manual
if (!isset($request->cliente_id)) {
    return back()->with('error', 'Cliente requerido');
}
```

### Rutas:
```php
// ✅ Usar Resource Routes
Route::resource('facturas', FacturaController::class);

// ❌ NO definir manualmente
Route::get('/facturas', [FacturaController::class, 'index']);
Route::get('/facturas/create', [FacturaController::class, 'create']);
// etc...
```

---

## 🎯 RESUMEN

**Prioridades:**
1. **Comentarios**: PHPDoc completo
2. **Simplicidad**: Código claro y directo
3. **Laravel nativo**: Usar características incluidas
4. **Eloquent**: Para todas las consultas
5. **Blade/Livewire**: Evitar JavaScript complejo
6. **Convenciones**: Seguir estándares de Laravel

