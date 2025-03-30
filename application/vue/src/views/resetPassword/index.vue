<template lang="pug">
  login-and-reset-password-template.login-and-reset-password-template
    el-form.login-form-box(autocomplete="on" :model="resetForm" :rules="resetRules" ref="resetForm" label-position="left")
      .absurd-text Cambiar contraseña
      .login-label.with-margin Nueva contraseña
      .login-item(style="display:flex; align-items:center;")
        span.svg-container
          svg-icon(icon-class="password" style="color: #FFF")
        custom-input.login-input(
          v-model="resetForm.password"
          type='password'
          @keyup.enter.native="handleLogin"
          placeholder="Contraseña"
          autocomplete="on"
          light_password_icon
          transparent
        )
      .login-label Repetir contraseña
      .login-item(style="display:flex; align-items:center;")
        span.svg-container
          svg-icon(icon-class="password" style="color: #FFF")
        custom-input.login-input(
          v-model="resetForm.repeatPassword"
          type='password'
          @keyup.enter.native="handleLogin"
          placeholder="Repetir contraseña"
          autocomplete="on"
          light_password_icon
          transparent
        )
      .login-button-box
        custom-button(with_border rounded light @click.native.prevent="handleLogin") Acceder
        span(v-if="show" style="font-size:14px;margin-left:5px;color:#00444F;font-weight:600") {{message}}
</template>

<script>
  import LoginAndResetPasswordTemplate from '@/layout/LoginAndResetPasswordTemplate'
  import { Message } from 'element-ui'
  import { changePassword } from '@/api/login'

  export default {
    name: 'ResetPasswordView',
    components: {
      LoginAndResetPasswordTemplate
    },
    data() {
      // TODO: para que funcione la validación hay que añadir el prop en los custom-input, pero se rompen los estilos. Revisar.
      const validatePass = (rule, value, callback) => {
        if (this.resetForm.password !== this.resetForm.repeatPassword || this.resetForm.repeatPassword === null) {
          callback(new Error('Las contraseñas no coinciden'))
        } else {
          callback()
        }
        callback()
      }
      return {
        show: false,
        message: null,
        resetForm: {
          password: null,
          repeatPassword: null,
          forgotten_password_code: this.$route.params.forgotten_password_code,
          is_app: false
        },
        resetRules: {
          password: [
            { required: true, trigger: 'blur' },
            { min: 8, message: 'Demasiado corta, debe tener min 8 caracteres', trigger: 'blur' }],
          repeatPassword: [
            { required: true, trigger: 'blur', validator: validatePass }
          ]
        },
        loading: false,
        pwdType: 'password'
      }
    },
    methods: {
      showPwd() {
        if (this.pwdType === 'password') {
          this.pwdType = ''
        } else {
          this.pwdType = 'password'
        }
      },
      handleLogin() {
        this.show = true
        this.$refs.resetForm.validate(valid => {
          const samePassword = this.resetForm.password === this.resetForm.repeatPassword
          const notNull = this.resetForm.password !== null && this.resetForm.repeatPassword !== null
          if (notNull) {
            if (samePassword) {
              console.log('hola')
              this.loading = true
              changePassword(this.resetForm).then(response => {
                this.show = false
                this.loading = false
                this.$router.push({ path: '/login' })
              }).catch(response => {
                console.log(response)
                Message({
                  message: 'No es posible cambiar la contraseña',
                  type: 'error',
                  duration: 2 * 1000
                })
                this.loading = false
              })
            } else {
              this.message = 'Las contraseñas no coinciden'
              return false
            }
          } else {
            this.message = 'Constaseñas obligatorias'
            return false
          }
        })
      }
    }
  }
</script>

<style lang="sass" scoped>
  // Los estilos están en el caomponente LoginAndResetPasswordTemplate
</style>
