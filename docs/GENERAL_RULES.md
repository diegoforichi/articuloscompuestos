# Reglas Generales de Desarrollo Laravel
## Para Hosting Compartido

**Versión**: 1.0  
**Última actualización**: 2025-10-16  
**Aplica a**: Todos los proyectos Laravel en hosting compartido  
**Autor**: [Tu nombre/empresa]

---

## 🎯 FILOSOFÍA DE DESARROLLO

### Principios Fundamentales (en orden de prioridad):

1. **SIMPLICIDAD** > Complejidad
   - Buscar siempre la solución más simple
   - Si hay 3 formas de hacerlo, elegir la más simple
   - No inventar soluciones complejas

2. **MANTENIBILIDAD** > Elegancia
   - Código fácil de leer y modificar
   - Documentación completa
   - Estructura clara y consistente

3. **EFECTIVIDAD** > Perfección
   - Soluciones que funcionen
   - No optimizar prematuramente
   - Entregar valor al cliente

4. **SEGURIDAD** siempre
   - Nunca comprometer seguridad por velocidad
   - Validar todo input
   - Logs de accesos críticos

5. **AUTONOMÍA** del servidor
   - No depender de instalaciones externas
   - Todo incluido en el proyecto
   - Reproducible sin dependencias

---

## 🏗️ STACK TECNOLÓGICO

### ✅ Permitido y Recomendado:

**Backend**:
- Laravel (versión LTS: 11.x o 12.x)
- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.3+
- Eloquent ORM (nativo Laravel)

**Frontend**:
- Blade Templates (nativo Laravel)
- Tailwind CSS v4
- Livewire (opcional, para interactividad sin JS)
- Alpine.js (opcional, si Livewire no es suficiente)

**Herramientas**:
- Composer (gestión de dependencias PHP)
- Laravel Pint (formateo de código)
- PHPUnit (testing)
- Vite (bundling, solo en local)

**Paneles Admin** (opcional):
- Filament (compatible con shared hosting)
- Laravel Nova (si el cliente paga licencia)

### ❌ Prohibido en Producción:

- ❌ Node.js en servidor
- ❌ Docker/Sail en producción
- ❌ Frameworks JS pesados (React, Vue standalone)
- ❌ ORMs externos (usar Eloquent)
- ❌ Compilación en servidor
- ❌ Composer install en servidor
- ❌ npm install en servidor

---

## 🚨 LIMITACIONES DEL HOSTING COMPARTIDO

### Restricciones Técnicas:

**NO disponible**:
- SSH completo (solo limitado)
- Node.js / npm
- Composer global
- Docker
- Root access
- Procesos en background indefinidos
- Cron jobs avanzados

**Limitado**:
- Memoria PHP: ~256MB
- Tiempo de ejecución: ~30s
- Upload de archivos: ~10MB
- CPU compartido
- Concurrencia limitada

### Estrategia de Mitigación:

1. **Subir vendor/ completo** - No ejecutar composer en servidor
2. **Compilar assets localmente** - No ejecutar npm en servidor
3. **Optimizar Laravel** - Cache de config, routes, views
4. **Base de datos eficiente** - Índices, paginación, eager loading
5. **No procesos largos** - Dividir en tareas pequeñas

---

## 🗄️ BASE DE DATOS

### Configuración Obligatoria:

**Producción**:
- MySQL 5.7+ o MariaDB 10.3+
- InnoDB engine
- UTF8MB4 charset
- Crear base de datos manualmente en cPanel/phpMyAdmin

**Desarrollo Local**:
- MySQL (recomendado, mismo que producción)
- SQLite (solo para tests rápidos)

### Convenciones:

```php
// Nombres de tablas: plural snake_case
users, invoices, invoice_items

// Columnas: snake_case
client_id, created_at, first_name

// Primary key: id (auto-increment)
// Foreign keys: modelo_id (singular)
// Timestamps: created_at, updated_at
// Soft deletes: deleted_at
```

### Migraciones:

```bash
# Crear migración
php artisan make:migration create_invoices_table

# Ejecutar migraciones
php artisan migrate

# NUNCA usar en producción:
php artisan migrate:fresh  # Borra todo
```

### Performance:

```php
// ✅ BIEN - Eager loading
$invoices = Invoice::with('client', 'items')->get();

// ❌ MAL - N+1 queries
$invoices = Invoice::all();
foreach ($invoices as $invoice) {
    echo $invoice->client->name; // Query por cada invoice
}

// ✅ BIEN - Paginación
$invoices = Invoice::paginate(20);

// ❌ MAL - Cargar todo
$invoices = Invoice::all();
```

