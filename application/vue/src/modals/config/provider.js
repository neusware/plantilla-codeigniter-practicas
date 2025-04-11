export default {
  title: 'proveedor',
  controller: 'provider',
  module_name: 'ProvidersModule',
  inputs: [
    { type: 'text', prop: 'nombre', label: 'Nombre', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], value: null },
    { type: 'text', prop: 'cif', label: 'CIF', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }], value: null },
    { type: 'text', prop: 'email', label: 'Email', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }, { pattern: "^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$", is_regex: true, message: 'Introduzca un email válido', trigger: ['blur', 'change'] }], value: null },
    { type: 'text', prop: 'phone', label: 'Teléfono', rules: [{ required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }, { pattern: '^(0|[1-9][0-9]*)$', is_regex: true, message: 'Introduzca un número válido', trigger: ['blur', 'change'] }], value: null },
    { type: 'file', prop: 'imagen', label: 'Imagen', rules: [], value: null }
  ]
}
