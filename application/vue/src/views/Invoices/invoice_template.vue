<template lang="pug">
.invoices-list-component
    title-box(:icon='$helpers.getRouteIcon()', :title='$helpers.getRouteTitle()')

    .container
        h3 Datos del cliente
        el-form.flex-form-with-rows(ref="invoice_client_form" :model="invoice")
            .form-row
                el-form-item(label="Cliente" prop="id_client")
                    el-select(
                        v-model="invoice.id_client"
                        placeholder= Seleccione un cliente
                        style= "width: 100%"
                    )
                        el-option(v-for="client in clientsList"
                        :key="client.value"
                        :label="client.label"
                        :value="client.value"
                        )
        <hr>
    .container
        h3 Líneas de facturación
        el-form.flex-form-with-rows(ref="invoice_lines_form" :model="invoiceFetched")
            .form-row(
                v-for="(line, index) in invoiceFetched.invoice_lines"
                :key="line.id"
            )
                custom-input(
                    v-model="line.id_product"
                    :label="'Producto ID'"
                    placeholder="ID Producto"
                    :prop="'invoice_lines.' + index + '.id_product'"
                )
                .form-flex-item-1
                    custom-input(
                        v-model="line.unidades"
                        label="Unidades"
                        placeholder="Unidades"
                        :prop="'invoice_lines.' + index + '.unidades'"
                    )
                .form-flex-item-1
                    custom-input(
                        v-model="line.precio_unitario"
                        label="Precio Unitario"
                        placeholder="Precio Unitario"
                        :prop="'invoice_lines.' + index + '.precio_unitario'"
                    )
                .form-flex-item-1
                    custom-input(
                        v-model="line.descuento"
                        label="Descuento"
                        placeholder="Descuento"
                        :prop="'invoice_lines.' + index + '.descuento'"
                    )
                .form-flex-item-1
                    custom-input(
                        v-model="line.subtotal"
                        label="Subtotal"
                        placeholder="Subtotal"
                        :prop="'invoice_lines.' + index + '.subtotal'"
                    )
                el-tooltip(
                    effect='light',
                    content='Eliminar Producto')
                    svg-icon.control-icon.cus(
                        icon-class='icono_eliminar',
                        @click.prevent.native.stop='handleDelete(index)'
                    )
            add-new-thing(
                text='Añadir línea de facturación',
                @click='addInvoiceLine'
                )
        <hr>
    .container
        h3 Pie de factura
        el-form.flex-form-with-rows(ref="invoice_form" :model="invoice")
            .form-row
                custom-input(v-model="invoice.codigo" label="Código de factura" placeholder="Ingrese código de facturación" prop="invoice.codigo")
                .form-flex-item-1
                    custom-date-picker(v-model="invoice.fecha" label="Fecha" placeholder="Escoge una fechas" prop="invoice.fecha")
                .form-flex-item-1
                    custom-input(v-model="invoice.total" label="Total facturado" placeholder="Ingrese total facturado" prop="invoice.total")
        <hr>
        custom-button.example-button(zoom_animation @click.prevent.native.stop='onSubmit()') {{this.action}} factura
        h3 DEBUG: invoice
        pre {{ invoice }}

//-   .top-pagination(v-if='pagination.total_pages > 1')
//-     .pagination-box
//-       el-pagination(
//-         layout='prev, pager, next',
//-         :page-size='pagination.limit',
//-         :current-page='pagination.curr_page',
//-         :page-count='pagination.total_pages',
//-         @current-change='changePagination'
//-       )

//-   .bottom-pagination(v-if='pagination.total_pages > 1')
//-     .pagination-box
//-       el-pagination(
//-         layout='prev, pager, next',
//-         :page-size='pagination.limit',
//-         :current-page='pagination.curr_page',
//-         :page-count='pagination.total_pages',
//-         @current-change='changePagination'
//-       )
</template>

