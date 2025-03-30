// Guardar aquí las funciones que sean útiles para usarles en los archivos .js, ya sean de configuración o similar

export function mergeRoutes(arr_routes_1, arr_routes_2) {
  const merged_arr_routes = []

  arr_routes_1.forEach((route, i) => {
    const final_route = route
    const found_same_route = arr_routes_2.find(route_2 => {
      return route_2.path === route.path
    })

    if (found_same_route) {
      final_route.children = [...final_route.children, ...found_same_route.children]
    }

    merged_arr_routes.push(final_route)
  })

  return merged_arr_routes
}
