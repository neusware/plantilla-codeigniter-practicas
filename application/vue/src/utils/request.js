import axios from 'axios'
// import { Notification, Message, MessageBox } from 'element-ui'
import { Notification, MessageBox } from 'element-ui'
import store from '../store'
import { getToken } from '@/utils/auth'

// 创建axios实例
const TIMEOUT_IN_MS = 30000
const service = axios.create({
  baseURL: process.env.BASE_API, // api base_url
  timeout: TIMEOUT_IN_MS // time in ms
})

// request拦截器
service.interceptors.request.use(config => {
  if (store.getters.token) {
    config.headers['X-Token'] = getToken() // 让每个请求携带自定义token 请根据实际情况自行修改
  }
  return config
}, error => {
  // Do something with request error
  console.log(error) // for debug
  Promise.reject(error)
})

// respone拦截器
service.interceptors.response.use(
  response => {
  /**
  * code为非20000是抛错 可结合自己业务进行修改
  */
    const res = response.data
    if (res.code === 20000) {
      return response.data
    } else if (res.code === 20001) {
      Notification({
        dangerouslyUseHTMLString: true,
        title: 'Correcto',
        message: (res.data.message) ? res.data.message : res.data,
        type: 'success',
        customClass: 'success-notification',
        duration: 2500
      })

      return response.data
    } else if (res.code === 30001) {
      Notification({
        dangerouslyUseHTMLString: true,
        title: 'Aviso',
        message: (res.data.message) ? res.data.message : res.data,
        type: 'warning',
        customClass: 'warning-notification',
        duration: 2500
      })

      return Promise.reject('error')
    } else if (res.code === 40001) {
      let error_message = 'Error'

      if (res.data.message) {
        error_message = res.data.message
      } else if (res.data.error) {
        error_message = res.data.error
      } else {
        error_message = res.data
      }

      Notification({
        dangerouslyUseHTMLString: true,
        title: 'Error',
        message: error_message,
        type: 'error',
        customClass: 'error-notification',
        duration: 2500
      })

      return Promise.reject('error')
    } else if (res.code === 90001) {
      if (store.getters.user.id) {
        store.dispatch('FedLogOut').then(() => {
          location.reload()// 为了重新实例化vue-router对象 避免bug
        })
      }

      return Promise.reject('mantenimiento')
    } else {
      // 50008:非法的token; 50012:其他客户端登录了;  50014:Token 过期了;
      if (res.code === 50008 || res.code === 50012 || res.code === 50014) {
        MessageBox.confirm('La sesión ha caducado, por favor vuelva a loguear', 'Aceptar para cerrar la sesión', {
          confirmButtonText: 'Aceptar',
          // cancelButtonText: 'Cancelar',
          type: 'warning'
        }).then(() => {
          store.dispatch('FedLogOut').then(() => {
            location.reload()// 为了重新实例化vue-router对象 避免bug
          })
        })
      }

      return Promise.reject('error')
    }
  },
  error => {
    console.log('err' + error)// for debug
    Notification({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    })

    return Promise.reject(error)
  }
)

export default service
