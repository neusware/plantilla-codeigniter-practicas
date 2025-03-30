<template lang="pug">
  section.app-main
    transition(name="fade", mode="out-in")
      //- <router-view :key="key"></router-view>
      router-view
</template>

<script>
  export default {
    name: 'AppMain',
    computed: {
      // key() {
      //   return this.$route.name !== undefined ? this.$route.name + +new Date() : this.$route + +new Date()
      // }
    }
  }
</script>

<style lang="sass">
  $breaking-phones: 480px

  // Estilos globales de las 2 secciones
  .app-main
    .two-sections-view
      width: 100%
      display: flex
      flex-wrap: wrap
      margin: 0px !important
      .section-title
        color: var(--main-color)
        font-weight: bold
        font-size: 26px
      .left-subview-box, .right-subview-box
        flex: 1
        min-width: 360px
        padding: 20px 40px
      .left-subview-box
        min-height: 40%
        max-height: 100%
      .right-subview-box
        overflow: auto
        background: #E5E5E5
        max-height: 100%
  // Estilos globales de las 2 secciones con scroll. A las clases right-subview-box añadirle la clase scrollable-section
  // Debajo de la clase scrollable-section insertar la clase scrollable-box y dentro añadir el contenido.
  @media (min-width: $breaking-phones)
    .right-subview-box.scrollable-section
      padding-right: 0px !important
      padding-left: 0px !important
    .left-subview-box.scrollable-section
      padding-right: 0px !important
      padding-left: 0px !important
    .scrollable-section
      .scrollable-box
        overflow: auto
        max-height: calc(100vh - 110px)
        background-clip: padding-box
        padding-right: 40px
        padding-left: 40px
</style>