---

## 🌍 INTERNACIONALIZACIÓN (i18n)

### Regla CRÍTICA: NUNCA Hardcodear Textos

**❌ NUNCA hacer esto**:
```blade
<h1>Productos</h1>
<button>Crear Producto</button>
```

**✅ SIEMPRE hacer esto**:
```blade
<h1>{{ __('models.product.plural') }}</h1>
<button>{{ __('actions.create') }} {{ __('models.product.singular') }}</button>
```

### Estructura Obligatoria:

```
resources/lang/
└── es/
    ├── models.php         # Nombres de modelos
    ├── navigation.php     # Menús y navegación
    ├── actions.php        # Acciones CRUD
    ├── messages.php       # Mensajes generales
    ├── attributes.php     # Campos/atributos
    └── validation.php     # Mensajes de validación
```

### Archivo models.php:

```php
return [
    'product' => [
        'singular' => 'Producto',
        'plural' => 'Productos',
        'article' => 'el',
        'article_plural' => 'los',
    ],
    // Agregar todos los modelos aquí
];
```

### Beneficio:

Cambiar "Productos" → "Artículos" = **editar 1 archivo**  
TODO se actualiza automáticamente en toda la aplicación

**Ver**: `.cursor/rules/i18n-rules.md` para detalles completos

---

## 📝 DOCUMENTACIÓN OBLIGATORIA

### PHPDoc en Código:

```php
/**
 * Controlador para gestión de facturas
 * 
 * Maneja todas las operaciones CRUD de facturas del sistema.
 * Mantiene la lógica simple y delega tareas complejas a servicios.
 * 
 * @package App\Http\Controllers
 * @version 1.0.0
 */
class InvoiceController extends Controller
{
    /**
     * Listar facturas con paginación
     * 
     * Obtiene todas las facturas con información del cliente,
     * ordenadas por fecha de creación descendente.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Código aquí
    }
}
```

### Archivos de Proyecto:

- **README.md**: Instalación y setup
- **CONTEXT.md**: Resumen del proyecto
- **CHANGELOG.md**: Historial de cambios
- **docs/PROJECT_SPECIFIC.md**: Reglas específicas del proyecto

---

## ✅ VALIDACIÓN Y SEGURIDAD

### Validación Obligatoria:

```php
// ✅ Usar Form Requests
php artisan make:request StoreInvoiceRequest

// En StoreInvoiceRequest:
public function rules()
{
    return [
        'client_id' => 'required|exists:clients,id',
        'total' => 'required|numeric|min:0',
        'date' => 'required|date',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
    ];
}

// En el controlador:
public function store(StoreInvoiceRequest $request)
{
    $validated = $request->validated();
    // Datos ya validados
}
```

### Seguridad:

```php
// ✅ CSRF Protection (automático en Laravel)
@csrf

// ✅ Sanitización de outputs (automático en Blade)
{{ $user->name }}  // Escapa HTML automáticamente

// ✅ SQL Injection (automático con Eloquent)
Invoice::where('client_id', $id)->get();  // Seguro

// ❌ NUNCA hacer:
DB::select("SELECT * FROM invoices WHERE client_id = $id");  // Inseguro
```

---

## 🧪 TESTING OBLIGATORIO

### Cobertura Mínima:

- Feature tests: 70%
- Unit tests: 80% (lógica de negocio)
- Tests pasando antes de deploy

### Estructura:

```bash
tests/
├── Feature/
│   ├── InvoiceTest.php      # CRUD de facturas
│   ├── ClientTest.php       # CRUD de clientes
│   └── AuthTest.php         # Autenticación
└── Unit/
    ├── InvoiceServiceTest.php  # Lógica de negocio
    └── CalculatorTest.php      # Cálculos
```

### Ejemplo:

```php
public function test_can_create_invoice()
{
    $client = Client::factory()->create();
    
    $response = $this->post('/invoices', [
        'client_id' => $client->id,
        'total' => 100.50,
        'date' => now(),
    ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('invoices', [
        'client_id' => $client->id,
        'total' => 100.50,
    ]);
}
```

### Comandos:

```bash
# Ejecutar tests
php artisan test

# Con cobertura
php artisan test --coverage

# Solo un test
php artisan test --filter test_can_create_invoice
```

---

## 📦 GESTIÓN DE DEPENDENCIAS

### Composer (PHP):

