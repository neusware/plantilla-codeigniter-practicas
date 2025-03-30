<template lang="pug">
  button.custom-button(
      :disabled="disabled"
      :class="{'with-border': with_border, rounded: rounded, 'zoom-animation': zoom_animation, 'paralelogram-animation': paralelogram_animation, square: square, parallelogram: parallelogram, thin: thin, 'dark-button': dark, 'light-button': light, transparent: transparent, 'transparent-to-light': transparent_to_light }"
    )
    .button-content(:class="{'space-between': space_between}")
      svg-icon.fake-space-right(v-if="svg_icon_left" :icon-class="svg_icon_left")
      i.fake-space-right(v-if="icon_left" :class="icon_left")
      slot
      svg-icon.fake-space-left(v-if="svg_icon_right" :icon-class="svg_icon_right")
      i.fake-space-left(v-if="icon_right" :class="icon_right")
    .underlined(v-if="underlined")
</template>

<script>
  export default {
    props: {
      disabled: {
        Type: Boolean,
        Default: false
      },
      with_border: {
        Type: Boolean,
        Default: false
      },
      rounded: {
        Type: Boolean,
        Default: false
      },
      zoom_animation: {
        Type: Boolean,
        Default: false
      },
      paralelogram_animation: {
        Type: Boolean,
        Default: false
      },
      thin: {
        Type: Boolean,
        Default: false
      },
      square: {
        Type: Boolean,
        Default: false
      },
      parallelogram: {
        Type: Boolean,
        Default: false
      },
      dark: {
        Type: Boolean,
        Default: false
      },
      light: {
        Type: Boolean,
        Default: false
      },
      underlined: {
        Type: Boolean,
        Default: false
      },
      transparent: {
        Type: Boolean,
        Default: false
      },
      transparent_to_light: {
        Type: Boolean,
        Default: false
      },
      svg_icon_left: {
        Type: String,
        Default: false
      },
      icon_left: {
        Type: String,
        Default: false
      },
      svg_icon_right: {
        Type: String,
        Default: false
      },
      icon_right: {
        Type: String,
        Default: false
      },
      space_between: {
        Type: Boolean,
        Default: false
      }
    },
    methods: {
      change(val) {
        this.$emit('input', val)
      }
    }
  }
</script>

<style lang="sass" scoped>
  $mainButtonColor: #1b5467
  $lightButtonColor: #1b5467a8

  .custom-button
    border-radius: 10px
    padding: 12px 25px
    font-size: var(--hight-font-size)
    color: #FFF
    border: none
    cursor: pointer
    background-color: $mainButtonColor
    outline: none
    background-position: center
    transition: background 0.8s
    &:hover:not(:disabled)
      background: $lightButtonColor radial-gradient(circle, transparent 1%, $lightButtonColor 1%) center/15000%
    &:active:not(:disabled)
      background-color: #CCC
      background-size: 100%
      transition: background 0s
    &:disabled
      cursor: auto
      opacity: 0.5
      background: #AAA !important
      color: #393E43 !important
    &.underlined
      border-bottom: 1px solid #888
      margin-top: 3px
    .button-content
      display: flex
      align-items: center
      justify-content: center
      &.space-between
        justify-content: space-between

  .rounded.custom-button
    border-radius: 100px
    &:hover:not(:disabled)
    &:active:not(:disabled)
    &:disabled
      border: 1px solid transparent !important

  // < animations >
  .zoom-animation.custom-button
    transition: transform .3s
    &:hover:not(:disabled)
      transform: scale(1.05)
      transition: transform .3s
    &:active:not(:disabled)
    &:disabled

  .paralelogram-animation.custom-button
    transform: skew(0deg)
    transition: transform .3s
    .button-content
      transform: skew(0deg)
      transition: transform .3s
    &:hover:not(:disabled)
      transform: skew(-20deg)
      transition: transform .3s
      .button-content
        transform: skew(20deg)
        transition: transform .3s

    &:active:not(:disabled)
    &:disabled
    .button-content
      transform: skew(0deg)
  // </ animations >

  .thin.custom-button
    padding: 7px 25px
    &:hover:not(:disabled)
    &:active:not(:disabled)
    &:disabled

  .square.custom-button
    border-radius: 0px
    &:hover:not(:disabled)
    &:active:not(:disabled)
    &:disabled

  .parallelogram.custom-button
    transform: skew(-20deg)
    &.zoom-animation:hover
      transform: scale(1.05) skew(-20deg) !important
      transition: transform .3s !important
    &.paralelogram-animation:hover
      transform: skew(0deg) !important
      transition: transform .3s !important
      .button-content
        transform: skew(0deg)
    &:active:not(:disabled)
    &:disabled
    .button-content
      transform: skew(20deg)

  .dark-button.custom-button
    background-color: #444
    color: #FFF
    &:hover:not(:disabled)
      background: #666 radial-gradient(circle, transparent 1%, #666 1%) center/15000%
      color: #FFF
    &:active:not(:disabled)
      background-color: #CCC
      background-size: 100%
      transition: background 0s
      color: #FFF

  .light-button.custom-button
    background-color: #FFF
    color: $mainButtonColor !important
    color: #444
    &:hover:not(:disabled)
      background: $mainButtonColor radial-gradient(circle, transparent 1%, $mainButtonColor 1%) center/15000%
      color: #FFF !important
    &:active:not(:disabled)
      background-color: #CCC
      background-size: 100%
      transition: background 0s
      color: #FFF

  .transparent
    background-color: transparent
    color: #444
    &:hover:not(:disabled)
      background: $mainButtonColor radial-gradient(circle, transparent 1%, $mainButtonColor 1%) center/15000%
      color: #FFF
    &:active:not(:disabled)
      background-color: #CCC
      background-size: 100%
      transition: background 0s
      color: #FFF

  .transparent-to-light.custom-button
    background-color: transparent
    color: #444
    &:hover:not(:disabled)
      background: #FFF radial-gradient(circle, transparent 1%, #CCC 1%) center/15000%
      color: $mainButtonColor !important
      color: #FFF
    &:active:not(:disabled)
      background-color: #CCC
      background-size: 100%
      transition: background 0s
      color: #FFF

  .with-border
    &.custom-button
      border: 1px solid #FFF !important
      &:hover:not(:disabled)
        border: 1px solid #FFF !important
    &.dark-button
      &:hover:not(:disabled)
    &.light-button
      &:hover:not(:disabled)
    &.transparent-to-light
      &:hover:not(:disabled)
</style>
