<template lang="pug">
  el-breadcrumb.app-breadcrumb(separator="/")
    transition-group(name="breadcrumb")
      el-breadcrumb-item(v-for="(item,index)  in levelList" :key="item.path")
        template(v-if="item.meta.title_gestion && $helpers.getUser().id_ambito_preparacion_plataforma == 2")
          span(v-if="item.redirect==='noredirect'||index==levelList.length-1" class="no-redirect") {{item.meta.title_gestion}}
          router-link(v-else :to="item.redirect||item.path") {{item.meta.title_gestion}}
        template(v-else)
          span(v-if="item.redirect==='noredirect'||index==levelList.length-1" class="no-redirect") {{item.meta.title}}
          router-link(v-else :to="item.redirect||item.path") {{item.meta.title}}
</template>

<script>
export default {
  created() {
    this.getBreadcrumb()
  },
  data() {
    return {
      levelList: null
    }
  },
  watch: {
    $route() {
      this.getBreadcrumb()
    }
  },
  methods: {
    getBreadcrumb() {
      let matched = this.$route.matched.filter(item => item.name)
      const first = matched[0]
      if (first && first.name !== 'Inicio') {
        matched = [{ path: '/dashboard', meta: { title: 'Inicio' }}].concat(matched)
      }
      this.levelList = matched
    }
  }
}
</script>

<style lang="sass" scoped>
  $breakpoint-phones: 480px

  .app-breadcrumb.el-breadcrumb
    display: inline-block
    font-size: 14px
    line-height: 60px
    margin-left: 10px
    /deep/ .el-breadcrumb__separator
      margin: 0px 5px
    .no-redirect
      color: #97a8be
      cursor: text

  @media (max-width: $breakpoint-phones)
    .app-breadcrumb.el-breadcrumb
      margin: 0 !important
      font-size: 12px !important
</style>
