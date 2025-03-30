<template lang="pug">
  .app-wrapper(:class="classObj")
    .drawer-bg(v-if="device === 'mobile' && sidebar.opened", @click="handleClickOutside")
    .main-container-with-topbar
      sidebar.sidebar-container.sidebar-container-with-topbar
      topbar.topbar-container
      app-main.app-main-with-topbar
</template>

<script>
import { Navbar, Sidebar, Topbar, AppMain } from './components'
import ResizeMixin from './mixin/ResizeHandler'

export default {
  name: 'layout',
  components: {
    Navbar,
    Topbar,
    Sidebar,
    AppMain
  },
  mixins: [ResizeMixin],
  created() {
    this.addClassToBody('has-topbar')
  },
  computed: {
    sidebar() {
      return this.$store.state.app.sidebar
    },
    device() {
      return this.$store.state.app.device
    },
    classObj() {
      return {
        hideSidebar: !this.sidebar.opened,
        withoutAnimation: this.sidebar.withoutAnimation,
        mobile: this.device === 'mobile'
      }
    }
  },
  methods: {
    addClassToBody(clase) {
      document.body.classList.add(clase)
    },
    handleClickOutside() {
      this.$store.dispatch('CloseSideBar', { withoutAnimation: false })
    }
  }
}
</script>

<style lang="sass" scoped>
  @import "src/styles/mixin"

  .app-wrapper
    @include clearfix
    position: relative
    height: 100%
    width: 100%

    .drawer-bg
      background: #000
      opacity: 0.3
      width: 100%
      top: 0
      height: 100%
      position: absolute
      z-index: 700

    .main-container-with-topbar
      min-height: 100vh

  @media(min-width: 1026px)
    .sidebar-container-with-topbar
      display: none
</style>
