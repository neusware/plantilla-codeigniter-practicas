<template lang="pug">
  .container#only-admins-view
    h1.centered {{message}}
</template>

<script>
  export default {
    data() {
      return {
        message: 'Si has accedido, eres administrador'
      }
    }
  }
</script>

<style lang="sass" scoped>
  #only-admins-view
    h1
      background: #AAA
</style>
