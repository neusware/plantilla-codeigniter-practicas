import request from '@/utils/request' // solicitudes http
import BaseApiCalls, { initBaseApiCalls } from '@/api/BaseApiCalls'

// instancia objeto con el constructor
initBaseApiCalls()

class UserApi extends BaseApiCalls {
  constructor() {
    super('user') // valor asignado a autoload_class_name -> url parametrizada | parametro de constructor clase padre BaseApiCalls
  }

  getPaginateUsers(page) {
    return request({
      url: `/api/user/getAllUsers/page/${page}`,
      method: 'get'
    })
  }

  getFilteredUsers(filter, page) {
    return request({
      url: `/api/user/getFilteredUsers`,
      method: 'post',
      data: {
        page, filter
      }
    })
  }

  updateUserImage(formData) {
    return request.post(`api/user/updateUserImage`, formData, { headers: { 'Content-Type': 'multipart/form-data' }})
  }

  deleteUserImage(id_user) {
    return request({
      url: `api/user/deleteUserImage/${id_user}`,
      method: 'get'
    })
  }
}

// export { UserApi as default }
export default new UserApi()
