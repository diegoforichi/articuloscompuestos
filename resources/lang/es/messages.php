<?php

/**
 * Traducciones de mensajes generales
 *
 * Este archivo contiene mensajes generales de la aplicación
 * que no pertenecen a otras categorías específicas.
 */

return [
    // Bienvenida
    'welcome' => 'Bienvenido',
    'welcome_back' => 'Bienvenido de nuevo',
    'hello' => 'Hola',
    'goodbye' => 'Adiós',

    // Mensajes generales
    'loading' => 'Cargando...',
    'processing' => 'Procesando...',
    'please_wait' => 'Por favor espere...',
    'no_results' => 'No se encontraron resultados',
    'no_data' => 'No hay datos disponibles',
    'empty' => 'Vacío',
    'none' => 'Ninguno',
    'all' => 'Todos',
    'yes' => 'Sí',
    'no' => 'No',
    'optional' => 'Opcional',
    'required' => 'Requerido',

    // Fechas y tiempo
    'today' => 'Hoy',
    'yesterday' => 'Ayer',
    'tomorrow' => 'Mañana',
    'date' => 'Fecha',
    'time' => 'Hora',
    'datetime' => 'Fecha y Hora',
    'created_at' => 'Creado el',
    'updated_at' => 'Actualizado el',
    'deleted_at' => 'Eliminado el',

    // Estados
    'active' => 'Activo',
    'inactive' => 'Inactivo',
    'pending' => 'Pendiente',
    'completed' => 'Completado',
    'cancelled' => 'Cancelado',
    'approved' => 'Aprobado',
    'rejected' => 'Rechazado',
    'draft' => 'Borrador',
    'published' => 'Publicado',

    // Validación
    'field_required' => 'Este campo es requerido',
    'invalid_input' => 'Entrada inválida',
    'invalid_email' => 'Correo electrónico inválido',
    'invalid_phone' => 'Teléfono inválido',
    'invalid_url' => 'URL inválida',
    'invalid_date' => 'Fecha inválida',
    'passwords_must_match' => 'Las contraseñas deben coincidir',
    'min_length' => 'Longitud mínima: :min caracteres',
    'max_length' => 'Longitud máxima: :max caracteres',

    // Permisos
    'permission_denied' => 'Permiso denegado',
    'unauthorized_access' => 'Acceso no autorizado',
    'forbidden_action' => 'Acción prohibida',

    // Sesión
    'session_expired' => 'Sesión expirada',
    'please_login' => 'Por favor inicie sesión',
    'logged_out' => 'Sesión cerrada exitosamente',

    // Errores comunes
    'error_occurred' => 'Ocurrió un error',
    'something_went_wrong' => 'Algo salió mal',
    'try_again' => 'Inténtelo de nuevo',
    'contact_support' => 'Contacte al soporte',

    // Confirmaciones
    'changes_saved' => 'Cambios guardados',
    'changes_discarded' => 'Cambios descartados',
    'unsaved_changes' => 'Tiene cambios sin guardar',
    'confirm_leave' => '¿Está seguro de salir? Los cambios no guardados se perderán',

    // Dashboard
    'dashboard' => 'Dashboard',
    'statistics' => 'Estadísticas',
    'overview' => 'Resumen',
    'recent_activity' => 'Actividad Reciente',
    'quick_actions' => 'Acciones Rápidas',

    'tenant' => [
        'section' => 'Tenant',
        'recalculation_mode' => 'Modo de recálculo',
        'recalculation_manual' => 'Manual',
        'recalculation_automatic' => 'Automático',
        'onboarding_section' => 'Onboarding admin tenant',
        'onboarding_description' => 'Opcional al crear tenant. Si completás un campo, debés completar nombre, email y contraseña.',
        'admin_name' => 'Nombre admin',
        'admin_email' => 'Email admin',
        'admin_password' => 'Contraseña admin',
        'onboarding_incomplete' => 'Para crear el usuario admin del tenant, completá nombre, email y contraseña.',
        'onboarding_email_exists' => 'Ese email ya existe. Usá otro para el admin del tenant.',
        'onboarding_success_title' => 'Tenant y admin creados',
        'onboarding_success_body' => 'Se completó el onboarding básico del tenant.',
        'no_users_badge' => 'Sin usuarios',
        'users_relation_title' => 'Usuarios del tenant',
        'users_relation_hint' => 'Gestioná usuarios del tenant desde esta pestaña.',
    ],

    'stock_movement' => [
        'reference' => [
            'stock_entry' => 'Entrada #:id',
            'consumption_product' => 'Consumo producto :code',
        ],
        'reference_column' => 'Referencia',
        'technical_reference' => 'Ref. técnica',
    ],

    'products' => [
        'consume_modal_heading' => 'Consumir stock',
        'consume_modal_description' => 'Descuenta insumos según la composición del producto. Por cada unidad consumida, se multiplica la cantidad de cada componente en la receta.',
        'consume_units_helper' => 'Indicá cuántas unidades del producto querés consumir.',
        'consume_preview_empty' => 'Este producto no tiene componentes en la composición.',
        'consume_preview_line' => ':name: :quantity :unit',
    ],

    'integration' => [
        'tenant_already_configured' => 'Este tenant ya tiene una integración configurada. Editá la existente o elegí otro tenant.',
        'no_tenant_available_title' => 'Sin tenants disponibles',
        'no_tenant_available_body' => 'Todos los tenants ya tienen integración. Solo puede existir una integración por tenant.',
        'tenant_select_helper' => 'Cada tenant admite una sola configuración de integración.',
        'active_toggle_helper' => 'Solo una configuración activa por tenant. Activar esta desactiva otras del mismo tenant si aplica.',
    ],
];
