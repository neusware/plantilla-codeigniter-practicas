import request from '@/utils/request' // solicitudes http
import BaseApiCalls, { initBaseApiCalls } from '@/api/BaseApiCalls'

// instancia objeto con el constructor
initBaseApiCalls()

class InvoiceApi extends BaseApiCalls {
  constructor() {
    super('invoice') // valor asignado a autoload_class_name -> url parametrizada | parametro de constructor clase padre BaseApiCalls
  }

  getFilteredInvoices(filter, page) {
    return request({
      url: `/api/invoice/getFilteredInvoices`,
      method: 'post',
      data: {
        page, filter
      }
    })
  }
}

// export { UserApi as default }
export default new InvoiceApi()