```bash
# Instalar dependencias (local)
composer install

# Agregar paquete
composer require package/name

# Actualizar
composer update

# Producción (local, luego subir vendor/)
composer install --optimize-autoloader --no-dev
```

### NPM (JavaScript):

```bash
# Instalar (local)
npm install

# Compilar para producción (local)
npm run build

# Desarrollo (local)
npm run dev
```

### Regla CRÍTICA:

- ✅ Ejecutar en **local**
- ✅ Subir `vendor/` completo al servidor
- ✅ Subir `public/build/` con assets compilados
- ❌ NO ejecutar en servidor

---

## 🚀 PROCESO DE DEPLOYMENT

### Preparación (Local):

```bash
# 1. Tests
php artisan test

# 2. Instalar dependencias
composer install --optimize-autoloader --no-dev

# 3. Compilar assets
npm run build

# 4. Optimizar Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Verificar .env.example actualizado
cp .env .env.example
# Limpiar valores sensibles en .env.example
```

### Subir al Servidor:

```bash
# Comprimir proyecto
tar -czf project.tar.gz \
  --exclude=node_modules \
  --exclude=.git \
  --exclude=storage/logs/*.log \
  --exclude=.env.local \
  app/ bootstrap/ config/ database/ public/ resources/ routes/ storage/ vendor/ .env artisan composer.json

# Subir via FTP/SFTP
# Descomprimir en servidor
# Configurar permisos
```

### En el Servidor:

```bash
# 1. Permisos
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# 2. Configurar .env de producción
# Editar .env con datos del servidor

# 3. Migraciones (si hay nuevas)
php artisan migrate --force

# 4. Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Verificar
# Probar funcionalidades críticas
```

### Checklist de Deploy:

- [ ] Tests pasando localmente
- [ ] Base de datos creada en servidor
- [ ] .env configurado en servidor
- [ ] vendor/ subido completo
- [ ] public/build/ con assets compilados
- [ ] Permisos configurados
- [ ] Migraciones ejecutadas
- [ ] Cache optimizado
- [ ] Aplicación verificada
- [ ] Backup realizado

---

## 💾 BACKUPS Y RECUPERACIÓN

### Frecuencia Obligatoria:

- **Base de datos**: Diario (automático)
- **Archivos**: Semanal
- **Completo**: Mensual

### Retención:

- Diarios: 7 días
- Semanales: 4 semanas
- Mensuales: 12 meses

### Verificación:

- ✅ Probar restauración mensualmente
- ✅ Documentar proceso de recuperación
- ✅ Almacenar backups fuera del servidor

### Script de Backup (ejemplo):

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d)
DB_NAME="your_database"
BACKUP_DIR="/path/to/backups"

# Backup de base de datos
mysqldump -u user -p$PASSWORD $DB_NAME > "$BACKUP_DIR/db_$DATE.sql"

# Backup de archivos
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" /path/to/project/storage

# Limpiar backups antiguos (más de 7 días)
find $BACKUP_DIR -name "db_*.sql" -mtime +7 -delete
```

---

## 📊 LOGGING Y MONITOREO

### Niveles de Log:

```php
// Usar niveles apropiados
Log::emergency('Sistema caído');
Log::alert('Acción inmediata requerida');
Log::critical('Condición crítica');
Log::error('Error de ejecución');
Log::warning('Advertencia');
Log::notice('Evento normal importante');
Log::info('Información');
Log::debug('Depuración');
```

### Qué Loggear:

```php
// ✅ Eventos importantes
Log::info('Factura creada', [
    'invoice_id' => $invoice->id,
    'client_id' => $invoice->client_id,
    'user_id' => auth()->id(),
    'total' => $invoice->total,
]);

