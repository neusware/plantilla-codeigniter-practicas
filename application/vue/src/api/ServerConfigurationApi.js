import request from '@/utils/request'

export function getMaintenanceStatus() {
  return request({
    url: 'api/serverConfiguration/getMaintenanceStatus',
    method: 'get'
  })
}
