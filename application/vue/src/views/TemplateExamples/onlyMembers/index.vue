<template lang="pug">
  .container#only-members-view
    h1.centered {{message}}
</template>

<script>
  export default {
    data() {
      return {
        message: 'Si has accedido, eres member'
      }
    },
    created() {
      this.$modal.show('hello-world')
    }
  }
</script>

<style lang="sass" scoped>
  #only-members-view
    h1
      background: #AAA
</style>
