<template lang="pug">
  //- Cambiar aquí los colores del sidebar aquí
  el-scrollbar(wrapClass="scrollbar-wrapper")
  	el-menu.main-sidebar(
      mode="vertical"
      :show-timeout="200"
      :default-active="$route.path"
      :collapse="isCollapse"
      text-color="#EEE"
    )
  		sidebar-item(:routes="permission_routers")
</template>

<script>
  import { mapGetters } from 'vuex'
  import SidebarItem from './SidebarItem'

  export default {
    components: { SidebarItem },
    computed: {
      ...mapGetters([
        'permission_routers',
        'sidebar'
      ]),
      isCollapse() {
        return !this.sidebar.opened
      }
    }
  }
</script>
