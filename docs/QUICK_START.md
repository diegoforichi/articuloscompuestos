# Inicio Rápido

## 🚀 3 Pasos para Empezar

### 1. Leer Documentos Clave (15 min)

```
📖 Lectura obligatoria:
└─ docs/GENERAL_RULES.md (10 min)
└─ docs/AI_DEVELOPMENT_GUIDELINES.md (5 min)
```

### 2. Configurar Proyecto (5 min)

```bash
# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate
```

### 3. Empezar a Desarrollar

```bash
# Modo desarrollo
composer run dev

# O manualmente
php artisan serve
npm run dev
```

---

## 📋 Reglas Esenciales (memorizar)

### ❌ NUNCA:
1. Hardcodear textos en vistas
2. Ejecutar composer/npm en servidor
3. Usar SQLite en producción
4. JavaScript complejo sin justificar
5. Deploy sin tests

### ✅ SIEMPRE:
1. Usar `__('models.xxx')` para textos
2. Documentar con PHPDoc
3. Incluir tests
4. Mantener simple
5. MySQL en producción

---

## 🎯 Atajos de Desarrollo

### Crear CRUD completo:

```bash
# 1. Modelo + Migración + Factory
php artisan make:model Product -mf

# 2. Controlador
php artisan make:controller ProductController --resource

# 3. Form Request
php artisan make:request StoreProductRequest

# 4. Test
php artisan make:test ProductTest

# 5. Agregar a models.php
# resources/lang/es/models.php
'product' => [
    'singular' => 'Producto',
    'plural' => 'Productos',
    'article' => 'el',
    'article_plural' => 'los',
],

# 6. Crear vistas con traducciones
# resources/views/products/index.blade.php
```

---

## 🧪 Verificación Rápida

```bash
# Tests
php artisan test

# Formateo
vendor/bin/pint

# Sin hardcode
grep -r "Productos\|Clientes\|Facturas" resources/views/
```

---

## 📚 Documentos por Situación

| Situación | Documento |
|-----------|-----------|
| Empezar proyecto nuevo | GENERAL_RULES.md |
| Usar asistente IA | AI_DEVELOPMENT_GUIDELINES.md |
| Hacer deploy | SECURITY_CHECKLIST.md |
| Ver stack del proyecto | CONTEXT.md |
| Cambiar nombre de modelo | i18n-rules.md |
| Duda sobre código | code-conventions.md |

---

## ⚡ Comandos Más Usados

```bash
# Desarrollo
composer run dev

# Tests
php artisan test

# Crear cosas
php artisan make:model Product -mf
php artisan make:controller ProductController --resource
php artisan make:request StoreProductRequest
php artisan make:test ProductTest

# Deploy (local)
npm run build
composer install --no-dev
php artisan config:cache

# Deploy (servidor)
php artisan migrate --force
chmod -R 755 storage/ bootstrap/cache/
```

---

## 🎯 Checklist Diario

Al empezar a trabajar:
- [ ] Leer reglas si es primera vez
- [ ] Pull del repo
- [ ] Tests pasando
- [ ] Entorno local funcionando

Al terminar:
- [ ] Código documentado
- [ ] Tests incluidos
- [ ] Sin hardcode
- [ ] Commit descriptivo

---

**Tiempo total de setup**: ~20 minutos  
**Siguiente**: Desarrollar siguiendo las reglas 🚀

