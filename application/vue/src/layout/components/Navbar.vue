<template lang="pug">
  el-menu.navbar(mode="horizontal")
    hamburger.hamburger-container(:toggleClick="toggleSideBar" :isActive="sidebar.opened")
    breadcrumb

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
        //- el-dropdown-item
          span(@click="$router.push('/mi-perfil')" style="display:block;") Editar perfil
        //- el-dropdown-item(divided)
        el-dropdown-item()
          span(@click="logout" style="display:block;") Cerrar Sesión
</template>

<script>
import { mapGetters } from 'vuex'
import Breadcrumb from '@/components/Breadcrumb'
import Hamburger from '@/components/Hamburger'

export default {
  components: {
    Breadcrumb,
    Hamburger
  },
  mounted() {
    this.$bus.$on('userAvatarChanged', () => this.reloadAvatar())
  },
  data() {
    return {
      show_avatar: true
    }
  },
  computed: {
    ...mapGetters([
      'sidebar',
      'avatar'
    ])
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
    }
  }
}
</script>

<style lang="sass" scoped>
  $breakpoint-phones: 480px
  $breakpoint-tablets: 768px

  .app-wrapper.hideSidebar.mobile, .app-wrapper.mobile
    .navbar
      width: 100% !important

  .hideSidebar
    .navbar
      width: calc(100% - #{var(--minimized-sidebar-width)}) !important

  .navbar
    width: calc(100% - #{var(--sidebar-width)}) !important
    transition: .3s
    position: fixed
    z-index: 100
    height: 60px
    line-height: 60px
    border-radius: 0px !important
    .hamburger-container
      line-height: 67px
      height: 60px
      float: left
      padding: 0 5px 0px 8px
    .screenfull
      position: absolute
      right: 90px
      top: 16px
      color: red
    .scope-name-box
      left: 50%
      top: 40px
      width: 210px
      // El margin left es la mitad del width
      margin-left: -105px
      height: 40px
      display: flex
      justify-content: center
      align-items: center
      position: absolute
      background: #FFF
      text-align: center
      padding: 5px 15px
      font-style: italic
      border: 2px solid #e6e6e6
      border-radius: 10px
      font-size: 17px
      .scope-text
        margin-right: 5px
      .scope-name
        font-weight: bold
        color: var(--main-color)
    .avatar-container
      height: 60px
      display: inline-block
      position: absolute
      right: 35px
      .el-icon-caret-bottom
        position: absolute
        right: -5px
        top: 45px
        font-size: 12px
      .avatar-wrapper
        width: 55px
        height: 55px
        cursor: pointer
        margin-top: 2px
        border: 2px solid var(--main-color)
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
  @media (max-width: $breakpoint-phones)
    .el-icon-caret-bottom
      top: 40px !important
    .scope-name-box
      left: 50% !important
      top: 44px !important
      width: 175px !important
      height: 30px !important
      // El margin left es la mitad del width
      margin-left: -80px !important
      font-size: 14px !important
    .avatar-wrapper
      width: 40px !important
      height: 40px !important
      margin-top: 10px !important
      .avatar-box
        .user-avatar
          width: 34px !important
          height: 34px !important
          position: relative
          top: -3px
          right: 2px
        .profile-img
</style>
