# Índice de Documentación

## 📋 Guía Rápida

¿Qué documento necesitas?

---

## 🚀 Inicio Rápido

### ¿Eres nuevo en el proyecto?
👉 **Leer primero**: [README.md](../README.md)

### ¿Vas a desarrollar?
👉 **Leer**: [GENERAL_RULES.md](GENERAL_RULES.md)  
👉 **Leer**: [AI_DEVELOPMENT_GUIDELINES.md](AI_DEVELOPMENT_GUIDELINES.md)

### ¿Vas a hacer deploy?
👉 **Leer**: [GENERAL_RULES.md#deployment](GENERAL_RULES.md)  
👉 **Verificar**: [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md)

### ¿Qué tecnologías usa?
👉 **Ver**: [CONTEXT.md](CONTEXT.md)

---

## 📚 Documentos Universales

### 1. [GENERAL_RULES.md](GENERAL_RULES.md) ⭐⭐⭐
**Propósito**: Reglas base para TODOS los proyectos Laravel en hosting compartido.

**Incluye**:
- Filosofía de desarrollo
- Stack tecnológico
- Limitaciones del hosting
- Base de datos
- Internacionalización
- Documentación
- Validación
- Testing
- Performance
- Control de versiones
- Deployment
- Backups

**Cuándo usar**: SIEMPRE, en cualquier proyecto Laravel.

**Copiar a nuevos proyectos**: ✅ SÍ

---

### 2. [AI_DEVELOPMENT_GUIDELINES.md](AI_DEVELOPMENT_GUIDELINES.md) ⭐⭐⭐
**Propósito**: Guía para trabajar con asistentes de IA sin errores.

**Incluye**:
- Problemas comunes de IA (alucinaciones, sobre-ingeniería)
- Estrategias de mitigación
- Prompts efectivos
- Checklist de verificación
- Code review post-IA
- Red flags

**Cuándo usar**: Al desarrollar con Claude, ChatGPT, Copilot, Cursor.

**Copiar a nuevos proyectos**: ✅ SÍ

---

### 3. [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md) ⭐⭐⭐
**Propósito**: Checklist de seguridad antes de cada deploy.

**Incluye**:
- Configuración inicial
- Protecciones de Laravel
- Autenticación/Autorización
- Validación
- APIs y rate limiting
- Headers de seguridad
- Plan de respuesta a incidentes

**Cuándo usar**: SIEMPRE antes de deploy a producción.

**Copiar a nuevos proyectos**: ✅ SÍ

---

### 4. [CONTEXT.md](CONTEXT.md) ⚠️
**Propósito**: Resumen rápido del proyecto ACTUAL.

**Incluye**:
- Información específica del proyecto
- Stack tecnológico usado
- Flujo de trabajo
- Recordatorios del proyecto

**Cuándo usar**: Para referencia rápida del proyecto.

**Copiar a nuevos proyectos**: ⚠️ NO (crear uno nuevo por proyecto)

---

## 🔧 Reglas Técnicas (Cursor)

### En `.cursor/rules/`:

- **[project-rules.md](../.cursor/rules/project-rules.md)**
  - Reglas específicas del proyecto
  - Filosofía y limitaciones
  - Stack y comportamiento del asistente

- **[i18n-rules.md](../.cursor/rules/i18n-rules.md)** ⭐
  - Sistema de traducciones
  - NUNCA hardcodear textos
  - Cambios centralizados

- **[code-conventions.md](../.cursor/rules/code-conventions.md)**
  - Convenciones de código
  - PHPDoc obligatorio
  - Ejemplos de código correcto

- **[deployment-rules.md](../.cursor/rules/deployment-rules.md)**
  - Proceso de deployment
  - Qué subir al servidor
  - Configuración de producción

- **[technical-context.md](../.cursor/rules/technical-context.md)**
  - Información del servidor
  - Limitaciones técnicas
  - Configuración de entornos

---

## 🎯 Flujo de Trabajo Recomendado

### Para Nuevo Proyecto:

```
1. Copiar estos 3 documentos universales:
   - docs/GENERAL_RULES.md
   - docs/AI_DEVELOPMENT_GUIDELINES.md
   - docs/SECURITY_CHECKLIST.md

2. Crear docs/CONTEXT.md específico del nuevo proyecto

3. Configurar .cursor/rules/ según necesidad

4. Leer GENERAL_RULES.md completo

5. Empezar desarrollo
```

### Para Desarrollo:

```
1. Leer reglas relevantes
2. Desarrollar siguiendo reglas
3. Usar asistente IA con prompts efectivos
4. Verificar código generado
5. Tests automáticos
6. Code review
7. Commit
```

### Para Deploy:

```
1. Verificar SECURITY_CHECKLIST.md
2. Seguir proceso en GENERAL_RULES.md
3. Backup antes de cambios
4. Deploy
5. Verificar aplicación
6. Documentar cambios
```

---

## 🌍 Sistema de Traducciones

**Archivos de idioma**: `resources/lang/es/`

- `models.php` - Nombres de modelos
- `navigation.php` - Menús
- `actions.php` - Acciones CRUD
- `messages.php` - Mensajes generales
- `attributes.php` - Campos/atributos

**Uso**:
```blade
{{ __('models.product.plural') }}
{{ __('actions.create') }}
{{ __('messages.welcome') }}
```

**Beneficio**: Cambiar "Productos" → "Artículos" = editar 1 archivo

---

## 🔒 Seguridad

### Checklist Rápido:

- [ ] APP_DEBUG=false en producción
- [ ] .env NO está en Git
- [ ] HTTPS activo
- [ ] Tests de seguridad pasando
- [ ] Backup reciente

Ver **[docs/SECURITY_CHECKLIST.md](docs/SECURITY_CHECKLIST.md)** completo.

---

## 📦 Dependencias

### Composer (PHP):
```bash
composer install              # Desarrollo
composer install --no-dev    # Producción
composer update              # Actualizar
```

### NPM (JavaScript):
```bash
npm install     # Instalar
npm run dev     # Desarrollo
npm run build   # Producción (compilar)
```

**IMPORTANTE**: En hosting compartido, subir `vendor/` y `public/build/` completos.

---

## 🎯 Características del Proyecto Base

- ✅ Laravel 12 con estructura moderna
- ✅ Sistema de traducciones configurado
- ✅ Reglas completas de desarrollo
- ✅ Guías de seguridad
- ✅ Optimizado para hosting compartido
- ✅ Documentación completa
- ✅ Listo para desarrollo con IA

---

## 📞 Contacto

- **Desarrollador**: [tu email]
- **Hosting**: [soporte hosting]
- **Documentación**: Ver carpeta `docs/`

---

## 📄 Licencia

Proyecto privado y propietario. Todos los derechos reservados.

---

**Última actualización**: 2025-10-16

**Próximos pasos**: Leer `docs/GENERAL_RULES.md` y empezar a desarrollar 🚀

