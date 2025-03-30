<template lang="pug">
  .bigger-container#bigger-container-containers-example-view(style="background: #5BAE8C;")
    title-box(:icon="$helpers.getRouteIcon(true)" :title="$helpers.getRouteTitle()")
    h1(style="text-align: center;") Class .bigger-container
    svg-icon.logo(:icon-class="$helpers.isSideBarOppened() ? 'icono_signlab' : 'logo_signlab'")
</template>

<style lang="sass" scoped>
  .logo
    width: 100%
    height: 50%
</style>
