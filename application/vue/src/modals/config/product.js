export default {
  title: 'producto',
  controller: 'product',
  module_name: 'ProductsModule',
  inputs: [
    { type: 'select', prop: 'id_provider', label: 'Proveedor', rules: [
      { required: true, message: 'El id del proveedor es obligatorio', trigger: 'blur' },
      { min: 1, max: 5, message: 'El id del proveedor debe tener entre 1 y 5 caracteres', trigger: 'blur' }
    ], value: null },
    { type: 'text', prop: 'nombre', label: 'Nombre', rules: [
      { required: true, message: 'El nombre es obligatorio', trigger: 'blur' },
      { pattern: /^[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s"'\-,]{1,100}$/, message: 'El nombre debe tener entre 1 y 100 caracteres y puede incluir tildes, comillas y espacios', trigger: 'blur' }
    ], value: null },
    { type: 'text', prop: 'codigo', label: 'Código', rules: [
      { required: true, message: 'El código es obligatorio', trigger: 'blur' },
      { pattern: /^[A-Z0-9]{9}$/, message: 'El código debe tener 9 caracteres alfanuméricos', trigger: 'blur' }
    ], value: null },
    { type: 'text', prop: 'stock', label: 'Stock', rules: [
      { required: true, message: 'El stock es obligatorio', trigger: 'blur' },
      { pattern: /^[0-9]{1,7}$/, message: 'El stock sólo admite digitos, de 1 a 7 caracteres', trigger: 'blur' }
    ], value: null },
    { type: 'text', prop: 'precio', label: 'Precio', rules: [
      { required: true, message: 'El precio es obligatorio', trigger: 'blur' },
      { pattern: /^[0-9]{1,7}$/, message: 'El precio debe tener entre 1 y 7 dígitos', trigger: 'blur' }
    ], value: null }

  ]
}
