<template lang="pug">
  .users-list-component
    title-box(:icon="$helpers.getRouteIcon()" :title="$helpers.getRouteTitle()")

      add-new-thing(:disabled="deleting" v-if="$helpers.isAdmin()" text="Añadir Usuario" @click="$modal.show('usuario')")

    .filters-box
      custom-input.filter-item(v-model="filters.buscador" placeholder="Buscar Usuario..." style="margin: 10px" @keyup.enter.native="fetchData(1)")
      custom-button.add-item(:disabled="deleting" @click.native="fetchData(1)" style="min-width: 150px;") Buscar

    .top-pagination(v-if="pagination.total_pages > 1")
      .pagination-box
        el-pagination(layout="prev, pager, next" :page-size="pagination.limit" :current-page="pagination.curr_page" :page-count="pagination.total_pages" @current-change="changePagination")

    //- este comentario pug, antes de los elementos hijos
    el-table.user-table.custom-table(
      :data='userList'
      fit=""
      stripe
      v-loading="loading" :element-loading-text="(!deleting ? 'Cargando...' : 'Borrando...')"
    )
      el-table-column(label="Nombre de usuario" prop="username" min-width="190px")
      el-table-column(label="Rol" min-width="120px" label-align="center")
        template.cell(slot-scope="scope")
          .column.title
            span(v-for="role in scope.row.grupos" style="margin-right: 10px") {{role.grupo.description}}
      el-table-column(label="E-mail" prop="email" min-width="190px")
      el-table-column(label="Nombre" prop="first_name" min-width="120px")
      el-table-column(label="Apellidos" prop="last_name" min-width="120px")
      el-table-column(label="Teléfono" prop="phone" min-width="100px" align="center")
      el-table-column(label="Acciones" min-width="110px" align="center")
        template(slot-scope="scope")
          el-tooltip(effect="light" content="Ver Perfil" placement="top")
            svg-icon.control-icon.cus(icon-class="icono_perfil_usuario" @click.prevent.native.stop="handleClickUser(scope.row)")
          el-tooltip(effect="light" content="Editar Usuario" placement="top")
            svg-icon.control-icon.cus(icon-class="icono_editar" @click.prevent.native.stop="handleEdit(scope.row)")
          el-tooltip(v-if="$helpers.getUser().id != scope.row.id" effect="light" content="Eliminar Usuario" placement="top-end")
            svg-icon.control-icon.cus(icon-class="icono_eliminar" @click.prevent.native.stop="handleDelete(scope.row)")

    .bottom-pagination(v-if="pagination.total_pages > 1")
      .pagination-box
        el-pagination(layout="prev, pager, next" :page-size="pagination.limit" :current-page="pagination.curr_page" :page-count="pagination.total_pages" @current-change="changePagination")
</template>

<script>
import UserApi from '@/api/UserApi'
import RouteFilters from '@/mixins/RouteFilters'

export default {
  name: 'user',
  mixins: [RouteFilters],
  mounted() { //  componente inicializado y listo para comunicarse con otros comp o servs
    //  listener en bus
    this.$bus.$on('recargarListaUsuarios', () => {
      //  ejecuto metodo del componente, que a su vez ejecuta metodo ApiUsers + param
      this.fetchData(this.pagination.curr_page)
    })

    // fb
    console.log('Mounted Users ejecutado')
    //  aunque no se dé el evento en el bus, ejecuto la misma lógica al montar el component
    this.fetchData(this.pagination.curr_page)
  },

  data() { //  propiedades, encapsuladas para el component
    return {
      loading: true,
      deleting: true,
      userList: [],
      pagination: {
        curr_page: 1,
        total_pages: 1,
        limit: 10
      },
      filters: {
        buscador: ''
      }
    }
  },

  methods: { //  metodos del component
    // por defecto siempre se le pasa la primera página
    fetchData(page = 1) {
      //  seteo de variables
      this.loading = true
      this.deleting = false
      this.userList = []
      this.pagination = {
        curr_page: 1,
        total_pages: 1,
        limit: 10
      }

      //  hace uso de metodo APi, para solicitud http con el query==filters y pagina
      UserApi.getFilteredUsers(this.filters, page)
        .then((response) => {
          //  asigna los valores de la respuesta
          this.userList = response.data.data
          this.pagination = response.data.counts
          this.encodeFilters()
          this.loading = false
        }).catch(() => { this.loading = false }) // evitar el 'cargando' siempre
    },
    // modifica la ruta  basada en id usuario pero codificada, params(nombre_ruta,param) -> ir a profile
    handleClickUser(user) {
      this.encodeFilters('UsersProfile', { id_usuario: user.id })
      //   La linea de arriba es equivalente a esta:      this.$router.push({ name: 'UsersProfile', params: { id_usuario: user.id }})
    },
    //  recibe por parametro objeto usuario
    handleEdit(usuario) {
      //   almaceno el parametro en una variable, como objeto, en la propiedad user
      const user_data = { user: usuario }
      //  se lo paso a un modal clonado, como una deep-copy (stringifeo y convierto en JSON, para evitar el paso x referencia, parece que el primer parametro indica que tipo de modal es
      this.$modal.show('usuario', JSON.parse(JSON.stringify(user_data)))
    },
    //  recibe por parametro objeto usuario
    handleDelete(user) {
      //  ejecuto modal, de tipo dialog, y como segundo parametro objeto con las propiedades esperadas y datos del parametro por interpolación, codificado aquí
      this.$modal.show('dialog', {
        title: 'Eliminar Usuario',
        text: `¿Seguro que quieres <b>eliminar</b> el usuario? <b>${user.first_name} ${user.last_name} (${user.username})</b>?`,
        buttons: [
          //  dos objetos, dos btns
          {
            title: 'No'
          },
          //  en btn borrar, comportamiento de request http haciendo uso de metodo de la API de usuario
          {
            title: 'Sí, borrar',
            default: true,
            handler: () => {
              // flags
              this.loading = true
              this.deleting = true
              // llamada api usuario a borrar x id
              UserApi.delete(user.id).then(response => {
                // en respuesta refresco
                this.fetchData(1)
              }).catch(() => {
                this.loading = false
                this.deleting = false
              })
              // lo oculto, fuera de la promesa
              this.$modal.hide('dialog')
            }
          }
        ]
      })
    },
    changePagination(page) {
      this.fetchData(page)
    },
    addNewUser() {
      //  abre un modal de tipo usuario?, puede que no se esté usando
      this.$modal.show('usuario')
    }
  },
  watch: {
    // watcher - se observa la ruta para que en cada cambio...
    '$route'(prev_route, new_route) {
      if (prev_route.name !== new_route.name) this.fetchData()
    }
  }
}

</script>

<style lang="sass" scoped>
  $breakpoint-phones: 480px
  $breakpoint-tablets: 768px

  .users-list-component
    .filters-box
      display: flex
      flex-wrap: wrap
      .filter-item
        min-width: 200px
        max-width: 200px
        //   max-width: 200px
      .add-item
        margin: 10px

  @media (max-width: $breakpoint-tablets)
    .users-list-component
      .filters-box
        .filter-item
          min-width: 46%
  @media (max-width: $breakpoint-phones)
    .users-list-component
      .filters-box
        .filter-item
          flex: 1
          min-width: 95%
          margin: 10px
</style>
