<template lang="pug">
  modal(name="hello-world" :draggable="true" :min-width="200" width="550px" height="auto" class="custom-modal" :adaptive="true" :scrollable="true")
    .modal-title
      span.modal-title-text Modal Modernito
    .modal-body
      div
        | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla dictum purus egestas libero ornare venenatis.
        | Maecenas pharetra tortor eu tortor imperdiet, a faucibus quam finibus. Nulla id lacinia quam.
        | Praesent imperdiet sed magna non finibus. Aenean blandit, mauris vitae lacinia rutrum,
        | nunc mi scelerisque sem, in laoreet sem lectus ut orci. Ut egestas nulla in vehicula feugiat.
        | Vivamus tincidunt nisi vel risus dictum suscipit. Nulla id blandit mi, vulputate aliquam enim.
    .modal-footer
      custom-button.dark-button(@click.native="$modal.hide('hello-world')") Entendido
</template>

<script>
  export default {
    name: 'SizeModalTest',
    data() {
      return {
      }
    },
    methods: {
    }
  }
</script>

<style lang="sass" scoped>
  @import '~@/styles/variables'
</style>
