<template lang="pug">
  .bigger-container#buttons-example-view
    title-box(:icon="$helpers.getRouteIcon(true)" :title="$helpers.getRouteTitle()")

    .section-description-box(style="padding: 10px 0px 20px 0px;")
      read-more(
        text="Al usar el componente custom-button podemos pasarle distintas props y con ello, alterar sus estilos o comportamiento. Las propiedades pueden combinarse entre ellas, aquí se muestra un ejemplo de cada una de las propiedades principales para que se vean sus efectos."
        :line_clamp="2"
        :initial_line_clamp_px="37"
      )

    .buttons-examples
      .buttons-box
        .buttons-text Buttons with icons
        .buttons
          custom-button.example-button(icon_left="el-icon-download" style="min-width: 250px;") icon_left (i)
          custom-button.example-button(icon_right="el-icon-download" style="min-width: 250px;") icon_right (i)
          custom-button.example-button(svg_icon_left="user" style="min-width: 250px;") svg_icon_left (svg-icon)
          custom-button.example-button(svg_icon_right="user" style="min-width: 250px;") svg_icon_right (svg-icon)
      .buttons-box
        .buttons-text Buttons with icons + space_between
        .buttons
          custom-button.example-button(icon_left="el-icon-download" space_between style="min-width: 250px;") icon_left (i)
          custom-button.example-button(icon_right="el-icon-download" space_between style="min-width: 250px;") icon_right (i)
          custom-button.example-button(svg_icon_left="user" space_between style="min-width: 250px;") svg_icon_left (svg-icon)
          custom-button.example-button(svg_icon_right="user" space_between style="min-width: 250px;") svg_icon_right (svg-icon)
      .buttons-box
        .buttons-text Standar buttons
        .buttons
          custom-button.example-button(vanilla) vanilla
          custom-button.example-button(zoom_animation) zoom_animation
          custom-button.example-button(paralelogram_animation) paralelogram_animation
          custom-button.example-button(dark) dark
          custom-button.example-button(light) light
          custom-button.example-button(transparent) transparent
          custom-button.example-button(transparent_to_light) transparent_to_light
      .buttons-box
        .buttons-text Thin buttons
        .buttons
          custom-button.example-button(thin vanilla) vanilla
          custom-button.example-button(thin zoom_animation) zoom_animation
          custom-button.example-button(thin paralelogram_animation) paralelogram_animation
          custom-button.example-button(thin dark) dark
          custom-button.example-button(thin light) light
          custom-button.example-button(thin transparent) transparent
          custom-button.example-button(thin transparent_to_light) transparent_to_light
      .buttons-box
        .buttons-text Square buttons
        .buttons
          custom-button.example-button(square vanilla) vanilla
          custom-button.example-button(square zoom_animation) zoom_animation
          custom-button.example-button(square paralelogram_animation) paralelogram_animation
          custom-button.example-button(square dark) dark
          custom-button.example-button(square light) light
          custom-button.example-button(square transparent) transparent
          custom-button.example-button(square transparent_to_light) transparent_to_light
      .buttons-box
        .buttons-text Parallelogram + Square buttons
        .buttons
          custom-button.example-button(parallelogram square vanilla) vanilla
          custom-button.example-button(parallelogram square zoom_animation) zoom_animation
          custom-button.example-button(parallelogram square paralelogram_animation) paralelogram_animation
          custom-button.example-button(parallelogram square dark) dark
          custom-button.example-button(parallelogram square light) light
          custom-button.example-button(parallelogram square transparent) transparent
          custom-button.example-button(parallelogram square transparent_to_light) transparent_to_light
      .buttons-box
        .buttons-text Rounded buttons
        .buttons
          custom-button.example-button(rounded) vanilla
          custom-button.example-button(rounded zoom_animation) zoom_animation
          custom-button.example-button(rounded paralelogram_animation) paralelogram_animation
          custom-button.example-button(rounded dark) dark
          custom-button.example-button(rounded light) light
          custom-button.example-button(rounded transparent) transparent
          custom-button.example-button(rounded transparent_to_light) transparent_to_light
      .buttons-box
        .buttons-text With_border buttons
        .buttons
          custom-button.example-button(with_border) vanilla
          custom-button.example-button(with_border zoom_animation) zoom_animation
          custom-button.example-button(with_border paralelogram_animation) paralelogram_animation
          custom-button.example-button(with_border dark) dark
          custom-button.example-button(with_border light) light
          custom-button.example-button(with_border transparent) transparent
          custom-button.example-button(with_border transparent_to_light) transparent_to_light
      .buttons-box
        .buttons-text Disabled buttons
        .buttons
          custom-button.example-button(disabled) vanilla
          custom-button.example-button(disabled zoom_animation) zoom_animation
          custom-button.example-button(disabled paralelogram_animation) paralelogram_animation
          custom-button.example-button(disabled dark) dark
          custom-button.example-button(disabled light) light
          custom-button.example-button(disabled transparent) transparent
          custom-button.example-button(disabled transparent_to_light) transparent_to_light
</template>

<script>
  import { mapGetters } from 'vuex'

  export default {
    name: 'buttons',
    computed: {
      ...mapGetters([
        'user',
        'roles'
      ])
    }
  }
</script>

<style lang="sass" scoped>
  #buttons-example-view
    .buttons-examples
      margin-bottom: 100px
      .buttons-box
        background: #DDD
        padding: 5px 10px
        .buttons-text
          font-size: 30px
          line-height: 46px
          text-align: center
        .buttons
          display: flex
          align-items: center
          justify-content: center
          padding: 10px 5px 25px 5px
          border-bottom: 1px solid #888
          .example-button
            margin: 0px 5px
</style>