// ✅ Errores
try {
    // Código
} catch (\Exception $e) {
    Log::error('Error al crear factura', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
}

// ✅ Accesos no autorizados
Log::warning('Intento de acceso no autorizado', [
    'user_id' => auth()->id(),
    'url' => request()->url(),
]);
```

### Monitoreo:

- Revisar logs semanalmente
- Configurar alertas para errores críticos
- Monitorear uso de disco
- Verificar uptime

---

## 🔄 CONTROL DE VERSIONES (GIT)

### .gitignore Obligatorio:

```gitignore
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
```

### Convenciones de Commits:

```bash
# Formato
tipo: descripción corta

# Tipos:
feat: Nueva funcionalidad
fix: Corrección de bug
docs: Documentación
style: Formato (no afecta código)
refactor: Refactorización
test: Tests
chore: Mantenimiento

# Ejemplos:
feat: agregar CRUD de facturas
fix: corregir cálculo de total en facturas
docs: actualizar README con instrucciones de deploy
refactor: simplificar InvoiceController
test: agregar tests para facturas
```

### Branches:

```
main/master     → Producción (protegido)
develop         → Desarrollo
feature/xxx     → Nuevas características
fix/xxx         → Correcciones
hotfix/xxx      → Correcciones urgentes en producción
```

---

## ⚡ PERFORMANCE

### Optimizaciones Obligatorias:

```php
// 1. Eager Loading
$invoices = Invoice::with('client', 'items.product')->get();

// 2. Paginación
$invoices = Invoice::paginate(20);

// 3. Índices en BD
Schema::table('invoices', function (Blueprint $table) {
    $table->index('client_id');
    $table->index('created_at');
});

// 4. Cache
Cache::remember('clients', 3600, function () {
    return Client::all();
});
```

### Cache en Producción:

```bash
# SIEMPRE ejecutar en producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🎯 PRINCIPIOS DE CÓDIGO

### SOLID (Simplificado):

- **S**ingle Responsibility: Una clase, un propósito
- **O**pen/Closed: Abierto a extensión, cerrado a modificación
- **L**iskov Substitution: Subclases sustituibles
- **I**nterface Segregation: Interfaces específicas
- **D**ependency Inversion: Depender de abstracciones

### DRY: Don't Repeat Yourself
- No duplicar código
- Crear helpers/servicios para código repetido

### KISS: Keep It Simple, Stupid
- Buscar siempre la solución más simple
- No sobre-ingenierizar

### YAGNI: You Aren't Gonna Need It
- No implementar funcionalidades "por si acaso"
- Implementar solo lo necesario ahora

---

## 📋 CHECKLIST DE PROYECTO

### Al Iniciar Proyecto:

- [ ] Clonar/crear proyecto Laravel
- [ ] Configurar .gitignore
- [ ] Crear .env.example
- [ ] Configurar idiomas (es por defecto)
- [ ] Crear estructura de tests
- [ ] Documentar README.md
- [ ] Crear docs/PROJECT_SPECIFIC.md

### Durante Desarrollo:

- [ ] Tests para nuevas features
- [ ] Documentar código (PHPDoc)
- [ ] Usar traducciones (no hardcode)
- [ ] Commits descriptivos
- [ ] Code review antes de merge

### Antes de Deploy:

- [ ] Tests pasando (100%)
- [ ] Configurar .env de producción
- [ ] Compilar assets (npm run build)
- [ ] Optimizar autoloader
- [ ] Cache de configuración
- [ ] Backup de BD
- [ ] Verificar .gitignore
- [ ] Actualizar CHANGELOG.md

### Después de Deploy:

- [ ] Verificar aplicación funciona
- [ ] Probar funcionalidades críticas
- [ ] Verificar logs (sin errores)
- [ ] Backup completo
- [ ] Documentar cambios
- [ ] Notificar al cliente (si aplica)

---

## 📚 RECURSOS Y REFERENCIAS

### Documentación Oficial:
- [Laravel](https://laravel.com/docs)
- [PHP](https://www.php.net/manual/es/)
- [MySQL](https://dev.mysql.com/doc/)
- [Tailwind CSS](https://tailwindcss.com/docs)

### Aprendizaje:
- [Laracasts](https://laracasts.com)
- [Laravel News](https://laravel-news.com)
- [Laravel Daily](https://laraveldaily.com)

### Herramientas:
- [Laravel Pint](https://laravel.com/docs/pint)
- [PHPStan](https://phpstan.org)
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)

---

## 🔄 MANTENIMIENTO DE ESTE DOCUMENTO

### Actualizar cuando:
- Cambien mejores prácticas de Laravel
- Se descubran nuevas limitaciones del hosting
- Se agreguen nuevas tecnologías al stack
- Se identifiquen nuevos patrones útiles

### Historial de Cambios:
- **v1.0 (2025-10-16)**: Versión inicial

---

**Nota**: Este documento es la base para todos los proyectos Laravel en hosting compartido. Las reglas específicas de cada proyecto deben documentarse en `docs/PROJECT_SPECIFIC.md`.

**Ver también**:
- `AI_DEVELOPMENT_GUIDELINES.md` - Trabajar con asistentes de IA
- `SECURITY_CHECKLIST.md` - Checklist de seguridad
- `.cursor/rules/` - Reglas técnicas específicas

