<template lang="pug">
  modal(name="provider" @before-open="beforeOpen" :min-width="500" height="auto" class="custom-modal" :adaptive="true" :scrollable="true")
    .modal-title
        span.modal-title-text {{action}} Proveedor
    .modal-body
      el-form.flex-modal-form(@submit.prevent.native="submitForm" :model="form" ref="providerForm" :rules="providerRules")
        .modal-form-flex-item(v-for="input in config.inputs" :key="input.prop")
          custom-input(v-if="input.type === 'text'" v-model="form[input.prop]" :label="input.label" :prop="input.prop")
          //- custom-input(v-else-if="input.type === 'file'" v-model="form[input.prop]" :label="input.label" :prop="input.prop")

    .modal-footer.space-between
      custom-button(:disabled="saving" @click.prevent.native="$modal.hide('provider')" dark thin) Cancelar
      custom-button(:disabled="saving" @click.prevent.native='onSubmit("providerForm")') {{(saving ? (action == 'Añadir' ? 'Añadiendo...' : 'Editando...') : action)}}
</template>

<script>
import ProviderApi from '@/api/ProviderApi'

export default {
  name: 'provider',
  data() {
    return {
      action: null,
      saving: false,
      form: {},
      // Reglas de validación para el formulario, usando el valor de prop en la configuración del modal
      providerRules: {
        nombre: [
          { required: true, message: 'El nombre es obligatorio', trigger: 'blur' },
          { min: 3, max: 50, message: 'El nombre debe tener entre 3 y 50 caracteres', trigger: 'blur' }
        ],
        cif: [
          { required: true, message: 'El CIF es obligatorio', trigger: 'blur' },
          { pattern: /^[A-Z0-9]{9}$/, message: 'El CIF debe tener 9 caracteres alfanuméricos', trigger: 'blur' }
        ],
        email: [
          { required: true, message: 'El email es obligatorio', trigger: 'blur' },
          { type: 'email', message: 'El email no es válido', trigger: 'blur' }
        ],
        phone: [
          { required: true, message: 'El teléfono es obligatorio', trigger: 'blur' },
          { pattern: /^[0-9]{9}$/, message: 'El teléfono debe tener 9 dígitos', trigger: 'blur' }
        ]
      },
      config: { inputs: [] } // config por defecto con un objeto vacío
    }
  },
  methods: {
    beforeOpen(event) {
      // fb
      console.log('Método beforeOpen() ejecutándose en modal provider')
      // manejo de excepciones
      try {
        // importo configuraciones
        this.config = require('@/modals/config/provider').default
        // fb
        console.log('Configuración provider.js cargada en el modal')
      } catch (error) {
        // fb
        console.error('Error al cargar la configuración por defecto para el modal: ', error)
        // seteo a vacio en caso de excepcion
        this.config = { inputs: [] }
      }

      // aseguro valores por defecto en form
      console.log('Valores por defecto cargados this.form = config.inputs')
      this.resetForm()

      // compruebo si el event.params trae un objeto provider
      if (event.params && event.params.provider) {
        // fb
        console.log('Existe un objeto provider en el event.params > action = editar && this.form = event.params.provider')
        // entonces la accón es editar
        this.action = 'Editar'
        // extraigo al form los datos del objeto
        this.form = { ...event.params.provider }
      } else {
        // si el event.params no trae provider va ser añadir, el form contiene los datos por defecto en configuración []
        console.log('No existe un objeto provider en el event.params > action = añadir')
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
      // validación ref=providerForm - definido como atributo del elemento form del modal
      this.$refs[formName].validate((valid) => {
        if (valid) {
          // set flag
          this.saving = true
          // evaluo accion para controlar la operación
          if (this.action === 'Añadir') {
            console.log('Enviando solicitud create(this.form) al back través de la ProviderApi:', this.form)
            // lanza la promesa con el BaseApiCalls y los datos del form
            ProviderApi.create(this.form).then(() => {
              // setea flag
              this.saving = false
              // esconde el modal
              this.$modal.hide('provider')
              // emite evento por bus para hacer el fetch
              console.log('Operación completada con éxito, emitiendo recargarListaProveedores por el bus')
              this.$bus.$emit('recargarListaProveedores')
            }).catch(() => {
              console.log('Catch en Onsubmit() - create')
              // evita el cargando freeze
              this.saving = false
            })
          } else {
            // se repite la logica, esta vez updateando
            console.log('Enviando solicitud update(this.form) al back a través de la ProviderApi:', this.form)
            ProviderApi.update(this.form).then(() => {
              this.saving = false
              this.$modal.hide('provider')
              console.log('Operación completada con éxito, emitiendo recargarListaProveedores por el bus')
              this.$bus.$emit('recargarListaProveedores')
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