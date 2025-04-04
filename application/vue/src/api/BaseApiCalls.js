import request from '@/utils/request'

// delara variable
var BaseApiCalls

// declara funcion
export function initBaseApiCalls() {
  // condiciona
  if (BaseApiCalls) return

  // devolver una clase que encapsula todos los métodos básicos de crud parametrizados
  BaseApiCalls = class BaseApiCalls {
    constructor(autoload_class_name) {
      // parametro dinámico en función de la entidad a tratar
      this.autoload_class_name = autoload_class_name
    }

    getAll() {
      return request({
        url: `/api/${this.autoload_class_name}/all`,
        method: 'get'
      })
    }

    getPaginated(page) {
      return request({
        url: `/api/${this.autoload_class_name}/page/${page}`,
        method: 'get'
      })
    }

    getDropdown() {
      return request({
        url: `/api/${this.autoload_class_name}/dropdown`,
        method: 'get'
      })
    }

    getFilteredDropdown(data) {
      return request({
        url: `/api/${this.autoload_class_name}/filteredDropdown`,
        method: 'post',
        data: {
          data: data
        }
      })
    }

    getDropdownById(id) {
      return request({
        url: `/api/${this.autoload_class_name}/dropdownById/${id}`,
        method: 'get'
      })
    }

    create(data) {
      data = JSON.stringify(data)
      return request({
        url: `/api/${this.autoload_class_name}/create`,
        method: 'post',
        data: {
          data
        }
      })
    }

    // Para cuando haya que crear adjuntando archivos
    createWithFormData(formData) {
      return request.post(`api/${this.autoload_class_name}/create`, formData, { headers: { 'Content-Type': 'multipart/form-data' }})
    }

    getById(id) {
      return request({
        url: `/api/${this.autoload_class_name}/data/${id}`,
        method: 'get'
      })
    }

    update(data) {
      data = JSON.stringify(data)
      return request({
        url: `/api/${this.autoload_class_name}/update`,
        method: 'post',
        data: {
          data
        }
      })
    }

    // Para cuando haya que actualizar adjuntando archivos
    updateWithFormData(formData) {
      return request.post(`api/${this.autoload_class_name}/update`, formData, { headers: { 'Content-Type': 'multipart/form-data' }})
    }

    delete(id) {
      return request({
        url: `/api/${this.autoload_class_name}/delete`,
        method: 'post',
        data: {
          id
        }
      })
    }
  }
}

initBaseApiCalls()

export default BaseApiCalls
// Nota: con esta importación funciona, si deja de hacerlo, probar con la siguiente:
// export { BaseApiCalls as default } // IMPORTANT: not `export default BaseApiCalls!!
