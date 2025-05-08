<template lang="pug">
  modal(name="product" @before-open="beforeOpen" :min-width="500" height="auto" class="custom-modal" :adaptive="true" :scrollable="true")
    .modal-title
      span.modal-title-text {{action}} Producto
    .modal-body
      el-form.flex-modal-form(@submit.prevent.native="submitForm" :model="form" ref="productForm" :rules="productRules")
        .modal-form-flex-item(v-for="input in config.inputs" :key="input.prop")
          custom-input(v-if="input.type === 'text'" v-model="form[input.prop]" :label="input.label" :prop="input.prop")
          el-form-item(v-else-if="input.type === 'select'" :label="input.label" :prop="input.prop")
            el-select(v-model="form[input.prop]" :placeholder="'Seleccione el proveedor'")
              el-option(v-for="provider in providers" :key="provider.value" :label="provider.label" :value="provider.value")
          //- custom-input(v-else-if="input.type === 'file'" v-model="form[input.prop]" :label="input.label" :prop="input.prop")
    .modal-footer.space-between
      custom-button(:disabled="saving" @click.prevent.native="$modal.hide('product')" dark thin) Cancelar
      custom-button(:disabled="saving" @click.prevent.native="onSubmit('productForm')") 
        | {{ saving ? (action == 'Añadir' ? 'Añadiendo...' : 'Editando...') : action }}
</template>

<script>
import ProductApi from '@/api/ProductApi'

export default {
  name: 'product',
  data() {
    return {
      action: null,
      saving: false,
      providers: [],
      form: {},
      // Reglas de validación para el formulario, usando el valor de prop en la configuración del modal
      productRules: {
        nombre: [
          { required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] },
          { pattern: /^[A-ZÁÉÍÓÚÑa-záéíóúñ0-9\s"'\-,.]{1,100}$/, message: 'El nombre debe ser alfanumérico y tener un máximo de 100 caracteres', trigger: ['blur', 'change'] }
        ],
        codigo: [
          { required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] },
          { pattern: /^[A-Z0-9]{9}$/, message: 'El código debe tener 9 caracteres alfanuméricos', trigger: ['blur', 'change'] }
        ],
        id_provider: [
          { required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] }
        ],
        stock: [
          { required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] },
          { pattern: /^\d{1,7}$/, message: 'El stock sólo admite digitos, de 1 a 7 caracteres', trigger: 'blur' }
        ],
        precio: [
          { required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] },
          { pattern: /^\d{1,7}$/, message: 'El precio sólo admite digitos, de 1 a 7 caracteres', trigger: ['blur', 'change'] }
        ]
      },
      config: { inputs: [] } // config por defecto con un objeto vacío
    }
  },
  methods: {
    beforeOpen(event) {
      // fb
      console.log('Método beforeOpen() ejecutándose en modal product')
      // manejo de excepciones
      try {
        // importo configuraciones por defecto
        this.config = require('@/modals/config/product').default
        // fb
        console.log('Configuración product.js cargada en el modal')
      } catch (error) {
        // fb
        console.error('Error al cargar la configuración por defecto para el modal: ', error)
        // seteo a vacio en caso de excepcion
        this.config = { inputs: [] }
      }

      // aseguro valores por defecto en form
      console.log('Valores por defecto cargados this.form = config.inputs')
      this.resetForm()

      //! request API para obtener los proveedores
      ProductApi.getProvidersDropdown()
        .then(response => {
          this.providers = response.data // Poblar la lista de proveedores
          console.log('Proveedores cargados:', this.providers)
        })
        .catch(error => {
          console.error('Error al obtener los proveedores:', error)
        })

      // compruebo si el event.params trae un objeto product
      if (event.params && event.params.product) {
        // fb
        console.log('Existe un objeto product en el event.params > action = editar && this.form = event.params.product')
        // entonces la accón es editar
        this.action = 'Editar'
        // extraigo al form los datos del objeto
        this.form = { ...event.params.product }
      } else {
        // si el event.params no trae product va ser añadir, el form contiene los datos por defecto en configuración []
        console.log('No existe un objeto product en el event.params > action = añadir')
        // seteo la accion
        this.action = 'Añadir'
      }
    },
    // resetForm - setea el form con los valores por defecto en config, propiedad del componente
    resetForm() {
      // si no son null o undefined
      if (this.config && this.config.inputs) {
        // seteo flag
        this.saving = false
        // setea el form con los valores por defecto this.config = {this.inputs : []}
        this.form = this.config.inputs.reduce((acc, input) => {
          acc[input.prop] = input.value
          return acc
        }, {})
      }
    },
    onSubmit(formName) {
      // fb
      console.log('Método Onsubmit() ejecutándose')
      // validación ref=productForm | ref definido como atributo del elemento form del modal
      this.$refs[formName].validate((valid) => {
        if (valid) {
          // set flag
          this.saving = true
          // evaluo accion para controlar la operación
          if (this.action === 'Añadir') {
            console.log('Enviando solicitud create(this.form) al back través de la ProductApi:', this.form)
            // lanza la promesa con el BaseApiCalls y los datos del form
            ProductApi.create(this.form).then(() => {
              // setea flag
              this.saving = false
              // esconde el modal
              this.$modal.hide('product')
              // emite evento por bus para hacer el fetch
              console.log('Operación completada con éxito, emitiendo recargarListaProductos por el bus')
              console.log('Form enviado: ' + this.form.data)
              this.$bus.$emit('recargarListaProductos')
            }).catch(() => {
              console.log('Catch en Onsubmit() - create')
              // evita el cargando freeze
              this.saving = false
            })
          } else {
            // se repite la logica, esta vez updateando
            console.log('Enviando solicitud update(this.form) al back a través de la ProductApi:', this.form)
            ProductApi.update(this.form).then(() => {
              this.saving = false
              this.$modal.hide('product')
              console.log('Operación completada con éxito, emitiendo recargarListaProductos por el bus')
              this.$bus.$emit('recargarListaProductos')
            }).catch(() => {
              console.log('Catch en Onsubmit() - update')
              this.saving = false
            })
          }
        }
      })
    }
  }
}
</script>

<style lang="sass" scoped>
  .modal-footer
    text-align: right
</style>