<template lang="pug">
  modal(name="generic-modal" @before-open="beforeOpen" @before-close="beforeClose" :min-width="500" height="auto" class="custom-modal" :adaptive="true" :scrollable="true")
    .modal-title
        span.modal-title-text {{action}} {{title}}
    .modal-body
      el-form.flex-modal-form(@submit.prevent.native="" :model="generic_model" ref="genericForm" :rules="generic_rules")
        .modal-form-flex-item(v-for="input in generic_inputs" :class='input.type === "file" ? "full" : ""')
          custom-input(v-if='input.type === "text"' v-model="input.value" :label="input.label" :prop="input.prop" )
          custom-input(v-else-if='input.type === "number"' v-model="input.value" :prop="input.prop" :label="input.label" type="number" min="0")
          custom-input(v-else-if='input.type === "password"' v-model="input.value" :label="input.label" :prop="input.prop" type="password")
          custom-select(v-else-if='input.type === "select" && input.module_name' v-model="input.value" :controller_name="input.controller_name" :module_name="input.module_name" :label="input.label" :prop="input.prop" clearable)
          custom-select(v-else-if='input.type === "select" && input.options' v-model="input.value" :options="input.options" :label="input.label" :prop="input.prop" clearable)
          custom-select(v-else-if='input.type === "select"' v-model="input.value" :controller_name="input.controller_name" :label="input.label" :prop="input.prop" clearable)
          custom-textarea(v-else-if='input.type === "textarea"' v-model="input.value" :label="input.label" :prop="input.prop")
          el-checkbox(v-else-if='input.type === "checkbox"' v-model="input.value" :prop="input.prop") {{input.label}}
          .upload-file-item(v-else-if='input.type === "file"')
            el-upload.ej(class="upload-demo" drag :on-change="editArchivo" :on-exceed="warningMessage" :on-remove="removeArchivo" ref="file" id="file" action="" :auto-upload="false" :limit="1")
              i(class="el-icon-upload")
                div(class="el-upload__text") Suelta tu archivo aquí o
                  em &nbsp; haz clic para cargar
              div(v-if="!error" slot="tip" class="el-upload__tip") Solo un archivo con un tamaño menor de 4MB
              div(v-else slot="tip" class="el-upload__tip" style="color:red") El tamaño del archivo tiene que ser menor de 4MB
              div(v-if="error2" slot="tip" class="el-upload__tip" style="color:red") Es obligatorio subir un archivo
    .modal-footer.space-between
      custom-button(@click.prevent.native="$modal.hide('generic-modal')" dark thin) Cancelar
      custom-button(@click.prevent.native='onSubmit("genericForm")') {{action}}
</template>

