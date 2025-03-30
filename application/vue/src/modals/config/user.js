export default {
  title: 'usuario',
  controller: 'user',
  module_name: 'UsersModule',
  inputs: [
    { type: 'text', prop: 'first_name', label: 'Nombre', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], value: null },
    { type: 'text', prop: 'last_name', label: 'Apellidos', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], value: null },
    { type: 'text', prop: 'phone', label: 'Teléfono', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }, { pattern: '^(0|[1-9][0-9]*)$', is_regex: true, message: 'Introduzca un número válido', trigger: ['blur', 'change'] }], value: null },
    { type: 'select', prop: 'roles', label: 'Rol', rules: [{ required: true, message: 'Seleccione el rol de usuario', trigger: ['blur', 'change'] }], controller_name: 'groups', value: null },
    { type: 'password', prop: 'password', rules: [{ min: 8, required: true, message: 'Demasiado corta, debe tener min 8 caracteres', trigger: ['blur', 'change'] }], label: 'Contraseña', value: null },
    { type: 'text', prop: 'email', label: 'Email', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }, { pattern: "^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$", is_regex: true, message: 'Introduzca un email válido', trigger: ['blur', 'change'] }], value: null }
  ]
}
