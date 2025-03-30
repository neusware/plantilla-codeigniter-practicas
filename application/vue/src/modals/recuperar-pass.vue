<template lang="pug">
   modal(name="recuperar-pass" :min-width="360" :width="550" height="auto" class="custom-modal" :adaptive="true" :scrollable="true")
    .modal-title
      span Recuperar contraseña
    .modal-body(v-if="!sent")
      el-form.m-body-data-box(autocomplete="on" :model="form" ref="form" :rules="formRules")
        .m-body-data
          .m-body-data-label Introduce el correo electrónico asociado a su cuenta:
          custom-input(v-model="form.email" prop="email")
          custom-input(style="display:none;")
    .modal-body(v-else)
      .confirmation Se ha enviado al correo electrónico un link para restablecer la contraseña asociada a esta cuenta. Si no aparece en unos minutos, revise la carpeta de Spam.

    .modal-footer
      custom-button(v-if="!sent" @click.native.prevent="recuperarPass('form')" dark) Enviar
      custom-button(v-else @click.native.prevent="$modal.hide('recuperar-pass'), sent = false" dark) Volver
</template>

<script>
import { forgotPassword } from '@/api/login'
export default {
  data() {
    return {
      sent: false,
      form: {
        email: null
      },
      formRules: {
        email: [
          { required: true, message: 'Introduzca su correo electrónico', trigger: 'blur' },
          { pattern: /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/, message: 'Introduzca un correo electrónico válido', trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    recuperarPass(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          console.log(this.form.email)
          const formData = new FormData()
          formData.append('identity', this.form.email)
          forgotPassword(formData).then(response => {
            this.sent = true
            this.form.email = null
          })
        } else {
          return false
        }
      })
    }
  }
}
</script>

<style lang="sass" scoped>
$fontColor: #F0F0F0

.custom-modal
  color: $fontColor !important
  /deep/ .v--modal-box
    background: transparent !important
    box-shadow: none
  .modal-title
    // background: #7AD2C4 !important
    span
      font-size: 20px
      font-weight: 600

  .m-body-data-label
    color: #626260
    font-weight: bold
    font-size: 18px
    margin-bottom: 15px

  .confirmation
    color: #626260
    font-weight: bold
    font-size: 18px
    margin-bottom: 15px

</style>
