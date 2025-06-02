import Vue from 'vue'

import 'normalize.css/normalize.css'//  A modern alternative to CSS resets

import Transitions from 'vue2-transitions' //  Transitions library
Vue.use(Transitions)

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import locale from '@/utils/es_element' //  lang i18n
Vue.use(ElementUI, { locale })

import '@/styles/index.sass' //  global css

// método $modal - global (main.js)
import VModal from 'vue-js-modal' //  import modals library
Vue.use(VModal, { dialog: true }) // uso, con parametro para modales de dialogo (si o no)

import App from './App'
import router from './router'

// importo métodos, se usarán con objeto $helpers
import globalMethods from './helpers/globalMethods'
// los hago globales
Vue.use(globalMethods)

import store from './store'

import '@/icons' //  icon
import '@/permission' //  permission control

//  MomentJs (dates formatting)
import moment from 'moment'
import 'moment/locale/es'
moment.locale('es')
Vue.prototype.$moment = moment

//  import globally used components
import './_globalComponents'

//  Fix for vue-router try catch error after update vue-router (https:// github.com/vuejs/vue-router/issues/2881#issuecomment-520554378)
import Router from 'vue-router'
const originalPush = Router.prototype.push
Router.prototype.push = function push(location, onResolve, onReject) {
  if (onResolve || onReject) return originalPush.call(this, location, onResolve, onReject)
  return originalPush.call(this, location).catch(err => err)
}

//  global bus -> emitir y escuchar eventos globalmente, comunicación entre componentes no relacionados directamente, sin importar su relación jerarquica
const EventBus = new Vue() // instancia vue
Vue.prototype.$bus = EventBus // asignación a vue prototype bus

// lo meto en globalMethods tb
// Vue.filter('formatNumber', function(value, currency = '') {
//   if (!value) return '0'
//   const formattedNumber = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
//   return currency ? `${formattedNumber} ${currency}` : formattedNumber
// })

Vue.config.productionTip = false

new Vue({
  el: '#app', // elemento html del punto de entrada (archivo index.html) al que afecta
  router, // router importado
  store, // store importado
  template: '<App/>', // asignacion componente App.vue
  components: { App }
})

/* Global bus

-Emitir evento:

  this.$bus.$emit('recargarListaUsuarios)

-Escuchar evento:

  this.$bus.$on('recargarListaUsuarios', () => {
    this.fetchData(this.pagination.curr_page)
    })

-Flujo -> se emite un evento desde un componente al bus global, si algun componente esta escuchando al evento desencadenará x lógica, en este ejemplo ejecuta un método propio como fetchData que en este caso de uso, suele llamar a la API relacionada con la entidad que esté manejando (ApiUsuario) que hace el para ejecutar un método de la misma

-Eliminar listener (evitar fugas):

  this.$bus.$off('recargarListaUsuarios)

*/
