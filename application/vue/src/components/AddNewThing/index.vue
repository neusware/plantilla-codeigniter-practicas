<template lang="pug">
  .add-new-thing
    span.thing-text {{text}}
    custom-button.add-element(:disabled="disabled" rounded @click.native.prevent="$emit('click')")
      span +
</template>

<script>
  export default {
    name: 'AddNewThing',
    props: {
      text: {
        type: String
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
      }
    }
  }
</script>

<style lang="sass" scoped>
  .add-new-thing
    display: flex
    justify-content: flex-end
    align-items: center
    .add-element
      background: var(--secondary-color)
      border: 1px solid var(--secondary-color) !important
      transition: .3s
      min-width: 45px
      height: 45px
      font-size: 30px
      padding: 0px !important
      margin: 0 10px
      font-weight: bold
      color: #FFF
      &:hover
        background: #E5E5E5
        border: none !important
        border: 1px solid var(--secondary-color) !important
        color: var(--secondary-color) !important
      span
        top: -1px
        position: relative
    .thing-text
      color: var(--dark-colored-font-color)
      font-weight: bold
      font-size: 18px
</style>
