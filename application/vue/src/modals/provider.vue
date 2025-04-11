<template lang="pug">
  modal(name="provider" @before-open="beforeOpen" :min-width="500" height="auto" class="custom-modal" :adaptive="true" :scrollable="true")
    .modal-title
        span.modal-title-text {{action}} Proveedor
    .modal-body
      el-form.flex-modal-form(@submit.prevent.native="submitForm" :model="form" ref="providerForm" :rules="rules")
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
      rules: {},
      config: { inputs: [] } // Inicializamos config con un objeto vacío
    }
  },
  methods: {
    beforeOpen(event) {
      console.log('Método beforeOpen() ejecutándose en modal provider')
      try {
        this.config = require('@/modals/config/provider').default
        console.log('Configuración por defecto para el modal cargada')
      } catch (error) {
        console.error('Error al cargar la configuración por defecto para el modal: ', error)
        this.config = { inputs: [] } // Fallback en caso de error
      }
      // ?
      this.resetForm()

      // compruebo si el event.params trae un objeto provider
      if (event.params && event.params.provider) {
        // entonces la accón es editar
        this.action = 'Editar'
        // extraigo los datos del objeto  para copiarlos al form
        this.form = { ...event.params.provider }
        // fb
        console.log('Existe un objeto provider en el event.params > action = editar && this.form = event.params.provider')
      } else {
        // si el event.params no trae provider va ser añadir, el form contiene los datos por defecto en configuración
        console.log('No existe un objeto provider en el event.params > action = añadir')
        this.action = 'Añadir'
      }
    },
    resetForm() {
      if (this.config && this.config.inputs) {
        this.saving = false
        this.form = this.config.inputs.reduce((acc, input) => {
          acc[input.prop] = input.value
          return acc
        }, {})
      }
    },
    onSubmit(formName) {
      // fb
      console.log('Método Onsubmit() ejecutándose')
      // validación
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
              console.console('Catch en Onsubmit() - create')
              this.saving = false
            }) // evita el cargando freeze
          } else {
            // se repite la logica, esta vez updateando
            console.log('Enviando solicitud update(this.form) al back a través de la ProviderApi:', this.form)
            ProviderApi.update(this.form).then(() => {
              this.saving = false
              this.$modal.hide('provider')
              console.log('Operación completada con éxito, emitiendo recargarListaProveedores por el bus')
              this.$bus.$emit('recargarListaProveedores')
            }).catch(() => {
              console.console('Catch en Onsubmit() - update')
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