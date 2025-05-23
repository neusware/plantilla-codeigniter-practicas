import request from '@/utils/request' // solicitudes http
import BaseApiCalls, { initBaseApiCalls } from '@/api/BaseApiCalls'

// instancia objeto con el constructor
initBaseApiCalls()

class ClientApi extends BaseApiCalls {
  constructor() {
    super('client') // valor asignado a autoload_class_name -> url parametrizada | parametro de constructor clase padre BaseApiCalls
  }

  getFilteredClients(filter, page) {
    return request({
      url: `/api/client/getFilteredClients`,
      method: 'post',
      data: {
        page, filter
      }
    })
  }

//   getProvidersDropdown() {
//     return request({
//       url: `/api/provider/dropdown`,
//       method: 'get'
//     })
//   }
//   }
}

// export { UserApi as default }
export default new ClientApi()
