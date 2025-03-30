<template lang="pug">
  .two-sections-view#two-sections-containers-example-view
    .left-subview-box(style="background: #5BAE8C;")
      title-box(:icon="$helpers.getRouteIcon(true)" :title="$helpers.getRouteTitle()")
      h1(style="text-align: center;") Class .left-subview-box
      svg-icon.logo(:icon-class="$helpers.isSideBarOppened() ? 'icono_signlab' : 'logo_signlab'")
    .right-subview-box(style="background: #1B5467;")
      title-box(:icon="$helpers.getRouteIcon(true)" :title="$helpers.getRouteTitle()")
      h1(style="text-align: center;") Class .right-subview-box
      svg-icon.logo(:icon-class="$helpers.isSideBarOppened() ? 'logo_signlab' : 'icono_signlab'")
</template>

<style lang="sass" scoped>
  .logo
    width: 100%
    height: 180px
</style>
