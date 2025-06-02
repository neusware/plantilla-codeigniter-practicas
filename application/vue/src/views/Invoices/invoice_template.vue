<template lang="pug">
.invoices-list-component
  title-box(
    :routeBack="['InvoicesList', null]"
    :icon='$helpers.getRouteIcon()'
    :title='(action === "Crear" ? $helpers.getRouteTitle() : action + " factura")'
  )

  .container
    h3 Datos del cliente
    el-form.flex-form-with-rows(ref="invoice_client_form" :model="invoice")
      .form-row
        .form-flex-item-1
          custom-select(v-model="invoice.id_client" label="Cliente" :options="clientsList" prop="id_client" :disabled="action === 'Ver'")
        .form-flex-item-1
          custom-input(v-model="selectedClient.email" label="Email" placeholder="Email del cliente" prop="email" :readonly='true')
        .form-flex-item-1
          custom-input(v-model="selectedClient.direccion" label="Dirección" placeholder="Dirección del cliente" prop="direccion" :readonly='true')
    <hr>
  .container
    h3 Líneas de facturación
    el-form.flex-form-with-rows(ref="invoice_lines_form" :model="invoiceForm")
      .form-row(
        v-for="(line, index) in invoiceForm.invoice_lines_w"
        :key="line.id"
      )
        .form-flex-item-1
          custom-select(
            v-model="line.id_product"
            :label="'Producto'"
            :options="productsList"
            :prop="'invoice_lines_w[' + index + '].id_product'"
            :disabled="action === 'Ver'"
            @input="productChange(index)"
          )
        .form-flex-item-1
          custom-input(
            v-model="line.unidades"
            label="Unidades"
            placeholder="Unidades"
            :prop="'invoice_lines_w[' + index + '].unidades'"
            :disabled="action === 'Ver'"
          )
        .form-flex-item-1
          custom-input(
            v-model="line.precio_unitario"
            label="Precio Unitario"
            placeholder="Precio Unitario"
            :prop="'invoice_lines_w[' + index + '].precio_unitario'"
            :readonly="true"
          )
        .form-flex-item-1
          custom-input(
            v-model="line.descuento"
            label="Descuento"
            placeholder="Descuento"
            :prop="'invoice_lines_w[' + index + '].descuento'"
            :disabled="action === 'Ver'"
          )
        .form-flex-item-1
          custom-input(
            v-model="line.subtotal"
            label="Subtotal"
            placeholder="Subtotal"
            :prop="'invoice_lines_w[' + index + '].subtotal'"
            :readonly='true'
          )
        el-tooltip(v-if="action !== 'Ver'" effect='light' content='Eliminar línea de facturación')
          svg-icon.control-icon.cus(
            icon-class='icono_eliminar',
            @click.prevent.native.stop="deleteInvoiceLine(index)"
          )
      add-new-thing(
        v-if="action !== 'Ver'"
        text='Añadir línea de facturación',
        @click.prevent.native.stop='addInvoiceLine()'
      )
    <hr>
  .container
    h3 Pie de factura
    el-form.flex-form-with-rows(ref="invoice_form" :model="invoice")
      .form-row
        custom-input(v-model="invoice.codigo" label="Código de factura" placeholder="Ingrese el código de la factura" prop="codigo" :disabled="action === 'Ver'")
        .form-flex-item-1
          custom-date-picker(v-model="invoice.fecha" label="Fecha" placeholder="Fecha de facturación" prop="fecha" :disabled="action === 'Ver'")
        .form-flex-item-1
          custom-input(v-model="invoice.total" label="Total facturado" placeholder="Ingrese total facturado" prop="total" :readonly='true')
    <hr>
    custom-button.example-button(zoom_animation @click.prevent.native.stop="onSubmit()" v-if="action !== 'Ver'") {{this.action}} factura
    h3 DEBUG: invoice
    pre {{ invoice }}
    h3 DEBUG: invoiceForm
    pre {{ invoiceForm }}
</template>

<script>
import InvoiceApi from '@/api/InvoiceApi'
import ClientApi from '@/api/ClientApi'
import ProductApi from '@/api/ProductApi'
import InvoiceLineApi from '@/api/InvoiceLineApi'
import RouteFilters from '@/mixins/RouteFilters'

