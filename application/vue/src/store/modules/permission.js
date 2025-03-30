import { asyncRouterMap, constantRouterMap } from '@/router'

/**
 * 通过meta.role判断是否与当前用户权限匹配
 * @param roles
 * @param route
 */
function hasPermission(roles, route) {
  if (route.meta && route.meta.roles) {
    return roles.some(role => route.meta.roles.indexOf(role) >= 0)
  } else {
    return true
  }
}

/**
 * 递归过滤异步路由表，返回符合用户角色权限的路由表
 * @param asyncRouterMap
 * @param roles
 */
function filterAsyncRouter(asyncRouterMap, roles) {
  const accessedRouters = asyncRouterMap.filter(route => {
    if (hasPermission(roles, route)) {
      if (route.children && route.children.length) {
        route.children = filterAsyncRouter(route.children, roles)
      }
      return true
    }
    return false
  })
  return accessedRouters
}

function moveItemFromArrayToNewPosition(arr, old_index, new_index) {
  while (old_index < 0) {
    old_index += arr.length
  }
  while (new_index < 0) {
    new_index += arr.length
  }
  if (new_index >= arr.length) {
    var k = new_index - arr.length
    while ((k--) + 1) {
      arr.push(undefined)
    }
  }
  arr.splice(new_index, 0, arr.splice(old_index, 1)[0])

  return arr
}

const permission = {
  state: {
    routers: constantRouterMap,
    addRouters: []
  },
  mutations: {
    SET_ROUTERS: (state, routers) => {
      state.addRouters = routers
      // state.routers = constantRouterMap.concat(routers)
      const rutas = constantRouterMap.concat(routers)
      const routes_to_change_positions = rutas.reduce(function(filtered_arr, actual_option, curr_index) {
        if (actual_option.force_router_position) {
          filtered_arr.push({ path: actual_option.path, to: (actual_option.force_router_position - 1) })
        }

        return filtered_arr
      }, []).sort((a, b) => parseInt(a.to) - parseInt(b.to))

      routes_to_change_positions.forEach((route_to_move, i) => {
        const position_from_route_to_change = rutas.map(item => item.path).indexOf(route_to_move.path)
        const position_to_route_to_move = rutas.map(item => item.path).indexOf(rutas.filter(item => !item.hidden).map(item => item.path)[route_to_move.to])
        moveItemFromArrayToNewPosition(rutas, position_from_route_to_change, position_to_route_to_move)
      })

      state.routers = rutas
    }
  },
  actions: {
    GenerateRoutes({ commit }, data) {
      return new Promise(resolve => {
        const { roles } = data
        const accessedRouters = filterAsyncRouter(asyncRouterMap, roles)
        // let accessedRouters
        // if (roles.indexOf('admin') >= 0) {
        //   accessedRouters = asyncRouterMap
        // } else {
        //   accessedRouters = filterAsyncRouter(asyncRouterMap, roles)
        // }
        commit('SET_ROUTERS', accessedRouters)
        resolve()
      })
    }
  }
}

export default permission
