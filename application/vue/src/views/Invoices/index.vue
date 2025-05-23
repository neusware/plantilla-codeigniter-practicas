<template lang="pug">
.invoices-list-component
  title-box(:icon='$helpers.getRouteIcon()', :title='$helpers.getRouteTitle()')
    add-new-thing(
      :disabled='deleting',
      v-if='$helpers.isAdmin()',
      text='Añadir Factura',
      @click='handleCreate()'
    )

  .filters-box
    custom-input.filter-item(
      v-model='filters.buscador',
      placeholder='Buscar Factura...',
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
    :data='invoiceList',
    fit='',
    stripe,
    v-loading='loading',
    :element-loading-text='!deleting ? "Cargando..." : "Borrando..."'
  )
    //- el-table-column(label='Id', prop='id', min-width='190px')
    el-table-column(label='Cliente', prop='clients', min-width='190px', align='center')
      template(slot-scope="scope")
        span {{scope.row.clients.nombre}} {{ scope.row.clients.apellido}}
    el-table-column(label='Código', prop='codigo', min-width='120px', align='center')
    el-table-column(label='Fecha', prop='fecha', min-width='190px', align='center')
    el-table-column(label='Total', prop='total', min-width='120px', align='center')
    el-table-column(label='Acciones', min-width='110px', align='center')
      template(slot-scope='scope')
        el-tooltip(effect='light',
          content='Editar Factura',
          placement='top')
          svg-icon.control-icon.cus(
            icon-class='icono_editar',
            @click.prevent.native.stop='handleEdit(scope.row)'
          )
        el-tooltip(v-if='$helpers.isAdmin()',
          effect='light',
          content='Eliminar Factura',
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
import InvoiceApi from '@/api/InvoiceApi'
import RouteFilters from '@/mixins/RouteFilters'

export default {
  name: 'invoice',
  mixins: [RouteFilters],
  mounted() {
    // Listener del evento por el bus global
    this.$bus.$on('recargarListaFacturas', () => {
      // Lectura de datos pasandole la pagincacion actual
      this.fetchData(this.pagination.curr_page)
    })

    // fb
    console.log('mounted() en componente invoice ejecutado')
    // Lectura de datos pasandole la pagincacion actual al montar instancia
    this.fetchData(this.pagination.curr_page)
  },
  // Dejo de escuchar el evento al destruir el componente
  beforeDestroy() {
    // elimino el listener para evitar multiples solicitudes
    this.$bus.$off('recargarListaFacturas')
    console.log('Eliminado listener recargarListaFacturas')
  },

  data() {
    return {
      // propiedades de la instancia vue
      // flags
      loading: true,
      deleting: true,
      // propiedad que almacena los datos
      invoiceList: [],
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
      InvoiceApi.getFilteredInvoices(this.filters, page)
        .then(response => {
          this.invoiceList = response.data.data
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
    handleEdit(invoice) {
      this.encodeFilters('InvoicesTemplate', {id_invoice: invoice.id})
    },
    handleCreate(invoice) {
      this.encodeFilters('InvoicesTemplate', {id_invoice: -1})
    },

    // delete de datos a partir del tooltip, recibe por parámtro el client (scope.row), sin embargo, se codifica a piñon el modal y la lógica
    handleDelete(invoice) {
      // fb
      console.log(
        'Método handleDelete() - Abriendo modal dialog usando los datos: ',
        invoice
      )
      // ejecuto el modal dialog, no client y lo configuro
      this.$modal.show('dialog', {
        title: 'Eliminar Factura',
        text: `¿Seguro que quieres <b>eliminar</b> la factura de <b>${invoice.clients.nombre}</b> con código <b>${invoice.codigo}</b>?`,
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
                'Enviando la solicitud  delete() al back a traves de la InvoiceApi '
              )
              // llamada a la front API, elimino
              InvoiceApi.delete(invoice.id)
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
.invoices-list-component
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
    .invoices-list-component
      .filters-box
        .filter-item
          min-width: 46%
  @media (max-width: $breakpoint-phones)
    .invoices-list-component
      .filters-box
        .filter-item
          flex: 1
          min-width: 95%
          margin: 10px
</style>

