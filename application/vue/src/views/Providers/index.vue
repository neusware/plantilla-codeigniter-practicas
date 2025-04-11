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
    custom-button.add-item(
      :disabled='deleting',
      @click.native='fetchData(1)'
    ) Buscar

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
    el-table-column(label='Email', prop='email', min-width='190px', align='center')
    el-table-column(
      label='Teléfono',
      prop='phone',
      min-width='120px',
      align='center'
    )
    el-table-column(label='Acciones', min-width='110px', align='center')
      template(slot-scope='scope')
        el-tooltip(
          effect='light',
          content='Editar Proveedor',
          placement='top'
          )
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
      this.$bus.$on('recargarListaProveedores', () => {
        this.fetchData(this.pagination.curr_page)
      })

      console.log('mounted() en componente provider ejecutado')
      this.fetchData(this.pagination.curr_page)
    },

    data() {
      return {
        loading: true,
        deleting: true,
        providerList: [],
        pagination: {
          curr_page: 1,
          total_pages: 1,
          limit: 10
        },
        filters: {
          buscador: ''
        }
      }
    },

    methods: {
      fetchData(page = 1) {
        this.loading = true
        this.deleting = false
        this.providerList = []
        this.pagination = {
          curr_page: 1,
          total_pages: 1,
          limit: 10
        }

        ProviderApi.getFilteredProviders(this.filters, page)
          .then((response) => {
            this.providerList = response.data.data
            this.pagination = response.data.counts
            this.encodeFilters()
            this.loading = false
          })
          .catch(() => {
            this.loading = false
          })
      },
      handleEdit(provider) {
        const provider_data = { provider: provider }
        console.log(
          'Método handleDelete() - Abriendo modal provider pasándole los datos: ',
          provider
        )
        this.$modal.show('provider', JSON.parse(JSON.stringify(provider_data)))
      },
      handleDelete(provider) {
        console.log(
          'Método handleDelete() - Abriendo modal provider pasándole los datos: ',
          provider
        )
        this.$modal.show('dialog', {
          title: 'Eliminar Proveedor',
          text: `¿Seguro que quieres <b>eliminar</b> el proveedor <b>${provider.nombre}</b>?`,
          buttons: [
            // dos objetos, dos btns
            {
              title: 'No'
            },
            // btn2
            {
              title: 'Sí, borrar',
              default: true,
              // función flecha en click con la logica para request a través de la API
              handler: () => {
                this.loading = true
                this.deleting = true
                console.log(
                  'Enviando la solicitud  delete() al back a traves de la ProviderApi '
                )
                ProviderApi.delete(provider.id)
                  .then((response) => {
                    this.fetchData(1)
                  })
                  .catch(() => {
                    this.loading = false
                    this.deleting = false
                  })
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
        if (prev_route.name !== new_route.name) this.fetchData()
      }
    }
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