export default {
  name: 'invoice',
  mixins: [RouteFilters],
  // ------------------------------------------------------------------------------------hooks
  created() {
    // extraigo parámetros del route params
    this.id_invoice = (this.$route.params.id_invoice) ? this.$route.params.id_invoice : null
    this.action = (this.$route.params.action) ? this.$route.params.action : null

    // console.log(this.action)

    // fetchs, sea create o update, necesito los clientes y productos, preparados para dropdown
    this.fetchClientsDropdown()
    this.fetchProductsDropdown()

    // evalúo route params para determinar el flujo - la acción
    if ((this.action === 'Editar' && this.id_invoice != null) || this.action === 'Ver') {

        /*
        1 - Leo y asigno
        2 - Bindeo las partes que me interesan del template a la nueva estructura de datos invoice - invoice_lines
        */

      //lectura y asignación de datos al modelo
      this.fetchData()

    } else if (this.action === 'Crear') {

      // inicializo propiedades del modelo
      // invoice tipo
      this.invoice = {
        'id': null,
        'id_client': null,
        'codigo': '',
        'fecha': '',
        'total': null,
        'is_hidden': 0
      }

      this.invoiceForm.invoice_lines_w = [
        // inicializo array con invoice_line tipo
        {
          'id': null,
          'id_invoice': null,
          'id_product': null,
          'unidades': null,
          'precio_unitario': null,
          'descuento': null,
          'subtotal': null
        }
      ]
    }
    // fb
    console.log('Created() en componente invoice_template ejecutado')
    // alert(JSON.parse(this.invoice))
  },

  // -------------------------------------------------------------------------------data
  data() {
    return {
      // flags
      loading: true,
      saving: false,
      action: null,

      // parámetro desde $route.params
      id_invoice: null,

      // -------------------------------------------------------------------------------------main data
      // almaceno datos recuperados por id, factura y sus relaciones
      invoiceFetched: {},

      // estructura d datos tipo invoice, valores por defecto
      invoice: {
        id_client: null,
        codigo: '',
        fecha: '',
        total: null,
        is_hidden: 0
      },

      // wrappeo en {} por elemento el-form
      invoiceForm: {
        // inicializo el array que almacenará las estructuras tipo para registros invoice_lines (casos create, update y delete)
        invoice_lines_w: []
      },

      // -------------------------------------------------------------------------------------dropdowns
      // lectura de clientes existentes, formato dropdown para select
      clientsList: [],
      // datos del cliente seleccionado <select> para mostrarlos en los inputs, inicializo por template
      selectedClient: {
        email: '',
        direccion: ''
      },

      // lectura de productos existentes, formato dropdown para select
      productsList: []

    }
  },
  // -------------------------------------------------------------------------------------methods
  methods: {

    // lectura de datos a través del getFilteredClients (filtro + pagincacion)
    fetchData() {
      this.loading = true
      this.saving = false
      this.invoice = {}

      // request front baseAPIcalls
      InvoiceApi.getById(this.id_invoice)
        .then(response => {

          //  asignación de datos leidos a modelo
          this.invoiceFetched = response.data

          // registro invoice tipo, el que se envía para update, asigno los valores leídos y los bindeo en el template (parte cliente + factura)
          this.invoice = {
            'id': this.invoiceFetched.id,
            'id_client': this.invoiceFetched.id_client,
            'codigo': this.invoiceFetched.codigo,
            'fecha': this.invoiceFetched.fecha,
            'total': this.invoiceFetched.total,
            'is_hidden': this.invoiceFetched.is_hidden
          }

          // registro invoice_lines_tipo con copia de los valores
          this.invoiceForm.invoice_lines_w = this.invoiceFetched['invoice_lines']

          // codifica la ruta en la barra de navegacion
          this.encodeFilters()
          this.loading = false
        })
        .catch(() => {
          // evita el freeze
          this.loading = false
        })
    },

    fetchClientsDropdown() {
      // llamo a la api de cliente para su dropdown, reutilizo metodo clase padre
      ClientApi.getDropdown()
        .then(response => {
          this.clientsList = response.data
        })
        .catch(() => console.log('Catch en fetchClientsDropdown()'))
    },

    fetchProductsDropdown() {
      // llamo a la api de cliente para su dropdown, reutilizo metodo clase padre
      ProductApi.getDropdown()
        .then(response => {
          this.productsList = response.data
        })
        .catch(() => console.log('Catch en fetchProductsDropdown()'))
    },

    onSubmit() {
      if (this.action === 'Actualizar') {
        this.saving = true

        console.log('Enviando datos de invoice:', this.invoice)
        InvoiceApi.update(this.invoice)
          .then(() => {
            console.log('Invoice actualizada')
            console.log('Enviando datos invoice lines:', this.invoiceForm.invoice_lines_w)
            return InvoiceLineApi.handlerInvoiceLines(this.invoiceForm.invoice_lines_w, this.invoice.id)
          })
          .then(() => {
            console.log('Invoice lines actualizadas')
            console.log('Update completado')
            // cambio de ruta, doy tiempo para visualizar el toast
            setTimeout(() => {
              this.$router.push({ name: 'InvoicesList' })
            }, 1000)
          })
          .catch(error => {
            console.error('Error al actualizar invoice:', error)
          })
          .finally(() => {
            this.saving = false
          })
      } else if (this.action === 'Crear') {
        this.saving = true
        console.log('Enviando datos invoice:', this.invoice)

        // BaseApiCalls
        InvoiceApi.create(this.invoice)
          .then(response => {
            console.log('Invoice creada')
            // recojo el id de la factura
            const invoiceId = response.data.id
            console.log('Enviando datos de invoice_lines:', this.invoiceForm.invoice_lines_w)
            // lo uso como parámetro
            return InvoiceLineApi.handlerInvoiceLines(this.invoiceForm.invoice_lines_w, invoiceId)
          })
          .then(() => {
            console.log('Invoice lines creadas')
            console.log('Create completado')
            // espero por toast y redirigo
            setTimeout(() => {
              this.$router.push({ name: 'InvoicesList' })
            }, 1000)
          })
          .catch(error => {
            console.error('Error al crear factura:', error)
          })
          .finally(() => {
            this.saving = false
          })
      }
    },

    addInvoiceLine() {
      // pusheo nuevo objeto invoice_line al array
      this.invoiceForm.invoice_lines_w.push({
        id: null,
        id_invoice: this.invoice.id || null,
        id_product: null,
        unidades: null,
        precio_unitario: null,
        descuento: null,
        subtotal: null
      })
      // this.calcularTotal()
    },

    // update de datos a partir de event en tooltip, recibe por parámtro el client(scope.row)
    deleteInvoiceLine(index) {
      // recibo por parámetro la posición del objeto en el array, lo elimino
      this.invoiceForm.invoice_lines_w.splice(index, 1)
      // recalculo
      this.calcularTotal()
    },

    calcularSubtotal(line) {
      // por parámetro la línea

      // extraigo sus valores
      const unidades = Number(line.unidades) || 0
      const precio = Number(line.precio_unitario) || 0
      const descuento = Number(line.descuento) || 0

      // fórmula, formateado en string
      return +(Number(unidades) * Number(precio) * (1 - (Number(descuento) / 100))).toFixed(2)
    },

    calcularTotal() {
      console.log('Calculando total')
      // recorro las lineas del modelo
      this.invoiceForm.invoice_lines_w.forEach(line => {
        // calculo el subtotal de cada linea
        line.subtotal = this.calcularSubtotal(line)
      })
      // calculo y asigno el total
      this.invoice.total = +this.invoiceForm.invoice_lines_w.reduce((sum, line) => sum + (Number(line.subtotal) || 0), 0).toFixed(2)
    },

    updatePrice(index) {
      // saco la linea
      const line = this.invoiceForm.invoice_lines_w[index]
      // evalúo por si falla
      if (!line || !line.id_product) {
        line.precio_unitario = null
        return
      }

      // En productList el identificador de las propiedades cambia, buscar por id (no id_product) y asignar precio (no precio_unitario)
      // busco el producto en lista id - id_product en líneas
      const selectedProduct = this.productsList.find(p => p.id === line.id_product)

      // evalúo
      if (selectedProduct && typeof selectedProduct.precio !== 'undefined') {
        // igualo el precio
        line.precio_unitario = selectedProduct.precio
      } else {
        line.precio_unitario = null
      }
    },

    productChange(index) {
      // ejecuto desde envent cambio en select, para cdo vienen de update no ejecutar

      // seteo el precio_unitario dado el producto seleccionado
      this.updatePrice(index)
      // seteo unidades y descuento a 0
      const line = this.invoiceForm.invoice_lines_w[index]
      line.unidades = 0
      line.descuento = 0
    }
  },

  // -----------------------------------------------------------------------------------observadores
  watch: {
    // observa la ruta en cada cambio
    $route(prev_route, new_route) {
      // si cambia la ruta lectura
      if (prev_route.name !== new_route.name) this.fetchData()
    },
    // observo invoice.id, evalúo el cambio
    'invoice.id_client'(newClientId, oldClientId) {
      if (newClientId) {
        // busco en la lista el cliente asociado
        const client = this.clientsList.find(client => client.value === newClientId)
        // si lo encuentro
        if (client) {
          this.selectedClient = { ...client } // copio el objeto
        } else {
          // o seteo valores x defecto
          this.selectedClient = { id: null, email: '', nombre: '', apellido: '', direccion: '' }
        }
      } else {
        // si no cambia, seteo valores x defecto
        this.selectedClient = { id: null, email: '', nombre: '', apellido: '', direccion: '' }
      }
    },
    'invoiceForm.invoice_lines_w': {
      handler(newLines, oldLines) {
        this.calcularTotal()
        // Actualiza el precio unitario si cambia el producto, comparo el id de producto
        newLines.forEach((line, idx) => {
          if (!oldLines || !oldLines[idx] || line.id_product !== oldLines[idx].id_product) {
            this.updatePrice(idx)
          }
        })
      },
      deep: true
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