import Vue from 'vue'
import Router from 'vue-router'

const _import = require('./_import_' + process.env.NODE_ENV)

// in development-env not use lazy-loading, because lazy-loading too many pages will cause webpack hot update too slow. so only in production use lazy-loading;
// detail: https://panjiachen.github.io/vue-element-admin-site/#/lazy-loading

Vue.use(Router)

/* Layout */
// import Layout from '@/layout/Layout'
/* LayoutTopMenu */
import Layout from '@/layout/LayoutTopMenu'

/**
* hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
* alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
*                                if not set alwaysShow, only more than one route under the children
*                                it will becomes nested mode, otherwise not show the root menu
* redirect: noredirect           if `redirect:noredirect` will no redirct in the breadcrumb
* name:'router-name'             the name is used by <keep-alive> (must set!!!)
* meta : {
    title: 'title'               the name show in submenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar,
  }
**/
export const constantRouterMap = [
  { path: '/login', component: _import('login/index'), hidden: true },
  { path: '/resetPassword/:forgotten_password_code', component: _import('resetPassword/index'), hidden: true },
  { path: '/404', component: _import('404'), hidden: true },

  {
    path: '',
    component: Layout,
    redirect: 'dashboard',
    children: [{
      path: 'dashboard',
      component: _import('dashboard/index'),
      name: 'DashboardRoute',
      meta: { title: 'Inicio', icon: 'dashboard', noCache: true }
    }]
  }
]

export default new Router({
  mode: 'history', // Entre otras cosas, quita la "#" de la url
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})

const baseAsyncRouterMap = [
// export const asyncRouterMap = [

  {
    path: '/example',
    component: Layout,
    redirect: '/example/buttons',
    name: 'ExamplesRoute',
    meta: { title: 'Examples', icon: 'example' },
    children: [
      {
        path: 'buttons',
        name: 'ButtonsRoute',
        component: _import('TemplateExamples/buttons/index'),
        meta: { title: 'Buttons', icon: 'example' }
      },
      {
        path: 'form',
        name: 'FormRoute',
        component: _import('TemplateExamples/form/index'),
        meta: { title: 'Form', icon: 'form' }
      },
      {
        path: 'form-with-rows',
        name: 'FormWithRowsRoute',
        component: _import('TemplateExamples/form-with-rows/index'),
        meta: { title: 'Form with rows', icon: 'form' }
      },
      {
        path: 'table',
        name: 'TableRoute',
        component: _import('TemplateExamples/table/index'),
        meta: { title: 'Table', icon: 'table' }
      },
      {
        path: 'calendar',
        name: 'CalendarRoute',
        component: _import('TemplateExamples/calendar/index'),
        meta: { title: 'Calendar', icon: 'table' }
      }
    ]
  },
  {
    path: '/containers',
    component: Layout,
    redirect: '/containers/container',
    name: 'ContainersRoute',
    meta: { title: 'Containers', icon: 'table' },
    children: [
      {
        path: 'container',
        name: 'ContainerRoute',
        component: _import('TemplateExamples/container/index'),
        meta: { title: 'Container', icon: 'example' }
      },
      {
        path: 'bigger-container',
        name: 'BiggerContainerRoute',
        component: _import('TemplateExamples/container/bigger_container'),
        meta: { title: 'Bigger Container', icon: 'example' }
      },
      {
        path: 'full-container',
        name: 'FullContainerRoute',
        component: _import('TemplateExamples/container/full_container'),
        meta: { title: 'Full Container', icon: 'example' }
      },
      {
        path: 'full-container-with-padding',
        name: 'FullContainerWithPaddingRoute',
        component: _import('TemplateExamples/container/full_container_with_padding'),
        meta: { title: 'Full Container With Padding', icon: 'example' }
      },
      {
        path: 'twoSections-container',
        name: 'TwoSectionsContainerRoute',
        component: _import('TemplateExamples/container/two_sections'),
        meta: { title: 'Two Sections Container', icon: 'example' }
      },
      {
        path: 'twoSections-container-with-scroll',
        name: 'TwoSectionsContainerWithScrollRoute',
        component: _import('TemplateExamples/container/two_sections_scroll'),
        meta: { title: 'Two Sections Scroll', icon: 'example' }
      }
    ]
  },
  {
    path: '/onlyAdmins',
    component: Layout,
    meta: { roles: ['admin'] },
    children: [
      {
        path: 'index',
        name: 'onlyAdminsRoute',
        component: _import('TemplateExamples/onlyAdmins/index'),
        meta: { title: 'onlyAdmins', icon: 'form' }
      }
    ]
  },
  {
    path: '/onlyMembers',
    component: Layout,
    meta: { roles: ['members'] },
    children: [
      {
        path: 'index',
        name: 'onlyMembersRoute',
        component: _import('TemplateExamples/onlyMembers/index'),
        meta: { title: 'onlyMembers', icon: 'form' }
      }
    ]
  },
  {
    path: '/usuarios',
    component: Layout,
    // hidden: true,
    children: [
      {
        // el path se mantiene en /usuarios
        path: '',
        // componentes de la /view
        component: _import('Users/index'),
        name: 'UsersList',
        // datos extra /icons
        meta: { title: 'Usuarios', icon: 'peoples' }
      },
      {
        // al path se le agrega un parametro local..../usuario/perfil?id
        path: 'perfil/:id_usuario',
        component: _import('Users/profile'),
        // hidden - no se puede navegar directamente desde la topbar, debe accederse dada una acción
        hidden: true,
        name: 'UsersProfile',
        meta: { title: 'Perfíl' }
      }
    ]
  },
  {
    path: '/providers',
    component: Layout, // ha de estar importado
    children: [
      {
        path: '',
        component: _import('Providers/index'), // ruta correcta
        hidden: false,
        name: 'ProvidersList',
        meta: { title: 'Proveedores', icon: 'tree' }
      }
    ]
  },

  { path: '*', redirect: '/404', hidden: true }
]

export const asyncRouterMap = [...baseAsyncRouterMap]
