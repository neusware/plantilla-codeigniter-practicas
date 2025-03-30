<template lang="pug">
  el-scrollbar(wrapClass="scrollbar-wrapper")
    el-menu.main-topbar(
      mode="horizontal"
      :default-active="$route.path"
      text-color="#EEE"
    )
      topbar-items.topbar-items-box(:routes="permission_routers")
</template>

<script>
  import { mapGetters } from 'vuex'
  import TopbarItems from './TopbarItems'

  export default {
    components: { TopbarItems },
    computed: {
      ...mapGetters([
        'permission_routers',
        'sidebar'
      ])
    }
  }
</script>
