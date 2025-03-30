<template lang="pug">
  .full-container-with-padding#full-container-with-padding-containers-example-view(style="background: #5BAE8C;")
    title-box(:icon="$helpers.getRouteIcon(true)" :title="$helpers.getRouteTitle()")
    h1(style="text-align: center;") Class .full-container-with-padding
    svg-icon.logo(:icon-class="$helpers.isSideBarOppened() ? 'icono_signlab' : 'logo_signlab'")
</template>

<style lang="sass" scoped>
  .logo
    width: 100%
    height: 50%
</style>
