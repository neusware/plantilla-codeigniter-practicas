<template lang="pug">
  .modals-container
    modal-hello-world
    modal-recuperar-pass
    modal-usuario
</template>

<script>
  import ModalHelloWorld from './hello-world.vue'
  import ModalRecuperarPass from './recuperar-pass.vue'
  import ModalUsuario from './usuario.vue'

  export default {
    name: 'GlobalModals',
    components: {
      ModalHelloWorld,
      ModalRecuperarPass,
      ModalUsuario
    }
  }
</script>
