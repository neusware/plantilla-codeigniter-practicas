<template lang="pug">
  login-and-reset-password-template
    el-form.login-form-box(autocomplete="on" :model="loginForm" ref="loginForm")
      .absurd-text Acceso
      .login-label.with-margin Nombre de Usuario
      .login-item
        span.svg-container.svg-container_login
          svg-icon(icon-class="user" style="color: #FFF")
        custom-input.login-input(transparent name="username" type="text" v-model="loginForm.identity" placeholder="Usuario")
      .login-label Contraseña
      .login-item
        span.svg-container
          svg-icon(icon-class="password" style="color: #FFF")
        custom-input.login-input(
          v-model="loginForm.password"
          type="password"
          placeholder="Contraseña"
          name="password"
          @keyup.enter.native="handleLogin"
          transparent
          light_password_icon
        )
      .login-button-box
        span.recovery(@click="$modal.show('recuperar-pass')") Recordar usuario y/o contraseña
        custom-button(
          @click.native.prevent="handleLogin"
          rounded
          :icon_right="(loading) ? 'el-icon-loading' : null"
        ) Acceder
</template>

<script>
  import LoginAndResetPasswordTemplate from '@/layout/LoginAndResetPasswordTemplate'
  // NOTA: la validación del email no la hemos usado hasta ahora, queda comentado por si queremos usarla más adelante.
  // NOTA 2: el loading tampoco lo usamos actualmente.
  // import { isvalidEmail } from '@/utils/validate'

  export default {
    name: 'LoginView',
    components: {
      LoginAndResetPasswordTemplate
    },
    data() {
      // const validateEmail = (rule, value, callback) => {
      //   if (!isvalidEmail(value)) {
      //     callback(new Error('Email inválido'))
      //   } else {
      //     callback()
      //   }
      // }
      // const validatePass = (rule, value, callback) => {
      //   callback()
      // }
      return {
        loginForm: {
          identity: 'superadmin@signlab.es',
          password: '123Prueba.'
        },
        // loginRules: {
        //   identity: [{ required: true, trigger: 'blur', validator: validateEmail }],
        //   password: [{ required: true, trigger: 'blur', validator: validatePass }]
        // },
        loading: false
      }
    },
    methods: {
      handleLogin() {
        this.$refs.loginForm.validate(valid => {
          if (valid) {
            this.loading = true

            this.$store.dispatch('Login', this.loginForm).then(() => {
              this.$store.dispatch('GetInfo').then(() => {
                // Si se tiene que mandar a otra ruta tras hacer login, ponerlo aquí
                const redirect_to = '/'
                window.location.href = redirect_to
                this.loading = false
              }).catch(() => {
                this.$helpers.showNewCustomNotification('No tienes permisos para acceder cuando el servidor esta en mantenimiento', 'error')
                this.loading = false
              })
            }).catch(() => {
              this.loading = false
              this.$helpers.showNewCustomNotification('Datos incorrectos', 'error')
            })
          } else {
            console.log('error on submit!!')
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
