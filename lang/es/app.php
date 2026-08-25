<?php

return [

    // Dashboard
    'dashboard' => 'Dashboard',
    'welcome' => 'Bienvenido, :name',
    'card_role' => 'Rol',
    'card_permissions' => 'Permisos',
    'card_environment' => 'Entorno',

    // Formulario de usuarios
    'user_photo' => 'Foto de perfil',
    'user_photo_preview' => 'Vista previa',
    'user_first_name' => 'Nombre',
    'user_first_name_ph' => 'Ej. Juan',
    'user_last_name' => 'Apellido',
    'user_last_name_ph' => 'Ej. García',
    'user_username' => 'Usuario',
    'user_username_ph' => 'Ej. juan_garcia',
    'user_email' => 'Correo electrónico',
    'user_email_ph' => 'correo@ejemplo.com',
    'user_password' => 'Contraseña',
    'user_new_password' => 'Nueva contraseña',
    'user_password_ph' => 'Mínimo 8 caracteres',
    'user_password_hint' => 'Dejar vacío para mantener la actual',
    'user_confirm' => 'Confirmar contraseña',
    'user_confirm_ph' => 'Repite la contraseña',
    'user_role' => 'Rol',
    'user_role_ph' => 'Sin rol asignado',
    'user_role_hint_new' => 'Seleccionar un rol carga sus permisos automáticamente.',
    'user_role_hint_edit' => 'Cambiar el rol reemplazará los permisos actuales.',
    'user_permissions' => 'Permisos del usuario',
    'user_permissions_desc' => 'Personaliza los accesos de este usuario.',
    'user_restore_perms' => 'Restaurar permisos originales',
    'user_btn_create' => 'Crear usuario',
    'user_btn_update' => 'Actualizar',

    // Formulario de roles
    'role_name' => 'Nombre del rol',
    'role_name_ph' => 'Ej. Administrador de CEDIS',
    'role_name_hint' => 'Escribe el nombre como se mostrará en la interfaz.',
    'role_identifier' => 'Identificador',
    'role_permissions' => 'Permisos del rol',
    'role_permissions_desc' => 'Selecciona los accesos que tendrá este rol.',
    'role_btn_save' => 'Guardar',

    // Toasts de usuario
    'user_perms_restored' => 'Permisos restaurados',
    'user_perms_restored_desc' => 'Se restauraron los permisos originales del usuario.',
    'user_perms_loaded' => 'Permisos cargados',
    'user_perms_loaded_desc' => 'Se cargaron los permisos de ":name".',
    'user_saved' => 'Éxito',
    'user_created_desc' => 'Usuario creado correctamente.',
    'user_updated_desc' => 'Usuario actualizado correctamente.',

    // Acciones comunes
    'cancel' => 'Cancelar',
    'success' => 'Éxito',
    'error' => 'Error',
    'user_protected' => 'Este usuario está protegido y no se puede eliminar.',
    'not_found' => 'No se encontró el :model. Puede que otra persona lo haya borrado.',
    'soft_deleted' => ':model eliminado correctamente.',
    'restored' => ':model restaurado correctamente.',
    'user_deactivated' => 'Usuario desactivado correctamente.',
    'user_activated' => 'Usuario activado correctamente.',
    'user_status' => 'Estado de la cuenta',
    'user_status_active' => 'Activo',
    'user_status_hint' => 'Un usuario desactivado no podrá iniciar sesión.',

    // Diálogos roles
    'role_delete_title' => '¿Eliminar rol?',
    'role_delete_desc' => 'Se eliminará el rol ":name" de forma permanente.',
    'role_delete_confirm' => 'Eliminar',
    'role_delete_cancel' => 'Cancelar',
    'role_delete_error' => 'No se puede eliminar',
    'role_delete_has_users' => 'El rol ":name" tiene usuarios asignados.',
    'role_deleted' => 'Rol eliminado',
    'role_deleted_desc' => 'El rol fue eliminado correctamente.',

];
