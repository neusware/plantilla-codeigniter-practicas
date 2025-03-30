import request from '@/utils/request'
import BaseApiCalls, { initBaseApiCalls } from '@/api/BaseApiCalls'

initBaseApiCalls()

class UserApi extends BaseApiCalls {
  constructor() {
    super('user')
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