<script>
  export default {
    name: 'generic-modal',
    data() {
      const validatePass = (rule, value, callback) => {
        (this.generic_inputs[this.input_password_key].value !== this.generic_inputs[this.input_password_again_key].value) ? callback(new Error('Las contraseñas no coinciden')) : callback()
      }
      return {
        action: null,
        title: null,
        generic_inputs: [],
        generic_rules: {},
        input_file_key: null,
        input_password_key: null,
        password_again: null,
        input_password_again_key: null,
        error: false,
        error2: false,
        loaded: false,
        ControllerApi: null,
        validatePass: validatePass
      }
    },
    computed: {
      // Usamos un computed para poder falsear la reactividad que tendriamos si el generic_model creado en el data y rellenado dinámicamente en un función.
      // Sin esto las reglas para la validación no funcionaría
      generic_model: {
        get: function() {
          const model = {}
          this.generic_inputs.forEach(input => {
            model[input.prop] = input.value // setear los diferentes values que tendrá cada input
          })
          return model
        }
      }
    },
    methods: {
      beforeOpen(event) {
        if (event.params.model) {
          this.procesarParametros(event.params.parametros, event.params.model)
          this.action = 'Editar'
          this.generic_inputs.push({ type: 'id', prop: 'id', value: event.params.model['id'] })
        } else {
          this.procesarParametros(event.params.parametros)
          this.action = 'Añadir'
        }
      },
      beforeClose() {
        this.action = null
        this.title = null
        this.generic_inputs = []
        this.generic_rules = {}
        this.input_file_key = null
        this.error = false
        this.error2 = false
        this.loaded = false
        this.ControllerApi = null
      },
      editArchivo(file) {
        this.generic_inputs[this.input_file_key].value = file
        this.error2 = false
        this.error = false
      },
      warningMessage() {
        this.$message({
          dangerouslyUseHTMLString: true,
          message: 'Solo puede ser subido <strong>1</strong> archivo a la vez',
          type: 'warning'
        })
      },
      removeArchivo() {
        this.generic_inputs[this.input_file_key].value = null
        this.error2 = true
        this.error = false
      },
      procesarParametros(parametros, modelo = false) {
        this.title = parametros.title
        if (parametros.controller) {
          if (parametros.module_name) {
            import('@/modules/' + parametros.module_name + '/api/' + this.$helpers.capitalizeEachWord(parametros.controller) + 'Api.js').then((controller_js) => {
              this.ControllerApi = controller_js.default.constructor()
              this.ControllerApi.getDropdown().then(response => {
                this.options_dropdown = response.data
              })
            })
          } else {
            import('@/api/' + this.$helpers.capitalizeEachWord(parametros.controller) + 'Api.js').then((controller_js) => {
              this.ControllerApi = controller_js.default.constructor()
              this.ControllerApi.getDropdown().then(response => {
                this.options_dropdown = response.data
              })
            })
          }
        }
        this.generic_inputs = JSON.parse(JSON.stringify(parametros.inputs))
        this.generic_inputs.forEach((input, key) => {
          if (modelo) {
            input.value = modelo[input.prop] // setear los diferentes values que tendrá cada input
          }
          if (input.type === 'file') this.input_file_key = key
          else if (input.type === 'password') {
            this.generic_rules['password_again'] = input.rules
            this.input_password_key = key
          }
          if (input.rules) {
            const arr = []
            input.rules.forEach(rule => {
              if (rule.is_regex) rule.pattern = new RegExp(rule.pattern)
              arr.push(rule)
            })
            this.generic_rules[input.prop] = arr
          }
        })
        if (this.input_password_key) {
          this.generic_inputs.splice(this.input_password_key + 1, 0, { type: 'password', prop: 'password_again', label: 'Repetir contraseña', value: null })
          this.input_password_again_key = this.input_password_key + 1
          this.generic_rules['password_again'].push({ required: true, trigger: ['blur', 'change'], validator: this.validatePass })
        }
      },
      onSubmit(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            var file_mode = false
            this.generic_inputs.forEach(input => { // parseo de booleano a 1-0
              if (input.type === 'checkbox') {
                if (input.value) {
                  input.value = 1
                } else {
                  input.value = 0
                }
              }
              if (input.type === 'file' && input.rules.length > 0 && input.value) {
                file_mode = true
              }
            })
            if (file_mode) {
              const file = document.querySelector('#file > div > input')
              if (file.files[0] !== undefined) {
                if (file.files[0].size > 4000000) {
                  this.error = true
                  return false
                }
              }
              this.generic_inputs[this.input_file_key].value = this.generic_inputs[this.input_file_key].value.name
              const createFormData = new FormData()
              createFormData.append(this.generic_inputs[this.input_file_key].prop, file.files[0])
              createFormData.append('data', JSON.stringify(this.generic_model))
              if (this.action === 'Añadir') {
                this.ControllerApi.create(createFormData, true).then((response) => {
                  this.$bus.$emit('genericReload')
                })
              } else {
                this.ControllerApi.update(createFormData, true).then((response) => {
                  this.$bus.$emit('genericReload')
                })
              }
            } else {
              if (this.action === 'Añadir') {
                this.ControllerApi.create(this.generic_model).then((response) => {
                  this.$bus.$emit('genericReload')
                })
              } else {
                this.ControllerApi.update(this.generic_model).then((response) => {
                  this.$bus.$emit('genericReload')
                })
              }
            }

            this.$modal.hide('generic-modal')
          }
        })
      }
    }
  }
</script>

<style lang="sass" scoped>
  .full
    width: 100%
    margin-top: 20px !important
    flex: none !important
  .upload-file-item
    display: flex
    justify-content: center
    margin-bottom: 15px
    width: 100%
    .full-row /deep/
      width: 100%
      .el-upload-dragger
        border: 1px dashed #666
        border-radius: 20px
        width: 330px
        height: 150px
        transition: 0.3s
        &:hover
          border-color: #999
          .el-icon-upload
            color: #999
          em
            color: #999
      .el-icon-upload
        font-size: 55px
        color: #27282b
        margin: 40px 0 10px
        line-height: 40px
      .el-upload__tip
        color: #27282b
        font-size: 13px
        margin-top: 0px
      .el-upload__submit
        font-size: 13px
        color: #666
        margin-top: 0px
  // NOTA: para el formulario, colocar la clase ".flex-modal-form" en el "el-form" e ir creando hijos
  //       con la clase ".modal-form-flex-item", se ajustará automáticamente por los estilos que hay
  //       creados en modals.sass. Si quieres que uno ocupe toda la fila, usa la clase ".max-width".
  //       Si quieres modificar más el modal, ponlos aquí debajo.
</style>
