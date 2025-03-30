<template lang="pug">
  .full-container#page-not-found-view
    .centered-box
      .icon-box
        svg-icon(icon-class="icono_signlab_light" style="color: #FFF; width: 200px; height: 200px;  display: block; margin: auto;")

      //- .text-box.title Error 404
      .text-box Página no encontrada
      .buttons-box
        .button-box
          custom-button.scope-button(transparent_to_light thin with_border rounded @click.native="$router.push({ path: '/' })") Volver
</template>

<script>
  export default {
    name: 'PageNotFound',
    data() {
      return {
        // loading: false
      }
    }
  }
</script>

<style lang="sass" scoped>
  $breakpoint-phones: 480px

  #page-not-found-view
    // height necesario para el full-layout
    height: 100%
    display: flex
    align-items: center
    justify-content: center
    background: var(--main-color)
    margin: 0px !important
    .centered-box
      margin-top: -150px
      color: #FFF
      max-width: 500px
      .icon-box
        margin-bottom: 15px
        opacity: 0
        animation-name: slideUp
        animation-duration: 0.5s
        animation-delay: 0.1s
        animation-fill-mode: forwards
      .text-box
        font-size: var(--title-font-size)
        font-weight: bold
        text-align: center
        padding: 20px 5px
        opacity: 0
        animation-name: slideUp
        animation-duration: 0.5s
        animation-delay: 0.2s
        animation-fill-mode: forwards
        &.title
          font-size: 42px

      .buttons-box
        display: flex
        justify-content: space-between
        flex-wrap: wrap
        margin-top: 15px
        opacity: 0
        animation-name: slideUp
        animation-duration: 0.5s
        animation-delay: 0.3s
        animation-fill-mode: forwards
        .button-box
          flex: 1
          min-width: 210px
          display: flex
          align-items: center
          justify-content: center
          margin: 5px
          .scope-button
            color: #FFF
            &:hover
              color: var(--font-color) !important

      @keyframes slideUp
        0%
          transform: translateY(60px)
          opacity: 0
        100%
          transform: translateY(0)
          opacity: 1

  @media (max-width: $breakpoint-phones)
    #change-scope-view
      align-items: center
      .centered-box
        margin-top: -25px
        .icon-box
        .text-box
          padding: 20px
        .buttons-box
          .button-box
            min-width: 180px
            .scope-button
              font-size: 18px
              padding: 15px 40px
              width: 165px
</style>
