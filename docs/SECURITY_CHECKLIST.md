# Checklist de Seguridad Laravel
## Para Hosting Compartido

**Versión**: 1.0  
**Última actualización**: 2025-10-16  
**Aplica a**: Todos los proyectos Laravel

---

## 🎯 PROPÓSITO

Este checklist asegura que tu aplicación Laravel cumpla con estándares mínimos de seguridad antes, durante y después del deployment.

**Uso**: Marcar cada item antes de cada deploy a producción.

---

## 🔒 ANTES DEL PRIMER DEPLOY

### Configuración Inicial:

- [ ] **APP_KEY generado**: `php artisan key:generate`
- [ ] **APP_KEY único** por entorno (local ≠ producción)
- [ ] **.env NO está en Git**: Verificar `.gitignore`
- [ ] **.env.example actualizado**: Sin valores sensibles
- [ ] **APP_DEBUG=false** en producción
- [ ] **APP_ENV=production** en servidor

### Base de Datos:

- [ ] **Usuario de BD con permisos mínimos**: No usar root
- [ ] **Contraseña fuerte** para usuario de BD
- [ ] **Base de datos creada manualmente**: Verificar en phpMyAdmin
- [ ] **Credenciales en .env**: No hardcodeadas en config
- [ ] **Sin datos de prueba** en producción

### Archivos y Permisos:

- [ ] **storage/ con 755**: `chmod -R 755 storage/`
- [ ] **bootstrap/cache/ con 755**: `chmod -R 755 bootstrap/cache/`
- [ ] **.env con 600**: Solo lectura para owner
- [ ] **Logs no públicos**: storage/logs no accesible por web
- [ ] **Vendor no modificado**: Sin cambios manuales

### HTTPS y URLs:

- [ ] **Certificado SSL válido**: Verificar en navegador
- [ ] **APP_URL con https://**: En .env de producción
- [ ] **ForceScheme HTTPS**: En AppServiceProvider si es necesario
- [ ] **Redirect HTTP → HTTPS**: En .htaccess
- [ ] **HSTS Header**: Configurado (opcional)

---

## 🛡️ PROTECCIONES DE LARAVEL

### CSRF Protection:

- [ ] **@csrf en todos los formularios**:
```blade
<form method="POST" action="/invoices">
    @csrf
    <!-- campos -->
</form>
```

- [ ] **VerifyCsrfToken activo**: Verificar en middleware
- [ ] **Excepciones justificadas**: Si hay rutas excluidas, documentar por qué

### SQL Injection:

- [ ] **Usar Eloquent para queries**:
```php
// ✅ BIEN
Invoice::where('client_id', $id)->get();

// ❌ MAL
DB::select("SELECT * FROM invoices WHERE client_id = $id");
```

- [ ] **Bindings en raw queries**: Si usas DB::raw()
- [ ] **No concatenar strings en queries**: NUNCA

### XSS Protection:

- [ ] **Blade escaping automático**:
```blade
{{-- ✅ Escapa HTML automáticamente --}}
{{ $user->name }}

{{-- ❌ Solo usar si confías en el HTML --}}
{!! $trustedHtml !!}
```

- [ ] **Sanitizar inputs de usuario**: Validación + limpieza
- [ ] **CSP Header** (opcional): Content-Security-Policy

### Mass Assignment:

- [ ] **$fillable o $guarded en modelos**:
```php
// ✅ BIEN
protected $fillable = ['name', 'email', 'phone'];

// ❌ MAL
protected $guarded = [];  // Peligroso, permitir todo
```

- [ ] **No usar $request->all() directamente**: Usar validated()

---

## 🔐 AUTENTICACIÓN Y AUTORIZACIÓN

### Passwords:

- [ ] **Hashing con bcrypt/argon2**: Laravel lo hace automáticamente
- [ ] **Longitud mínima**: 8 caracteres
- [ ] **Validación de complejidad**: Números, mayúsculas, símbolos
- [ ] **No enviar por email**: Solo tokens de reset

### Sesiones:

- [ ] **SESSION_DRIVER configurado**: file/database (no array en prod)
- [ ] **SESSION_LIFETIME apropiado**: 120 minutos por defecto
- [ ] **SESSION_SECURE_COOKIE=true**: Con HTTPS
- [ ] **SESSION_HTTP_ONLY=true**: Prevenir XSS
- [ ] **SESSION_SAME_SITE**: lax o strict

### Autorización:

- [ ] **Gates o Policies** para permisos:
```php
// En controlador
$this->authorize('update', $invoice);
```

- [ ] **Middleware auth** en rutas protegidas:
```php
Route::middleware('auth')->group(function () {
    Route::resource('invoices', InvoiceController::class);
});
```

- [ ] **Verificar ownership**: Usuario solo accede a sus datos
- [ ] **Admin area protegido**: Middleware de rol/permiso

---

## ✅ VALIDACIÓN DE INPUTS

### Form Requests:

- [ ] **Form Request para cada formulario**:
```php
php artisan make:request StoreInvoiceRequest
```

