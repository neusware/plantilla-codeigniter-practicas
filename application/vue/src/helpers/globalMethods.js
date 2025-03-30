import store from '@/store'
import router from '@/router'
import { Message, Notification } from 'element-ui'

// Métodos globales, importados en main.js y accesibles mediante objeto global $helpers
export default {
  install: (Vue) => {
    Vue.prototype.$helpers = {
      changeBackgroundColorCssVariable(variable_name = '--app-original-background') {
        const variable_color_value = (getComputedStyle(document.documentElement).getPropertyValue(variable_name)) ? getComputedStyle(document.documentElement).getPropertyValue(variable_name) : variable_name

        document.body.style.setProperty('--app-background', variable_color_value)
      },
      isAdmin() {
        return (store.getters.roles.indexOf('admin') !== -1)
      },
      fileUrl($controller, $field, $file) {
        return process.env.BASE_API + '/api/' + $controller + '/file/' + store.getters.token + '/' + $field + '/' + $file
      },
      isSideBarOppened() {
        return store.getters.sidebar.opened
      },
      isSideBarHidden() {
        return (store.getters.device === 'mobile')
      },
      getUser() {
        return store.getters.user
      },
      strip_html_tags(str) {
        return (!str) ? '' : str.toString().replace(/<[^>]*>/g, '')
      },
      getRouteTitle() {
        return (parseInt(store.getters.user.id_ambito_preparacion_plataforma) === 2 && router.currentRoute.meta.title_gestion) ? router.currentRoute.meta.title_gestion : router.currentRoute.meta.title
      },
      getRouteIcon(get_parent_icon) {
        return (get_parent_icon && router.currentRoute.matched.length > 1 && router.currentRoute.matched[0].meta.icon) ? router.currentRoute.matched[0].meta.icon : router.currentRoute.meta.icon
      },
      generateRandomString(longitud) {
        return Math.random().toString(36).substring(2, longitud + 2)
      },
      showCustomNotification(text, message_type = 'info', message_duration = 3000, html_string = false) {
        Message({
          dangerouslyUseHTMLString: html_string,
          message: text,
          type: message_type,
          showClose: true,
          duration: message_duration
        })
      },
      showNewCustomNotification(text, message_type = 'info', message_duration = 3000, html_string = false) {
        let notification_title = 'Info'
        let notification_class = 'info-notification'

        switch (message_type) {
          case 'success':
            notification_title = 'Correcto'
            notification_class = 'success-notification'
            break
          case 'warning':
            notification_title = 'Aviso'
            notification_class = 'warning-notification'
            break
          case 'error':
            notification_title = 'Error'
            notification_class = 'error-notification'
            break
          default:
            notification_title = 'Info'
            notification_class = 'info-notification'
        }

        Notification({
          dangerouslyUseHTMLString: html_string,
          title: notification_title,
          message: text,
          type: message_type,
          customClass: notification_class,
          duration: message_duration
        })
      },
      vueEditorCustomOptions() {
        return [
          [{ header: [false, 1, 2, 3, 4, 5, 6] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ align: '' }, { align: 'center' }, { align: 'right' }, { align: 'justify' }],
          [{ list: 'ordered' }, { list: 'bullet' }, { list: 'check' }],
          [{ indent: '-1' }, { indent: '+1' }],
          // [{ color: ['#444'] }, { background: ['#98ca3e', '#7d9fd3', 'transparent'] }],
          ['clean']
        ]
      },
      mergeRecursive(obj1, obj2) {
        for (var p in obj2) {
          try {
            // Property in destination object set; update its value.
            if (obj2[p].constructor === Object) {
              obj1[p] = this.mergeRecursive(obj1[p], obj2[p])
            } else {
              obj1[p] = obj2[p]
            }
          } catch (e) {
            // Property in destination object not set; create it and set its value.
            obj1[p] = obj2[p]
          }
        }
        return obj1
      },
      capitalizeEachWord(text) {
        const texto = text.replace(/\s+/g, ' ').trim()

        return texto.toLowerCase().split(' ').map(x => x[0].toUpperCase() + x.slice(1)).join(' ')
      }
    }
  }
}
