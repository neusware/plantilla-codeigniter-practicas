<template lang="pug">
  modal(name="client" @before-open="beforeOpen" :min-width="500" height="auto" class="custom-modal" :adaptive="true" :scrollable="true")
    .modal-title
      span.modal-title-text {{action}} Cliente
    .modal-body
      el-form.flex-modal-form(@submit.prevent.native="submitForm" :model="form" ref="clientForm" :rules="clientRules")
        .modal-form-flex-item(v-for="input in config.inputs" :key="input.prop")
          custom-input(v-if="input.type === 'text'" v-model="form[input.prop]" :label="input.label" :prop="input.prop")
        //-   el-form-item(v-else-if="input.type === 'select'" :label="input.label" :prop="input.prop")
        //-     el-select(v-model="form[input.prop]" :placeholder="'Seleccione el proveedor'")
        //-       el-option(v-for="provider in providers" :key="provider.value" :label="provider.label" :value="provider.value")
          //- custom-input(v-else-if="input.type === 'file'" v-model="form[input.prop]" :label="input.label" :prop="input.prop")
    .modal-footer.space-between
      custom-button(:disabled="saving" @click.prevent.native="$modal.hide('client')" dark thin) Cancelar
      custom-button(:disabled="saving" @click.prevent.native="onSubmit('clientForm')") 
        | {{ saving ? (action == 'Añadir' ? 'Añadiendo...' : 'Editando...') : action }}
</template>

<script>
import ClientApi from '@/api/ClientApi'

export default {
  name: 'client',
  data() {
    return {
      action: null,
      saving: false,
      form: {},
      // Reglas de validación para el formulario, usando el valor de prop en la configuración del modal
      clientRules: {
        nombre: [
          { required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] },
          { pattern: /^[A-ZÁÉÍÓÚÑa-záéíóúñ\s]{1,100}$/, message: 'El nombre debe contener sólo letras y tener un máximo de 100 caracteres', trigger: ['blur', 'change'] }
        ],
        email: [
          { required: true, message: 'El email es obligatorio', trigger: 'blur' },
          { type: 'email', message: 'El email no es válido', trigger: 'blur' }
        ],
        apellido: [
          { required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] },
          { pattern: /^[A-ZÁÉÍÓÚÑa-záéíóúñ\s]{1,100}$/, message: 'El apellido debe contener sólo letras y tener un máximo de 100 caracteres', trigger: ['blur', 'change'] }
        ],
        direccion: [
          { required: true, message: 'Este campo es obligatorio', trigger: ['blur', 'change'] },
          { pattern: /^[A-ZÁÉÍÓÚÑa-záéíóúñ\s]{1,500}$/, message: 'La dirección debe contener sólo letras y tener un máximo de 500 caracteres', trigger: ['blur', 'change'] }
        ]
      },
      config: { inputs: [] } // config por defecto con un objeto vacío
    }
  },
  methods: {
    beforeOpen(event) {
      // fb
      console.log('Método beforeOpen() ejecutándose en modal client')
      // manejo de excepciones
      try {
        // importo configuraciones por defecto
        this.config = require('@/modals/config/client').default
        // fb
        console.log('Configuración client.js cargada en el modal')
      } catch (error) {
        // fb
        console.error('Error al cargar la configuración por defecto para el modal: ', error)
        // seteo a vacio en caso de excepcion
        this.config = { inputs: [] }
      }

      // aseguro valores por defecto en form
      console.log('Valores por defecto cargados this.form = config.inputs')
      this.resetForm()

      //! evalúo si el event.params trae un objeto client
      if (event.params && event.params.client) {
        // fb
        console.log('Existe un objeto client en el event.params > action = editar && this.form = event.params.client')
        // entonces la accón es editar
        this.action = 'Editar'
        // extraigo al form los datos del objeto
        this.form = { ...event.params.client }
      } else {
        // si el event.params no trae client va ser añadir, el form contiene los datos por defecto en configuración []
        console.log('No existe un objeto client en el event.params > action = añadir')
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
            console.log('Enviando solicitud create(this.form) al back través de la ClientApi:', this.form)
            // lanza la promesa con el BaseApiCalls y los datos del form
            ClientApi.create(this.form).then(() => {
              // setea flag
              this.saving = false
              // esconde el modal
              this.$modal.hide('client')
              // emite evento por bus para hacer el fetch
              console.log('Operación completada con éxito, emitiendo recargarListaClientes por el bus')
              console.log('Form enviado: ' + this.form.data)
              this.$bus.$emit('recargarListaClientes')
            }).catch(() => {
              console.log('Catch en Onsubmit() - create')
              // evita el cargando freeze
              this.saving = false
            })
          } else {
            // se repite la logica, esta vez updateando
            console.log('Enviando solicitud update(this.form) al back a través de la ClientApi:', this.form)
            ClientApi.update(this.form).then(() => {
              this.saving = false
              this.$modal.hide('client')
              console.log('Operación completada con éxito, emitiendo recargarListaClientes por el bus')
              this.$bus.$emit('recargarListaClientes')
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