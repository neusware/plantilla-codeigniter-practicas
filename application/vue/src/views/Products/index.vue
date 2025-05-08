<template lang="pug">
.products-list-component
  title-box(:icon='$helpers.getRouteIcon()', :title='$helpers.getRouteTitle()')
    add-new-thing(
      :disabled='deleting',
      v-if='$helpers.isAdmin()',
      text='Añadir Producto',
      @click='$modal.show("product")'
    )

  .filters-box
    custom-input.filter-item(
      v-model='filters.buscador',
      placeholder='Buscar Producto...',
      @keyup.enter.native='fetchData(1)'
    )
    custom-button.add-item(:disabled='deleting', @click.native='fetchData(1)') Buscar

  //- .top-pagination(v-if='pagination.total_pages > 1')
  //-   .pagination-box
  //-     el-pagination(
  //-       layout='prev, pager, next',
  //-       :page-size='pagination.limit',
  //-       :current-page='pagination.curr_page',
  //-       :page-count='pagination.total_pages',
  //-       @current-change='changePagination'
  //-     )

  el-table.product-table.custom-table(
    :data='productList',
    fit='',
    stripe,
    v-loading='loading',
    :element-loading-text='!deleting ? "Cargando..." : "Borrando..."'
  )
    //- el-table-column(label='Id', prop='id', min-width='190px')
    el-table-column(label='Nombre', prop='nombre', min-width='190px', align='center')
    el-table-column(label='Código', prop='codigo', min-width='120px', align='center')
    el-table-column(label='Proovedor', prop='provider', min-width='190px', align='center')
      template(slot-scope='scope')
        span {{  scope.row.provider.nombre }}
    el-table-column(label='Stock', prop='stock', min-width='120px', align='center')
    el-table-column(label='Precio', prop='precio', min-width='120px', align='center')
     template(slot-scope='scope')
          span {{ $helpers.formatNumber(scope.row.precio, "€") }}
    el-table-column(label='Acciones', min-width='110px', align='center')
      template(slot-scope='scope')
        el-tooltip(effect='light',
          content='Editar Producto',
          placement='top')
          svg-icon.control-icon.cus(
            icon-class='icono_editar',
            @click.prevent.native.stop='handleEdit(scope.row)'
          )
        el-tooltip(v-if='$helpers.isAdmin()',
          effect='light',
          content='Eliminar Producto',
          placement='top-end')
          svg-icon.control-icon.cus(
            icon-class='icono_eliminar',
            @click.prevent.native.stop='handleDelete(scope.row)'
          )

  .bottom-pagination(v-if='pagination.total_pages > 1')
    .pagination-box
      el-pagination(
        layout='prev, pager, next',
        :page-size='pagination.limit',
        :current-page='pagination.curr_page',
        :page-count='pagination.total_pages',
        @current-change='changePagination'
      )
</template>

<script>
import ProductApi from '@/api/ProductApi'
import RouteFilters from '@/mixins/RouteFilters'

export default {
  name: 'product',
  mixins: [RouteFilters],
  mounted() {
    // Listener del evento por el bus global
    this.$bus.$on('recargarListaProductos', () => {
      // Lectura de datos pasandole la pagincacion actual
      this.fetchData(this.pagination.curr_page)
    })

    // fb
    console.log('mounted() en componente producto ejecutado')
    // Lectura de datos pasandole la pagincacion actual al montar instancia
    this.fetchData(this.pagination.curr_page)
  },
  // Dejo de escuchar el evento al destruir el componente
  beforeDestroy() {
    // Elimina elimino el listener para evitar multiples solicitudes
    this.$bus.$off('recargarListaProductos')
    console.log('Eliminado listener recargarListaProductos')
  },

  data() {
    return {
      // propiedades de la instancaia vue
      // flags
      loading: true,
      deleting: true,
      // propiedad que almacena los datos
      productList: [],
      pagination: {
        curr_page: 1,
        total_pages: 1,
        limit: 10
      },
      // propiedad para el input, con valor por defecto
      filters: {
        buscador: ''
      }
    }
  },

  methods: {
    // lectura de datos a través del getFilteredProducts (filtro + pagincacion)
    fetchData(page = 1) {
      this.loading = true
      this.deleting = false
      this.productList = []
      this.pagination = {
        curr_page: 1,
        total_pages: 1,
        limit: 10
      }

      // llamada front API
      ProductApi.getFilteredProducts(this.filters, page)
        .then(response => {
          this.productList = response.data.data
          this.pagination = response.data.counts
          // codifica la ruta en la barra de navegacion
          this.encodeFilters()
          this.loading = false
        })
        .catch(() => {
          // evita el freeze
          this.loading = false
        })
    },
    // update de datos a partir del tooltip, recibe por parámtro el product (scope.row)
    handleEdit(product) {
      // elaboro un objeto con el proovedor pasado por parámetro y lo almaceno
      const product_data = { product: product }
      // fb
      console.log(
        'Método handleEdit() - Abriendo modal product pasándole los datos: ',
        product
      )
      // ejecuta el modal product pasándole los datos (objeto product), que  los recogera en event,params.(product), es como se comunican
      this.$modal.show('product', JSON.parse(JSON.stringify(product_data)))
    },

    // delete de datos a partir del tooltip, recibe por parámtro el product (scope.row), sin embargo, se codifica a piñon el modal y la lógica
    handleDelete(product) {
      // fb
      console.log(
        'Método handleDelete() - Abriendo modal dialog usando los datos: ',
        product
      )
      // ejecuto el modal dialog, no product y lo configuro
      this.$modal.show('dialog', {
        title: 'Eliminar Producto',
        text: `¿Seguro que quieres <b>eliminar</b> el producto <b>${product.nombre}</b>?`,
        buttons: [
          // dos objetos, dos btns
          {
            title: 'No'
          },
          // btn2
          {
            title: 'Sí, borrar',
            default: true,
            // función flecha en click btn con la logica para request a través de la API
            handler: () => {
              // seteo de flags
              this.loading = true
              this.deleting = true
              // fb
              console.log(
                'Enviando la solicitud  delete() al back a traves de la ProductApi '
              )
              // llamada a la front API, elimino
              ProductApi.delete(product.id)
                .then(response => {
                  // lectura x defecto para refrescar
                  this.fetchData(1)
                })
                .catch(() => {
                  // en excepción, seteo de flags
                  this.loading = false
                  this.deleting = false
                })
              // escondo el modal
              this.$modal.hide('dialog')
            }
          }
        ]
      })
    },
    changePagination(page) {
      this.fetchData(page)
    }
  },
  // observa la ruta en cada cambio
  watch: {
    $route(prev_route, new_route) {
      // si cambia la ruta lectura
      if (prev_route.name !== new_route.name) this.fetchData()
    }
  }
}
</script>

<style lang="sass" scoped>
$breakpoint-phones: 480px
$breakpoint-tablets: 768px

// Ajustar los estilos para que coincidan con los de users-list-component
.products-list-component
  .filters-box
    display: flex
    flex-wrap: wrap
    .filter-item
      min-width: 200px
      max-width: 200px
      margin: 10px
    .add-item
      margin: 10px
      width: 150px

  @media (max-width: $breakpoint-tablets)
    .products-list-component
      .filters-box
        .filter-item
          min-width: 46%
  @media (max-width: $breakpoint-phones)
    .products-list-component
      .filters-box
        .filter-item
          flex: 1
          min-width: 95%
          margin: 10px
</style>

