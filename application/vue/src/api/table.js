import request from '@/utils/request'

export function getList(params) {
  return request({
    url: '/api/example/list',
    method: 'get',
    params
  })
}
