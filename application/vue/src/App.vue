<template lang="pug">
  #app
    modals-container
    v-dialog
    router-view
</template>

<script>
  import ModalsContainer from './modals/index.vue'

  export default {
    name: 'App',
    components: {
      ModalsContainer
    }
  }
</script>

<style lang="sass">
</style>
