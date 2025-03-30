<template lang="pug">
  .login-container(v-if="status != null && status.status == 1 && status.roles_can_access == 0")
    .maintenance
      svg-icon.logo-img-maintenance(icon-class="logo_signlab_light")
      .maintenance-section-box
        h1 ¡Lo sentimos!
        h1 La web esta en mantenimento.
        h1 ¡Gracias por su paciencia!
  .login-container-maintenance(v-else-if="status != null && status.status == 1 && status.roles_can_access > 0")
    .separator
      .login-box-maintenance
        .login-section-box
          .message El servidor esta en mantenimiento.
          .message Solo pueden acceder los usuarios permitidos.
      .login-box
        .logo-container.login-section-box
          svg-icon.logo-img(icon-class="icono_signlab_light")
          .project-title.centered Panel Signlab
        .inputs-box.login-section-box
          slot
  .login-container(v-else)
    .login-box
      .logo-container.login-section-box
        svg-icon.logo-img(icon-class="icono_signlab_light")
        .project-title.centered Panel Signlab
      .inputs-box.login-section-box
        slot
</template>

<script>
  import { getMaintenanceStatus } from '@/api/ServerConfigurationApi'
  export default {
    name: 'LoginAndResetPasswordLayoutTemplate',
    beforeCreate() {
      getMaintenanceStatus().then(response => {
        this.status = response.data
      })
    },
    data() {
      return {
        status: null
      }
    },
    methods: {
      // handleLogin() {
      //   this.$refs.loginForm.validate(valid => {
      //     if (valid) {
      //       this.loading = true
      //       this.$store.dispatch('Login', this.loginForm).then(() => {
      //         this.loading = false
      //         this.$router.push({ path: '/' })
      //       }).catch(() => {
      //         this.loading = false
      //         // this.$message.error('Datos incorrectos')
      //       })
      //     } else {
      //       console.log('error submit!!')
      //       return false
      //     }
      //   })
      // }
    }
  }
</script>

<style lang="sass" scoped>
  .login-container::after, .login-container-maintenance::after
    content: ""
    background: url("/static/fondo_verde_signlab_corporativo.jpg") no-repeat fixed center
    background-size: cover
    top: 0
    left: 0
    bottom: 0
    right: 0
    position: absolute
    z-index: -1

  .login-container-maintenance
    display: flex
    // flex-wrap: wrap

  .login-container, .login-container-maintenance
    position: fixed
    height: 100%
    width: 100%
    background-color: black
    display: flex
    justify-content: center
    align-items: center
    .maintenance
      flex: 1
      color: white
      width: 100%
      .logo-img-maintenance
        background: var(--secondary-color)
        box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.5)
        margin: auto
        width: 30%
        padding: 30px
        display: flex
        justify-content: center
        text-align: center
        border-radius: 0px 20px 0px 20px
        height: auto
        @media (max-width: 768px)
          width: 60%
          padding: 20px
          display: flex
          justify-content: center
          text-align: center
        @media (max-width: 360px)
          width: 70%
          font-size: 300px
          max-height: 100px
      .maintenance-section-box
        margin-top: 50px
        text-align: center
        width: 100%
        @media (max-width: 360px)
          h1
            font-size: 25px
    .login-box-maintenance
      display: flex
      background: var(--secondary-color)
      color: #FFF
      width: 420px
      padding: 20px
      margin: auto
      margin-bottom: 40px
      border-radius: 6px
      flex-wrap: wrap
      @media (max-width: 360px)
        max-width: 320px
      .message
        display: flex
        font-size: 18px
        font-weight: bold
        justify-content: center
        margin-top: 10px
    .login-box
      display: flex
      justify-content: space-between
      background: var(--secondary-color)
      border-bottom-right-radius: 70px
      color: #FFF
      width: 750px
      padding: 35px
      margin: 0px 10px
      flex-wrap: wrap
      @media (max-width: 360px)
          max-width: 320px
          padding: 20px
          border-bottom-right-radius: 30px
      .logo-container
        flex-direction: column
        align-items: center
        align-content: center
        text-align: center
        .logo-img
          width: 100%
          height: 80%
          @media (max-width: 649px)
            width: 50%
        .project-title
          font-size: 32px
          font-weight: bold
          padding: 20px 5px
      .login-section-box
        flex: 1
        min-width: 280px
        /deep/
          .absurd-text
            font-size: var(--title-font-size)
            margin: 0px 0px 20px 0px
            font-weight: bold
          .login-item
            display: flex
            align-items: center
            border-bottom: 1px solid #EEE !important
            margin: 16px 0px
            .login-input
              flex: 1
              /deep/ input::placeholder
                color: #FFF !important
          .svg-container
            padding: 6px 5px 6px 15px
            width: 30px
            display: inline-block
            // font-weight: bold
            &_login
              font-size: 20px
          @media (max-width: 649px)
            .with-margin
              margin-top: 10px
            .login-item
              margin-top: auto
            .absurd-text
              opacity: 0
          .login-button-box
            justify-content: space-between
            display: flex
            .recovery
              cursor: pointer
              margin: auto 10px auto 0px
              font-size: 14px
</style>
