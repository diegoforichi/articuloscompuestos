# Reglas del Proyecto - Panel de Facturas Laravel

Este directorio contiene todas las reglas y convenciones que el asistente de IA debe seguir al trabajar en este proyecto.

## 📁 Archivos de Reglas

### 1. `project-rules.md` ⭐
**Propósito**: Reglas generales del proyecto, filosofía y limitaciones críticas.

**Incluye**:
- Filosofía del proyecto (simplicidad, mantenibilidad, efectividad)
- Limitaciones del servidor (hosting compartido)
- Stack tecnológico permitido
- Restricciones de JavaScript
- Estrategia de dependencias
- **Reglas de internacionalización (i18n)**
- Enfoque de soluciones
- Comportamiento del asistente

**Cuándo consultar**: SIEMPRE antes de sugerir cualquier solución.

---

### 2. `deployment-rules.md`
**Propósito**: Reglas específicas para el proceso de despliegue.

**Incluye**:
- Proceso de despliegue completo
- Directorios a subir (vendor/, assets compilados)
- Configuración de producción
- Restricciones del servidor
- Checklist de despliegue

**Cuándo consultar**: Al preparar o ejecutar despliegues.

---

### 3. `technical-context.md`
**Propósito**: Contexto técnico completo del proyecto y servidor.

**Incluye**:
- Información del servidor (specs, software)
- Limitaciones importantes (SQLite viejo, MySQL 5.7)
- Stack tecnológico detallado
- Estructura del proyecto
- Configuración de entornos
- Dependencias instaladas

**Cuándo consultar**: Al trabajar con configuración, base de datos o dependencias.

---

### 4. `code-conventions.md`
**Propósito**: Convenciones de código y estilo.

**Incluye**:
- Comentarios obligatorios (PHPDoc)
- Estructura de código (controladores, modelos)
- Frontend (Blade + Tailwind)
- **Ejemplos con sistema de traducciones**
- Restricciones de JavaScript
- Buenas prácticas
- Convenciones de nombres

**Cuándo consultar**: Al escribir o modificar código.

---

### 5. `i18n-rules.md` ⭐ NUEVO
**Propósito**: Reglas de internacionalización y traducciones.

**Incluye**:
- **Regla CRÍTICA: NUNCA hardcodear textos**
- Estructura de archivos de idioma
- Uso de traducciones en vistas
- Helpers personalizados
- Proceso de cambio de nombres
- Ejemplos completos

**Cuándo consultar**: SIEMPRE al crear vistas, controladores o cualquier interfaz.

---

## 🎯 Principios Fundamentales

### 1. SIMPLICIDAD PRIMERO
- Buscar siempre la solución más simple
- Evitar sobre-ingeniería
- No agregar complejidad innecesaria

### 2. MANTENIBILIDAD
- Código fácil de entender
- Documentación completa
- Estructura clara

### 3. EFECTIVIDAD
- Soluciones que funcionen
- Sin dependencias externas
- Auto-contenido

### 4. AUTONOMÍA
- No depender de instalaciones en servidor
- Subir vendor/ completo
- Assets compilados localmente

### 5. INTERNACIONALIZACIÓN
- **NUNCA hardcodear textos**
- Usar sistema de traducciones
- Cambios centralizados

---

## 🚨 Recordatorios Críticos

### Base de Datos:
- ❌ SQLite es VIEJO - NO usar en producción
- ✅ MySQL 5.7.23 - SÍ usar en producción

### Dependencias:
- ❌ NO ejecutar composer en servidor
- ✅ Subir vendor/ completo
- ❌ NO ejecutar npm en servidor
- ✅ Compilar assets localmente

### JavaScript:
- ❌ NO Vanilla JavaScript complejo
- ❌ NO frameworks pesados
- ✅ SÍ Livewire (sin JavaScript)
- ✅ SÍ Alpine.js (solo si es necesario)

### Internacionalización (NUEVO):
- ❌ **NO hardcodear textos en vistas**
- ✅ **SÍ usar `__('models.xxx')`**
- ✅ **SÍ crear archivos de idioma**
- ✅ **SÍ cambios centralizados**

### Hosting:
- Compartido con recursos limitados
- Apache 2.4.59, PHP 8.2.12, MySQL 5.7.23
- Sin Node.js, sin Docker

---

## 🤝 Comportamiento del Asistente

### SIEMPRE:
- ✅ Ofrecer múltiples soluciones con pros/contras
- ✅ Explicar y justificar recomendaciones
- ✅ Priorizar simplicidad y mantenibilidad
- ✅ Considerar limitaciones del servidor
- ✅ Documentar todo el código
- ✅ Usar Laravel nativo cuando sea posible
- ✅ Verificar que no requiera instalaciones externas
- ✅ **Usar sistema de traducciones (NUNCA hardcodear textos)**
- ✅ **Crear archivos de idioma para nuevos modelos**

### NUNCA:
- ❌ Sugerir soluciones complejas sin justificación
- ❌ Usar JavaScript complejo sin necesidad
- ❌ Ignorar limitaciones del hosting
- ❌ Agregar dependencias que requieran instalación
- ❌ Sobre-ingenierizar soluciones simples
- ❌ Omitir documentación
- ❌ Sugerir compilación en servidor
- ❌ **Hardcodear textos en vistas o controladores**
- ❌ **Crear vistas sin usar sistema de traducciones**

---

## 📚 Cómo Usar estas Reglas

### Para el Asistente de IA:
1. **Leer SIEMPRE** antes de responder
2. **Consultar** el archivo relevante según el contexto
3. **Aplicar** las reglas y convenciones
4. **Justificar** las decisiones tomadas
5. **Ofrecer alternativas** cuando sea apropiado

### Para el Desarrollador:
1. **Mantener actualizadas** las reglas
2. **Consultar** cuando haya dudas
3. **Actualizar** cuando cambien requisitos
4. **Compartir** con el equipo

---

## 🌍 Sistema de Traducciones (NUEVO)

### Archivos de idioma creados:
```
resources/lang/es/
├── models.php        # Nombres de modelos
├── navigation.php    # Menús y navegación
├── actions.php       # Acciones CRUD
├── messages.php      # Mensajes generales
└── attributes.php    # Atributos/campos
```

### Ejemplo de uso:
```blade
{{-- ✅ BIEN --}}
<h1>{{ __('models.product.plural') }}</h1>
<button>{{ __('actions.create') }} {{ __('models.product.singular') }}</button>

{{-- ❌ MAL --}}
<h1>Productos</h1>
<button>Crear Producto</button>
```

### Beneficio:
Cambiar "Productos" → "Artículos" = **editar 1 archivo** (models.php)

---

## 🔄 Actualización de Reglas

**Cuándo actualizar**:
- Cambios en el servidor o hosting
- Nuevas tecnologías adoptadas
- Cambios en la arquitectura
- Nuevas restricciones o requisitos
- Nuevos modelos o funcionalidades

**Cómo actualizar**:
1. Editar el archivo relevante
2. Mantener el formato y estructura
3. Documentar el cambio
4. Notificar al equipo

---

## 📞 Contacto

Si tienes dudas sobre estas reglas o necesitas actualizarlas, consulta con el equipo de desarrollo.

**Última actualización**: 2025-10-16 (agregado sistema de traducciones)
