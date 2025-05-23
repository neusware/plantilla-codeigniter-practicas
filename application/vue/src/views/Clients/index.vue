<template lang="pug">
.clients-list-component
  title-box(:icon='$helpers.getRouteIcon()', :title='$helpers.getRouteTitle()')
    add-new-thing(
      :disabled='deleting',
      v-if='$helpers.isAdmin()',
      text='Añadir Cliente',
      @click='$modal.show("client")'
    )

  .filters-box
    custom-input.filter-item(
      v-model='filters.buscador',
      placeholder='Buscar Cliente...',
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

  el-table.client-table.custom-table(
    :data='clientList',
    fit='',
    stripe,
    v-loading='loading',
    :element-loading-text='!deleting ? "Cargando..." : "Borrando..."'
  )
    //- el-table-column(label='Id', prop='id', min-width='190px')
    el-table-column(label='Email', prop='email', min-width='190px', align='center')
    el-table-column(label='Nombre', prop='nombre', min-width='120px', align='center')
    el-table-column(label='Apellido', prop='apellido', min-width='190px', align='center')
    el-table-column(label='Dirección', prop='direccion', min-width='120px', align='center')
    el-table-column(label='Acciones', min-width='110px', align='center')
      template(slot-scope='scope')
        el-tooltip(effect='light',
          content='Editar Cliente',
          placement='top')
          svg-icon.control-icon.cus(
            icon-class='icono_editar',
            @click.prevent.native.stop='handleEdit(scope.row)'
          )
        el-tooltip(v-if='$helpers.isAdmin()',
          effect='light',
          content='Eliminar Cliente',
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
import ClientApi from '@/api/ClientApi'
import RouteFilters from '@/mixins/RouteFilters'

export default {
  name: 'client',
  mixins: [RouteFilters],
  mounted() {
    // Listener del evento por el bus global
    this.$bus.$on('recargarListaClientes', () => {
      // Lectura de datos pasandole la pagincacion actual
      this.fetchData(this.pagination.curr_page)
    })

    // fb
    console.log('mounted() en componente client ejecutado')
    // Lectura de datos pasandole la pagincacion actual al montar instancia
    this.fetchData(this.pagination.curr_page)
  },
  // Dejo de escuchar el evento al destruir el componente
  beforeDestroy() {
    // elimino el listener para evitar multiples solicitudes
    this.$bus.$off('recargarListaClientes')
    console.log('Eliminado listener recargarListaClientes')
  },

  data() {
    return {
      // propiedades de la instancia vue
      // flags
      loading: true,
      deleting: true,
      // propiedad que almacena los datos
      clientList: [],
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
    // lectura de datos a través del getFilteredClients (filtro + pagincacion)
    fetchData(page = 1) {
      this.loading = true
      this.deleting = false
      this.clientList = []
      this.pagination = {
        curr_page: 1,
        total_pages: 1,
        limit: 10
      }

      // request front API
      ClientApi.getFilteredClients(this.filters, page)
        .then(response => {
          this.clientList = response.data.data
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
    // update de datos a partir de event en tooltip, recibe por parámtro el client(scope.row)
    handleEdit(client) {
      // elaboro un objeto con el proovedor pasado por parámetro y lo almaceno
      const client_data = { client: client }
      // fb
      console.log(
        'Método handleEdit() - Abriendo modal client pasándole los datos: ',
        client
      )
      // ejecuta el modal client pasándole los datos (objeto client), que  los recogera en event,params.(client), es como se comunican
      this.$modal.show('client', JSON.parse(JSON.stringify(client_data)))
    },

    // delete de datos a partir del tooltip, recibe por parámtro el client (scope.row), sin embargo, se codifica a piñon el modal y la lógica
    handleDelete(client) {
      // fb
      console.log(
        'Método handleDelete() - Abriendo modal dialog usando los datos: ',
        client
      )
      // ejecuto el modal dialog, no client y lo configuro
      this.$modal.show('dialog', {
        title: 'Eliminar Cliente',
        text: `¿Seguro que quieres <b>eliminar</b> el cliente <b>${client.nombre}</b>?`,
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
                'Enviando la solicitud  delete() al back a traves de la ClientApi '
              )
              // llamada a la front API, elimino
              ClientApi.delete(client.id)
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

// Ajustar los estilos para que coincidan con los de clients-list-component
.clients-list-component
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
    .clients-list-component
      .filters-box
        .filter-item
          min-width: 46%
  @media (max-width: $breakpoint-phones)
    .clients-list-component
      .filters-box
        .filter-item
          flex: 1
          min-width: 95%
          margin: 10px
</style>

