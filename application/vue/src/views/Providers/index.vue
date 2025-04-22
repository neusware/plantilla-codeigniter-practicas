<template lang="pug">
.providers-list-component
  title-box(:icon='$helpers.getRouteIcon()', :title='$helpers.getRouteTitle()')
    add-new-thing(
      :disabled='deleting',
      v-if='$helpers.isAdmin()',
      text='Añadir Proveedor',
      @click='$modal.show("provider")'
    )

  .filters-box
    custom-input.filter-item(
      v-model='filters.buscador',
      placeholder='Buscar Proveedor...',
      @keyup.enter.native='fetchData(1)'
    )
    custom-button.add-item(:disabled='deleting', @click.native='fetchData(1)') Buscar

  .top-pagination(v-if='pagination.total_pages > 1')
    .pagination-box
      el-pagination(
        layout='prev, pager, next',
        :page-size='pagination.limit',
        :current-page='pagination.curr_page',
        :page-count='pagination.total_pages',
        @current-change='changePagination'
      )

  el-table.provider-table.custom-table(
    :data='providerList',
    fit='',
    stripe,
    v-loading='loading',
    :element-loading-text='!deleting ? "Cargando..." : "Borrando..."'
  )
    el-table-column(label='Nombre', prop='nombre', min-width='190px')
    el-table-column(label='CIF', prop='cif', min-width='190px', align='center')
    el-table-column(
      label='Email',
      prop='email',
      min-width='190px',
      align='center'
    )
    el-table-column(
      label='Teléfono',
      prop='phone',
      min-width='120px',
      align='center'
    )
    el-table-column(label='Acciones', min-width='110px', align='center')
      template(slot-scope='scope')
        el-tooltip(effect='light', content='Editar Proveedor', placement='top')
          svg-icon.control-icon.cus(
            icon-class='icono_editar',
            @click.prevent.native.stop='handleEdit(scope.row)'
          )
        el-tooltip(
          v-if='$helpers.isAdmin()',
          effect='light',
          content='Eliminar Proveedor',
          placement='top-end'
        )
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
  import ProviderApi from '@/api/ProviderApi'
  import RouteFilters from '@/mixins/RouteFilters'

  export default {
    name: 'provider',
    mixins: [RouteFilters],
    mounted() {
      // Emito el evento por el bus global
      this.$bus.$on('recargarListaProveedores', () => {
        // Lectura de datos pasandole la pagincacion actual
        this.fetchData(this.pagination.curr_page)
      })

      // fb
      console.log('mounted() en componente provider ejecutado')
      // Lectura de datos pasandole la pagincacion actual
      this.fetchData(this.pagination.curr_page)
    },

    data() {
      return {
        // propiedades de la instancaia vue
        // flags
        loading: true,
        deleting: true,
        // propiedad que almacena los datos
        providerList: [],
        pagination: {
          curr_page: 1,
          total_pages: 1,
          limit: 10,
        },
        // propiedad para el input, con valor por defecto
        filters: {
          buscador: '',
        },
      }
    },

    methods: {
      // lectura de datos a través del getFilteredProviders (filtro + pagincacion)
      fetchData(page = 1) {
        this.loading = true
        this.deleting = false
        this.providerList = []
        this.pagination = {
          curr_page: 1,
          total_pages: 1,
          limit: 10,
        }

        // llamada a la front API
        ProviderApi.getFilteredProviders(this.filters, page)
          .then((response) => {
            this.providerList = response.data.data
            this.pagination = response.data.counts
            // codifica la ruta en la barra de navegacion
            this.encodeFilters()
            this.loading = false
          })
          .catch(() => {
            this.loading = false
          })
      },
      // update de datos a partir del tooltip, recibe por parámtro el provider (scope.row)
      handleEdit(provider) {
        // elabora un objeto con el proovedor pasado por parámetro y lo almacena
        const provider_data = { provider: provider }
        // fb
        console.log(
          'Método handleDelete() - Abriendo modal provider pasándole los datos: ',
          provider
        )
        // ejecuta el modal provider pasándole los datos (objeto provider), el modal incorppora la logica de negocio
        this.$modal.show('provider', JSON.parse(JSON.stringify(provider_data)))
      },
      // delete de datos a partir del tooltip, recibe por parámtro el provider (scope.row), sin embargo, se codifica a piñon el modal y la lógica
      handleDelete(provider) {
        // fb
        console.log(
          'Método handleDelete() - Abriendo modal provider pasándole los datos: ',
          provider
        )
        // ejecuto el modal y lo configuro
        this.$modal.show('dialog', {
          title: 'Eliminar Proveedor',
          text: `¿Seguro que quieres <b>eliminar</b> el proveedor <b>${provider.nombre}</b>?`,
          buttons: [
            // dos objetos, dos btns
            {
              title: 'No',
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
                  'Enviando la solicitud  delete() al back a traves de la ProviderApi '
                )
                // llamada a la front API, elimino
                ProviderApi.delete(provider.id)
                  .then((response) => {
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
              },
            },
          ],
        })
      },
      changePagination(page) {
        this.fetchData(page)
      },
    },
    // observa la ruta en cada cambio
    watch: {
      $route(prev_route, new_route) {
        if (prev_route.name !== new_route.name) this.fetchData()
      },
    },
  }
</script>

<style lang="sass" scoped>
$breakpoint-phones: 480px
$breakpoint-tablets: 768px

// Ajustar los estilos para que coincidan con los de users-list-component
.providers-list-component
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
    .providers-list-component
      .filters-box
        .filter-item
          min-width: 46%
  @media (max-width: $breakpoint-phones)
    .providers-list-component
      .filters-box
        .filter-item
          flex: 1
          min-width: 95%
          margin: 10px
</style>
