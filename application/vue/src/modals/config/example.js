export default {
  title: 'titulo',
  controller: 'CONTROLLER_AQUI',
  // module_name: 'UsersModule',
  inputs: [
    { type: 'text', prop: 'name', label: 'Nombre', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], value: null },
    { type: 'text', prop: 'description', label: 'Email', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }, { pattern: "^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$", is_regex: true, message: 'Introduzca un email válido', trigger: ['blur', 'change'] }], value: null },
    { type: 'select', prop: 'maintenance_access', label: 'Acceso en mantenimiento', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], options: [{ label: 'Si', value: 1 }, { label: 'No', value: 0 }], value: null },
    { type: 'number', prop: 'prueba1', label: 'Numero', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], value: null },
    { type: 'select', prop: 'prueba2', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], controller_name: 'user', label: 'Usuario', module_name: 'UsersModule', value: null },
    { type: 'textarea', prop: 'prueba3', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], label: 'textarea', value: null },
    { type: 'checkbox', prop: 'prueba4', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], label: 'checkbox', value: null },
    { type: 'file', prop: 'prueba5', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], label: 'file', value: null },
    { type: 'password', prop: 'prueba6', rules: [{ min: 8, message: 'Demasiado corta, debe tener min 8 caracteres', trigger: ['blur', 'change'] }], label: 'password', value: null }
  ]
}