<script>
import InvoiceApi from '@/api/InvoiceApi'
import ClientApi from '@/api/ClientApi'
import RouteFilters from '@/mixins/RouteFilters'

export default {
  name: 'invoice',
  mixins: [RouteFilters],
  created(){
    // extraigo el parámero de la url
    this.id_invoice = (this.$route.params.id_invoice) ? this.$route.params.id_invoice : null

    // sea crear o editar, necesito los clientes
    this.fetchClientsDropdown()

    // lo evalúo
    // si no es -1, se updatea la factura
    if(this.id_invoice != -1){

        /* 
        1 - Seteo flag
        2 - Leo y asigno
        3 - Bindeo las partes que me interesan del template a la nueva estructura de datos invoice
        */
        this.action='update'
        this.fetchData() // lectura y asignación de datos de la factura asociada al id y sus relaciones en invoiceFetched, con el que muestro datos en el front y asignación de datos a invoice, registro tipo q se manda al back para updatear






    }else{ // si es -1 se crea una factura
        this.invoice =
        {
        "id": null,
        "id_client": null,
        "codigo": '',
        "fecha": '',
        "total": null,
        "is_hidden": 0,
        }
    }

    // alert(JSON.parse(this.invoice))
  },
  data() {
    return {
        // propiedades de la instancia vue
        // flags
        loading: true,
        saving: false,
        action: null,

        id_invoice: null, // valor desde $route.params
        invoiceFetched:{}, // datos de la invoice asociada al id junto con sus relaciones
        invoice:{}, // datos de registro invoice creado o updated

        selectedClient:{}, // datos del cliente seleccionado <select>
        clientsList:[], // lista de clientes para dropdown

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
      this.saving = false
      this.invoice = {}
      this.pagination = {
        curr_page: 1,
        total_pages: 1,
        limit: 10
      }

      // request front baseAPIcalls
      InvoiceApi.getById(this.id_invoice, page)
        .then(response => {
          
        //  asignación de datos leidos a modelo 
          this.invoiceFetched = response.data
          this.pagination = response.data.counts

        // registro invoice tipo, el que se envía para update, asigno los valores leídos y los bindeo en el template (parte cliente + factura)
        this.invoice = {
            "id": this.invoiceFetched.id,
            "id_client": this.invoiceFetched.id_client,
            "codigo": this.invoiceFetched.codigo,
            "fecha": this.invoiceFetched.fecha,
            "total": this.invoiceFetched.total,
            "is_hidden": this.invoiceFetched.is_hidden,
        }
          // codifica la ruta en la barra de navegacion
          this.encodeFilters()
          this.loading = false
        })
        .catch(() => {
          // evita el freeze
          this.loading = false
        })
    },
    fetchClientsDropdown(){

            // llamo a la api de cliente para su dropdown, reutilizo metodo clase padre
            ClientApi.getDropdown()
            .then(response => {
                this.clientsList = response.data
            })
            .catch(() => console.log('Catch en fetchClientsDropdown()'))
    },
    // update de datos a partir de event en tooltip, recibe por parámtro el client(scope.row)
    handleDelete(index) {
      this.invoice.invoice_lines.splice(index, 1);
    },
    changePagination(page) {
      this.fetchData(page)
    },
    onSubmit(){

        if(this.action === 'update'){
            // llamo a la api con los datos de invoice para actualizar la factura
            console.log('onSubmit')
            InvoiceApi.update(this.invoice)

            // llamo a la api con los invoice_lines para actualizarlos/sincronizarlos
        }else{
            // estoy creando
        }


    },
        addInvoiceLine() {
      if (!this.invoice.invoice_lines) {
        this.$set(this.invoice, 'invoice_lines', []);
      }
      this.invoice.invoice_lines.push({
        id: null,
        id_invoice: this.invoice.id || null,
        id_product: null,
        unidades: null,
        precio_unitario: null,
        descuento: null,
        subtotal: null
      });
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

