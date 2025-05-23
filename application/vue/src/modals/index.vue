<template lang="pug">
  .modals-container
    modal-hello-world
    modal-recuperar-pass
    modal-usuario
    modal-provider
    modal-product
    modal-client
</template>

<script>
  import ModalHelloWorld from './hello-world.vue'
  import ModalRecuperarPass from './recuperar-pass.vue'
  import ModalUsuario from './usuario.vue'
  import ModalProvider from './provider.vue'
  import ModalProduct from './product.vue'
  import ModalClient from './client.vue'

  export default {
    name: 'GlobalModals',
    components: {
      ModalHelloWorld,
      ModalRecuperarPass,
      ModalUsuario,
      ModalProvider,
      ModalProduct,
      ModalClient
    }
  }
</script>
