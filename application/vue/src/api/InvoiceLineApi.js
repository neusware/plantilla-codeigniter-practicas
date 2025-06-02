import request from '@/utils/request' // solicitudes http
import BaseApiCalls, { initBaseApiCalls } from '@/api/BaseApiCalls'

// instancia objeto con el constructor
initBaseApiCalls()

class InvoiceLineApi extends BaseApiCalls {
  constructor() {
    super('invoice_line') // valor asignado a autoload_class_name -> url parametrizada | parametro de constructor clase padre BaseApiCalls
  }

  handlerInvoiceLines(invoice_lines_data, id_invoice) {
    return request({
      url: `/api/invoice_line/handler_invoice_lines`,
      method: 'post',
      data: {
        invoice_lines_data, id_invoice
      }
    })
  }
}

export default new InvoiceLineApi()
