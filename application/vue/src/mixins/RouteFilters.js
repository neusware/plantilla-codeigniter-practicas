export default {
  mounted() {
    this.decodeFilters()
  },
  data() {
    return {
      filters: {},
      pagination: {}
    }
  },
  methods: {
    // Ejemplos:

    // Por ruta actual sin parametros:      this.encodeFilters()
    // Por ruta actual con parametros:      this.encodeFilters('actual', { id: data.id.toString(), tipo: this.filters.tipo.toString(), user_id: data.user_id.toString() })
    // Por name de ruta con parametros:     this.encodeFilters('EntrevistaRoute', { id: data.id.toString(), tipo: this.filters.tipo.toString(), user_id: data.user_id.toString() })
    // Por name de ruta sin parametros:     this.encodeFilters('EntrevistaRoute')
    // Por ruta en bruto:                   this.encodeFilters('/entrevista-recovery/' + data.id + '/' + this.filters.tipo + '/' + data.user_id, null, 'raw')

    encodeFilters(ruta_name = 'actual', params = null, modo = 'normal', blank = false) {
      // utf8_to_b64
      const search_filters_b64 = btoa(JSON.stringify({ filters: this.filters, pagination: this.pagination }))

      // Si se quiere abrir en una tab nueva pasar blank = true
      if (blank) {
        var routeData = null
        if (modo === 'normal') {
          if (ruta_name === 'actual') {
            if (params) {
              routeData = this.$router.resolve({ name: this.$route.name, params, query: { search: search_filters_b64 }})
            } else {
              routeData = this.$router.resolve({ name: this.$route.name, query: { search: search_filters_b64 }})
            }
          } else {
            if (params) {
              routeData = this.$router.resolve({ name: ruta_name, params, query: { search: search_filters_b64 }})
            } else {
              routeData = this.$router.resolve({ name: ruta_name, query: { search: search_filters_b64 }})
            }
          }
        } else {
          routeData = this.$router.resolve(ruta_name + '?search=' + search_filters_b64)
        }
        window.open(routeData.href, '_blank')
      } else {
        if (modo === 'normal') {
          if (ruta_name === 'actual') {
            if (params) {
              this.$router.push({ name: this.$route.name, params, query: { search: search_filters_b64 }})
            } else {
              this.$router.push({ name: this.$route.name, query: { search: search_filters_b64 }})
            }
          } else {
            if (params) {
              this.$router.push({ name: ruta_name, params, query: { search: search_filters_b64 }})
            } else {
              this.$router.push({ name: ruta_name, query: { search: search_filters_b64 }})
            }
          }
        } else {
          this.$router.push(ruta_name + '?search=' + search_filters_b64)
        }
      }
    },
    decodeFilters() {
      // b64_to_utf8
      if ('search' in this.$route.query) {
        try {
          const search_filters_utf8 = JSON.parse(atob(this.$route.query.search))
          this.filters = search_filters_utf8.filters
          this.pagination = search_filters_utf8.pagination
        } catch (error) {
          this.$router.push({ path: '/404' })
        }
      }
    }
  }
}
