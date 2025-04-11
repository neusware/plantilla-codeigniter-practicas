<template lang="pug">
  .topbar-menu-wrapper
    hamburger.hamburger-container(v-if="$helpers.isSideBarHidden()" :toggleClick="toggleSideBar" :isActive="sidebar.opened")

    router-link(:to="'/'" )
      svg-icon.main-logo(icon-class="logo_signlab_light")

    .menu-items-box-parent(v-if="!$helpers.isSideBarHidden()")
      .menu-items-box
        template(v-for="item in routes" v-if="!item.hidden&&item.children")

          router-link(v-if="hasOneShowingChildren(item.children) && !item.children[0].children&&!item.alwaysShow" :to="item.path+'/'+item.children[0].path" :key="item.children[0].name")
            el-menu-item.topbar-menu-item(:index="item.path+'/'+item.children[0].path")
              svg-icon.big-icon(v-if="item.children[0].meta&&item.children[0].meta.icon" :icon-class="item.children[0].meta.icon")
              span.topbar-main-item(v-if="item.children[0].meta&&item.children[0].meta.title" slot="title") {{item.children[0].meta.title}}

          el-submenu.topbar-submenu-parent(v-else :index="item.name || item.path" :key="item.name")
            template(slot="title")
              svg-icon.big-icon(v-if="item.meta&&item.meta.icon" :icon-class="item.meta.icon")
              span.topbar-main-item(v-if="item.meta&&item.meta.title" slot="title") {{item.meta.title}}

            template(v-for="child in item.children" v-if="!child.hidden")
              sidebar-item(:is-nest="true" class="nest-menu" v-if="child.children&&child.children.length>0" :routes="[child]" :key="child.path")

              router-link(v-else :to="item.path+'/'+child.path" :key="child.name")
                el-menu-item.topbar-submenu-item(:index="item.path+'/'+child.path")
                  svg-icon.medium-icon(v-if="child.meta&&child.meta.icon" :icon-class="child.meta.icon")
                  span.topbar-subitem(v-if="child.meta&&child.meta.title" slot="title") {{child.meta.title}}

    el-dropdown.avatar-container(trigger="click")
      .avatar-wrapper
        .avatar-box(v-if="show_avatar")
          img.profile-img(
            v-if="$helpers.getUser().imagen"
            v-bind:src="$helpers.fileUrl('User', 'imagen', $helpers.getUser().imagen)"
            width="100%"
            height="100%"
            style="cursor: pointer; border-radius: 50%; object-fit: cover;"
          )
          svg-icon.user-avatar(v-else icon-class="user_default")
        i.el-icon-caret-bottom
      el-dropdown-menu.user-dropdown(slot="dropdown")
        el-dropdown-item()
          span(@click="logout" style="display:block;") Cerrar Sesión
</template>

<script>
  import { mapGetters } from 'vuex'
  import Hamburger from '@/components/Hamburger'

  export default {
    name: 'TopbarItem',
    components: {
      Hamburger
    },
    mounted() {
      this.$bus.$on('userAvatarChanged', () => this.reloadAvatar())
    },
    // !recibe las rutas como prop
    props: {
      routes: {
        type: Array
      }
    },
    data() {
      return {
        show_avatar: true
      }
    },
    methods: {
      reloadAvatar() {
        this.show_avatar = false
        setTimeout(() => {
          this.$store.dispatch('GetInfo').then(() => {
            this.show_avatar = true
          })
        }, 100)
      },
      toggleSideBar() {
        this.$store.dispatch('ToggleSideBar')
      },
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
    },
    computed: {
      ...mapGetters([
        'sidebar'
      ])
    }
  }
</script>

<style lang="sass" scoped>
  $breakpoint-tablets: 768px
  .big-icon, .medium-icon
    margin-right: 7px

  .main-logo
    width: 180px !important
    height: 40px !important
    padding: 0px 10px 0px 40px

  .topbar-menu-wrapper
    display: flex
    justify-content: space-between
    align-items: center
    .hamburger-container
      padding-left: 10px
    .menu-items-box-parent
      flex: 1
      display: flex
      justify-content: flex-end
      .menu-items-box
        display: flex
        align-items: center
    .avatar-container
      height: 60px
      display: inline-block
      right: 35px
      margin-left: 40px
      .el-icon-caret-bottom
        color: var(--font-menu-color)
        position: absolute
        right: -5px
        top: 45px
        font-size: 12px
      .avatar-wrapper
        width: 55px
        height: 55px
        cursor: pointer
        margin-top: 2px
        border: 2px solid var(--secondary-color)
        border-radius: 50px
        background: #FFF
        .avatar-box
          width: 100%
          height: 100%
          .profile-img
          .user-avatar
            width: 45px
            height: 45px
            margin-left: 3px
            margin-top: 2px
          .el-icon-caret-bottom
            position: absolute
            right: -5px
            top: 45px
            font-size: 12px

  @media (max-width: $breakpoint-tablets)
    .avatar-container
      right: 10px !important
      .avatar-wrapper
        .user-avatar
          .el-icon-caret-bottom
            top: 20px !important
</style>
