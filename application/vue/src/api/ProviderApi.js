import request from '@/utils/request' // solicitudes http
import BaseApiCalls, { initBaseApiCalls } from '@/api/BaseApiCalls'

// instancia objeto con el constructor
initBaseApiCalls()

class ProviderApi extends BaseApiCalls {
  constructor() {
    super('provider') // valor asignado a autoload_class_name -> url parametrizada | parametro de constructor clase padre BaseApiCalls
  }

  getPaginateProvider(page) {
    return request({
      url: `/api/user/getAllProviders/page/${page}`,
      method: 'get'
    })
  }

  getFilteredProviders(filter, page) {
    return request({
      url: `/api/provider/getFilteredProviders`,
      method: 'post',
      data: {
        page, filter
      }
    })
  }

//   updateUserImage(formData) {
//     return request.post(`api/user/updateUserImage`, formData, { headers: { 'Content-Type': 'multipart/form-data' }})
//   }

//   deleteUserImage(id_user) {
//     return request({
//       url: `api/user/deleteUserImage/${id_user}`,
//       method: 'get'
//     })
//   }
}

// export { UserApi as default }
export default new ProviderApi()