- [ ] **Reglas de validación estrictas**:
```php
public function rules()
{
    return [
        'client_id' => 'required|exists:clients,id',
        'total' => 'required|numeric|min:0|max:999999.99',
        'date' => 'required|date|before_or_equal:today',
        'email' => 'required|email:rfc,dns',
    ];
}
```

- [ ] **Mensajes personalizados**: En español si aplica
- [ ] **Sanitización adicional**: trim, lowercase, etc.

### Validación de Archivos:

- [ ] **Validar tipo de archivo**: mimes, mimetypes
- [ ] **Validar tamaño**: max:2048 (2MB)
- [ ] **Almacenar fuera de public/**: storage/app/
- [ ] **Generar nombres únicos**: No usar nombre original
- [ ] **Escanear malware**: Si es crítico

```php
'file' => 'required|file|mimes:pdf,jpg,png|max:2048'
```

---

## 🌐 APIs Y RATE LIMITING

### Rate Limiting:

- [ ] **Throttle en rutas públicas**:
```php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/api/public', ...);
});
```

- [ ] **Throttle en login**: Prevenir brute force
- [ ] **Throttle en registro**: Prevenir spam
- [ ] **Throttle en API**: Por usuario/IP

### API Authentication:

- [ ] **Sanctum para SPAs**: Si aplica
- [ ] **Tokens con expiración**: No tokens perpetuos
- [ ] **Revocar tokens**: Al logout
- [ ] **CORS configurado**: Solo orígenes permitidos

---

## 📊 LOGGING Y MONITOREO

### Logs de Seguridad:

- [ ] **Log de intentos de login fallidos**:
```php
Log::warning('Login fallido', [
    'email' => $request->email,
    'ip' => $request->ip(),
]);
```

- [ ] **Log de accesos no autorizados**:
```php
Log::warning('Acceso no autorizado', [
    'user_id' => auth()->id(),
    'resource' => 'invoice',
    'action' => 'delete',
]);
```

- [ ] **Log de cambios críticos**:
```php
Log::info('Factura eliminada', [
    'invoice_id' => $invoice->id,
    'user_id' => auth()->id(),
]);
```

- [ ] **No loggear datos sensibles**: Passwords, tokens, números de tarjeta

### Monitoreo:

- [ ] **Revisar logs semanalmente**: storage/logs/
- [ ] **Alertas de errores críticos**: Email/Slack (opcional)
- [ ] **Monitoreo de uptime**: UptimeRobot u similar
- [ ] **Verificar espacio en disco**: Logs pueden crecer

---

## 🔄 ACTUALIZACIONES Y MANTENIMIENTO

### Dependencias:

- [ ] **Actualizar Laravel**: Mensualmente, verificar changelog
- [ ] **Actualizar PHP**: Mantener 8.2+
- [ ] **Actualizar dependencias**:
```bash
composer update
composer audit  # Verificar vulnerabilidades
```

- [ ] **Revisar security advisories**: GitHub/Laravel News
- [ ] **No usar paquetes abandonados**: Verificar en Packagist

### Backups:

- [ ] **Backup de BD diario**: Automático
- [ ] **Backup de archivos semanal**: storage/app/
- [ ] **Probar restauración mensualmente**: Crítico
- [ ] **Almacenar fuera del servidor**: Otro lugar/cloud
- [ ] **Encriptar backups sensibles**: Si contienen datos críticos

---

## 🚀 DEPLOYMENT SEGURO

### Antes de Deploy:

- [ ] **Tests pasando**: `php artisan test`
- [ ] **Linter sin errores**: `vendor/bin/pint --test`
- [ ] **Sin debug statements**: dd(), dump(), var_dump()
- [ ] **Sin console.log()**: En JavaScript
- [ ] **Sin comentarios TODO**: O documentarlos

### Durante Deploy:

- [ ] **Modo mantenimiento**: `php artisan down`
- [ ] **Backup previo**: Antes de cualquier cambio
- [ ] **Migraciones con --force**: `php artisan migrate --force`
- [ ] **Cache optimizado**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Después de Deploy:

- [ ] **Verificar aplicación**: Probar funcionalidades críticas
- [ ] **Modo mantenimiento OFF**: `php artisan up`
- [ ] **Revisar logs**: Sin errores críticos
- [ ] **Verificar HTTPS**: Sin mixed content
- [ ] **Probar autenticación**: Login/logout funcionan

---

## 🌐 HEADERS DE SEGURIDAD

### Headers Recomendados:

**En .htaccess** (Apache):
```apache
# X-Content-Type-Options
Header always set X-Content-Type-Options "nosniff"

# X-Frame-Options
Header always set X-Frame-Options "DENY"

# X-XSS-Protection
Header always set X-XSS-Protection "1; mode=block"

# Referrer-Policy
Header always set Referrer-Policy "strict-origin-when-cross-origin"

# Permissions-Policy
Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
```

**O en middleware de Laravel**:
```php
return $next($request)
    ->header('X-Content-Type-Options', 'nosniff')
    ->header('X-Frame-Options', 'DENY')
    ->header('X-XSS-Protection', '1; mode=block');
```

Checklist:
- [ ] **X-Content-Type-Options**: nosniff
- [ ] **X-Frame-Options**: DENY o SAMEORIGIN
- [ ] **X-XSS-Protection**: 1; mode=block
- [ ] **Referrer-Policy**: strict-origin-when-cross-origin
- [ ] **Content-Security-Policy**: (opcional, avanzado)

---

## 📧 EMAIL Y NOTIFICACIONES

### Configuración:

- [ ] **MAIL_FROM_ADDRESS**: Email válido
- [ ] **MAIL_FROM_NAME**: Nombre de la aplicación
- [ ] **SPF/DKIM configurado**: Si usas email propio
- [ ] **Rate limiting en emails**: Prevenir spam
- [ ] **No enviar passwords**: Solo tokens de reset

### Contenido:

- [ ] **Links con HTTPS**: En emails
- [ ] **Tokens con expiración**: Password reset, verificación
- [ ] **Firma de emails**: Para evitar spoofing
- [ ] **Opción de unsubscribe**: En emails masivos

---

## 🔍 PRUEBAS DE SEGURIDAD

### Tests Automáticos:

- [ ] **Test de autenticación**:
```php
public function test_guest_cannot_access_dashboard()
{
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
}
```

- [ ] **Test de autorización**:
```php
public function test_user_cannot_delete_other_users_invoice()
{
    $user = User::factory()->create();
    $invoice = Invoice::factory()->create(); // De otro usuario
    
    $response = $this->actingAs($user)->delete("/invoices/{$invoice->id}");
    $response->assertForbidden();
}
```

- [ ] **Test de validación**:
```php
public function test_invoice_requires_valid_client()
{
    $response = $this->post('/invoices', [
        'client_id' => 999999,  // No existe
    ]);
    
    $response->assertSessionHasErrors('client_id');
}
```

### Pruebas Manuales:

- [ ] **Intentar SQL injection**: En formularios
- [ ] **Intentar XSS**: `<script>alert('XSS')</script>`
- [ ] **Forzar HTTPS**: Acceder con http://
- [ ] **Probar sin autenticar**: Rutas protegidas
- [ ] **Probar con otro usuario**: Acceso a datos ajenos

---

## 🚨 INCIDENTES DE SEGURIDAD

### Plan de Respuesta:

Si descubres una vulnerabilidad:

1. **No entrar en pánico**
2. **Evaluar el impacto**: ¿Qué datos se comprometieron?
3. **Parchear inmediatamente**: Fix y deploy urgente
4. **Revisar logs**: ¿Fue explotado?
5. **Notificar**: A clientes si aplica
6. **Documentar**: Lecciones aprendidas
7. **Prevenir**: Actualizar este checklist

### Contactos de Emergencia:

- **Desarrollador**: [tu email/teléfono]
- **Hosting**: [soporte del hosting]
- **Cliente**: [contacto del cliente]

---

## 📋 CHECKLIST RÁPIDO PRE-DEPLOY

**5 minutos antes de deploy**:

- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] .env NO está en Git
- [ ] Tests pasando
- [ ] Backup realizado
- [ ] HTTPS funcionando

**Si alguno falla, NO DEPLOYAR**

---

## 🎯 NIVELES DE SEGURIDAD

### Nivel 1 - CRÍTICO (Obligatorio):
- ✅ HTTPS activo
- ✅ APP_DEBUG=false
- ✅ .env seguro
- ✅ Validación de inputs
- ✅ CSRF protection

### Nivel 2 - IMPORTANTE (Altamente recomendado):
- ✅ Rate limiting
- ✅ Logs de seguridad
- ✅ Headers de seguridad
- ✅ Backups automáticos
- ✅ Tests de seguridad

### Nivel 3 - AVANZADO (Opcional):
- ⚪ WAF (Web Application Firewall)
- ⚪ 2FA (Two-Factor Authentication)
- ⚪ Penetration testing
- ⚪ Security monitoring (Sentry)
- ⚪ DDoS protection

---

## 📚 RECURSOS

### Documentación:
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)

### Herramientas:
- [Observatory by Mozilla](https://observatory.mozilla.org/)
- [Security Headers](https://securityheaders.com/)
- [SSL Labs](https://www.ssllabs.com/ssltest/)

---

## 🔄 MANTENIMIENTO DE ESTE DOCUMENTO

### Actualizar cuando:
- Se descubran nuevas vulnerabilidades
- Laravel lance security patches
- Cambien mejores prácticas
- Después de incidentes de seguridad

### Historial:
- **v1.0 (2025-10-16)**: Versión inicial

---

**Nota**: Este checklist es una guía mínima. Dependiendo de la aplicación, pueden requerirse medidas adicionales.

**Ver también**:
- `GENERAL_RULES.md` - Reglas universales
- `AI_DEVELOPMENT_GUIDELINES.md` - Desarrollo con IA
- [Laravel Security Documentation](https://laravel.com/docs/security)

