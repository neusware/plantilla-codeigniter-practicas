import request from '@/utils/request' // solicitudes http
import BaseApiCalls, { initBaseApiCalls } from '@/api/BaseApiCalls'

// instancia objeto con el constructor
initBaseApiCalls()

class ProductApi extends BaseApiCalls {
  constructor() {
    super('product') // valor asignado a autoload_class_name -> url parametrizada | parametro de constructor clase padre BaseApiCalls
  }

  getFilteredProducts(filter, page) {
    return request({
      url: `/api/product/getFilteredProducts`,
      method: 'post',
      data: {
        page, filter
      }
    })
  }

  getProvidersDropdown() {
    return request({
      url: `/api/provider/dropdown`,
      method: 'get'
    })
  }

  // checkCIF(cif) {
  //   return request({
  //     url: `/api/provider/checkCIF`,
  //     method: 'post',
  //     data: { cif }
  //   })
  // }

  // createProvider(data) {
  //   data = JSON.stringify(data)
  //   console.log('Método createProvider (ProviderApi) ejecutándose')
  //   return request({
  //     url: `/api/${this.autoload_class_name}/createProvider`,
  //     method: 'post',
  //     data: {
  //       data
  //     }
  //   })
  // }

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
export default new ProductApi()
