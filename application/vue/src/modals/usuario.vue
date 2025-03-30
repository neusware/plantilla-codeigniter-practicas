<template lang="pug">
  modal(name="usuario" @before-open="beforeOpen" :min-width="500" height="auto" class="custom-modal" :adaptive="true" :scrollable="true")
    .modal-title
        span.modal-title-text {{action}} Usuario
    .modal-body
      el-form.flex-modal-form(@submit.prevent.native="addUser" :model="user" ref="userForm" :rules="userRules")
        .modal-form-flex-item(v-if="action === 'Editar'")
          custom-input(v-model="user.username" disabled label="Nombre de Usuario:" prop="username")
        .modal-form-flex-item
          custom-input(v-model="user.first_name" label="Nombre:" prop="first_name")
        .modal-form-flex-item
          custom-input(v-model="user.last_name" label="Apellidos:" prop="last_name")
        .modal-form-flex-item
          custom-input(v-model="user.email" label="E-mail:" prop="email")
        .modal-form-flex-item(v-if="action == 'Añadir' || user != null && user.id != undefined && user.id != null && $helpers.getUser().id != user.id")
          custom-select(v-model="user.roles" :options="roles_options" label="Rol:" prop="roles")
        .modal-form-flex-item
          custom-input(v-model="user.phone" label="Teléfono:" prop="phone")
        .modal-form-flex-item(v-if="action == 'Añadir' || user != null && user.id != undefined && user.id != null && $helpers.getUser().id == user.id")
        .max-width(v-if="check_change_password || action === 'Editar'" style="margin: 20px 0px 10px 0px;")
          el-checkbox.max-width(v-model="check_change_password") Cambiar contraseña
        template(v-if="action === 'Añadir' || (check_change_password)")
          .modal-form-flex-item
            custom-input(v-model="user.password" type="password" label="Contraseña:" prop="password")
          .modal-form-flex-item
            custom-input(v-model="password_again" type="password" label="Repetir contraseña:" prop="password_again")

    .modal-footer.space-between
      custom-button(:disabled="saving" @click.prevent.native="$modal.hide('usuario')" dark thin) Cancelar
      custom-button(:disabled="saving" @click.prevent.native='onSubmit("userForm")') {{(saving ? (action == 'Añadir' ? 'Añadiendo...' : 'Editando...') : action)}}
</template>

<script>
  import UserApi from '@/api/UserApi'
  import GroupsApi from '@/api/GroupsApi'

  export default {
    name: 'usuario',
    data() {
      const validatePass = (rule, value, callback) => {
        (this.user.password !== this.password_again) ? callback(new Error('Las contraseñas no coinciden')) : callback()
        // if (this.user.password !== this.password_again) {
        //   callback(new Error('Las contraseñas no coinciden'))
        // } else {
        //   callback()
        // }
      }
      return {
        params: null,
        action: null,
        saving: false,
        user: {
          id: null,
          username: null,
          password: null,
          email: null,
          first_name: null,
          last_name: null,
          phone: null,
          roles: []
        },
        check_change_password: false,
        password_again: '',
        roles_options: [],
        userRules: {
          first_name: [{ required: true, message: 'Introduzca el nombre del usuario', trigger: ['blur', 'change'] }],
          last_name: [{ required: true, message: 'Introduzca los apellidos del usuario', trigger: ['blur', 'change'] }],
          email: [
            { required: true, message: 'Introduzca un email', trigger: ['blur', 'change'] },
            { pattern: /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/, message: 'Introduzca un email válido', trigger: ['blur', 'change'] }
          ],
          phone: [
            { required: true, message: 'Introduzca un teléfono', trigger: ['blur', 'change'] },
            { min: 9, message: 'El número es demasiado corto', trigger: ['blur', 'change'] },
            { max: 9, message: 'El número es demasiado largo', trigger: ['blur', 'change'] },
            { pattern: /^(0|[1-9][0-9]*)$/, message: 'Introduzca un número válido', trigger: ['blur', 'change'] }
          ],
          password: [
            { required: true, min: 8, message: 'Demasiado corta, debe tener min 8 caracteres', trigger: ['blur', 'change'] }
          ],
          password_again: [
            { required: true, trigger: ['blur', 'change'], validator: validatePass },
            { min: 8, message: 'Demasiado corta, debe tener min 8 caracteres', trigger: ['blur', 'change'] }
          ],
          roles: [
            { required: true, message: 'Seleccione el rol de usuario', trigger: ['blur', 'change'] }
          ]
        }
      }
    },
    methods: {
      beforeOpen(event) {
        this.resetUser()
        this.getRolesFromApi()

        if (event.params && event.params.user) {
          this.params = event.params
          this.action = 'Editar'
          this.recibeDatosDeUsuario()
        } else {
          this.params = false
          this.action = 'Añadir'
        }

        // Comprobación para poner como fijo el id_cliente
        if (event.params && event.params.client_id) {
          this.params = event.params
          this.user.client_id = this.params.client_id
        }
      },
      resetUser() {
        this.saving = false
        this.check_change_password = false

        this.user.id = null
        this.user.password = null
        this.password_again = null
        this.user.username = null
        this.user.email = null
        this.user.first_name = null
        this.user.last_name = null
        this.user.phone = null
        this.user.roles = []
      },
      recibeDatosDeUsuario() {
        this.user.id = this.params.user.id
        this.user.username = this.params.user.username
        this.user.password = this.params.user.password
        this.user.email = this.params.user.email
        this.user.first_name = this.params.user.first_name
        this.user.last_name = this.params.user.last_name
        this.user.phone = this.params.user.phone.toString() // Se pasa a string para que no de error de verificación
        this.user.roles = this.params.user.grupos[0].grupo.id
        this.password_again = this.params.user.password
      },
      onSubmit(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            this.saving = true
            this.user.username = this.user.email
            if (this.action === 'Añadir') {
              UserApi.create(this.user).then((response) => {
                this.saving = false
                this.$modal.hide('usuario')
                this.$bus.$emit('recargarListaUsuarios')
              }).catch(() => { this.saving = false })
            } else {
              UserApi.update(this.user).then((response) => {
                this.saving = false
                this.$modal.hide('usuario')
                this.$bus.$emit('recargarListaUsuarios')
              }).catch(() => { this.saving = false })
            }
          }
        })
      },
      getRolesFromApi() {
        const self = this
        GroupsApi.getDropdown().then(response => {
          self.roles_options = response.data
        })
      }
    }
  }
</script>

<style lang="sass" scoped>
  // NOTA: para el formulario, colocar la clase ".flex-modal-form" en el "el-form" e ir creando hijos
  //       con la clase ".modal-form-flex-item", se ajustará automáticamente por los estilos que hay
  //       creados en modals.sass. Si quieres que uno ocupe toda la fila, usa la clase ".max-width".
  //       Si quieres modificar más el modal, ponlos aquí debajo.
</style>
