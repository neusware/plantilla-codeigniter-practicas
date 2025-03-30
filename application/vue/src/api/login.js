import request from '@/utils/request'

export function login(identity, password) {
  return request({
    url: 'api/external/login',
    method: 'post',
    data: {
      identity,
      password
    }
  })
}

export function getInfo() {
  return request({
    url: 'api/auth/info',
    method: 'get'
  })
}

export function logout() {
  return request({
    url: 'api/auth/logout',
    method: 'post'
  })
}

export function changePassword(data) {
  return request({
    url: 'api/external/changePassword',
    method: 'post',
    data
  })
}

export function forgotPassword(formData) {
  return request.post('api/external/forgotPassword', formData, { headers: { 'Content-Type': 'multipart/form-data' }})
}
