export default {
  title: 'cliente',
  controller: 'client',
  module_name: 'ClientsModule',
  inputs: [
    { type: 'text', prop: 'nombre', label: 'Nombre', rules: [
      { required: true, message: 'El campo es obligatorio', trigger: 'blur' },
      { pattern: /^[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s"'\-,]{1,100}$/, message: 'El nombre debe tener entre 1 y 100 caracteres y puede incluir tildes, comillas y espacios', trigger: 'blur' }
    ], value: null },
    { type: 'text', prop: 'email', label: 'Email', rules: [
      { required: true, message: 'El campo es obligatorio', trigger: 'blur' },
      { type: 'email', message: 'El email no es válido', trigger: 'blur' }
    ], value: null },
    { type: 'text', prop: 'apellido', label: 'Apellido', rules: [
      { required: true, message: 'El campo es obligatorio', trigger: 'blur' },
      { pattern: /^[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s"'\-,]{1,100}$/, message: 'El apellido debe tener entre 1 y 100 caracteres y puede incluir tildes, comillas y espacios', trigger: 'blur' }
    ], value: null },
    { type: 'text', prop: 'direccion', label: 'Direccion', rules: [
      { required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] },
      { pattern: /^[A-ZÁÉÍÓÚÑa-záéíóúñ\s]{1,500}$/, message: 'La dirección debe contener sólo letras y tener un máximo de 500 caracteres', trigger: ['blur', 'change'] }
    ], value: null }

  ]
}
