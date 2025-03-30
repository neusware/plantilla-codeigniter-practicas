<template lang="pug">
  .menu-wrapper
    router-link(:to="'/'" )
      svg-icon.logo(:icon-class="$helpers.isSideBarOppened() ? 'logo_signlab_light' : 'icono_signlab_light'" :class="$helpers.isSideBarOppened() ? '' : 'smaller-icon'")

    .wellcome-message(v-if="$helpers.getUser()" v-show="$helpers.isSideBarOppened()")
      | Bienvenido,
      .student-name &nbsp;{{$helpers.getUser().first_name}}

    template(v-for="item in routes" v-if="!item.hidden&&item.children")
      router-link(v-if="hasOneShowingChildren(item.children) && !item.children[0].children&&!item.alwaysShow" :to="item.path+'/'+item.children[0].path" :key="item.children[0].name")
        el-menu-item(:index="item.path+'/'+item.children[0].path" :class="{'submenu-title-noDropdown':!isNest}")
          svg-icon.big-icon(v-if="item.children[0].meta&&item.children[0].meta.icon" :icon-class="item.children[0].meta.icon")
          span.sidebar-main-item(v-if="item.children[0].meta&&item.children[0].meta.title" slot="title") {{item.children[0].meta.title}}

      el-submenu(v-else :index="item.name||item.path" :key="item.name")
        template(slot="title")
          svg-icon.big-icon(v-if="item.meta&&item.meta.icon" :icon-class="item.meta.icon")
          span.sidebar-main-item(v-if="item.meta&&item.meta.title" slot="title" style="font-size: 17px;") {{item.meta.title}}

        template(v-for="child in item.children" v-if="!child.hidden")
          sidebar-item(:is-nest="true" class="nest-menu" v-if="child.children&&child.children.length>0" :routes="[child]" :key="child.path")

          router-link(v-else :to="item.path+'/'+child.path" :key="child.name")
            el-menu-item(:index="item.path+'/'+child.path")
              svg-icon.medium-icon.fake-space-right(v-if="child.meta&&child.meta.icon" :icon-class="child.meta.icon")
              span.sidebar-subitem(v-if="child.meta&&child.meta.title" slot="title") {{child.meta.title}}

    .close-session(v-show="$helpers.isSideBarOppened()")
      custom-button.logout-button(with_border rounded light @click.native.prevent="logout") Salir
</template>

<script>
  export default {
    name: 'SidebarItem',
    props: {
      routes: {
        type: Array
      },
      isNest: {
        type: Boolean,
        default: false
      }
    },
    methods: {
      logout() {
        const self = this
        this.$store.dispatch('LogOut').then(() => {
          self.$router.push('/login')
        })
      },
      hasOneShowingChildren(children) {
        const showingChildren = children.filter(item => {
          return !item.hidden
        })
        if (showingChildren.length === 1) {
          return true
        }
        return false
      }
    }
  }
</script>

<style lang="sass" scoped>
  .submenu-info-name
    color: var(--font-menu-color)
    font-weight: bold
    text-align: center
    padding: 15px 3px
    background: var(--menu-bg)

  .menu-wrapper > a > li.is-active, .menu-wrapper > li.el-submenu > /deep/ div.is-active
    background: var(--menu-item-selected) !important
    color: var(--font-menu-selectedcolor) !important
    padding-left: 0 !important

  .menu-wrapper > a > li, .menu-wrapper > li.el-submenu > /deep/ div
    color: var(--font-menu-color) !important
    display: flex !important
    align-items: center !important
    background: var(--menu-bg) !important
    padding-left: 0 !important
    &:hover
      background: var(--menu-hover) !important

  .menu-wrapper
    background: var(--menu-bg)
    padding-top: 25px !important
    .logo
      width: 100%
      height: 180px
      margin: 0px !important
      padding: 5px 10px 30px 10px
      &.smaller-logo-icon
        height: 160px
    .wellcome-message
      display: flex
      color: var(--font-menu-color) !important
      font-size: 22px
      font-style: italic
      padding: 10px 5px 15px 25px
      .student-name
        font-weight: bold

    /deep/ .el-menu-item
      color: var(--font-menu-color) !important
      font-size: 17px
      padding-right: 20px !important

    .big-icon
      min-width: 25px
      min-height: 25px
    .medium-icon
      margin-left: 0px !important
      margin-right: 5px !important
    .sidebar-main-item
      font-weight: 700
      white-space: initial
      line-height: normal
    .sidebar-subitem
    .el-menu-item.submenu-title-noDropdown
      display: flex
      align-items: center
    /deep/ .el-icon-arrow-up:before
      content: "\E78F" !important
      color: #FFF !important
    /deep/ .el-icon-arrow-down:before
      content: "\E790" !important
      color: #FFF !important
      font-size: 13px
    /deep/ .el-icon-arrow-down
      right: 10px !important

    /deep/ li .el-submenu
      display: flex
      align-items: center

  .close-session
    text-align: right
    padding: 50px 25px 30px 0px
    .logout-button
</style>
