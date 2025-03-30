<template lang="pug">
  .profile-tab.h-bigger-loading-font#user-profile-view(v-loading="loading" element-loading-text="Cargando...")
    .loading-box(v-if="!loading")
      title-box(v-if="!user_data" :routeBack="['UsersList', null]" title="Perfil no autorizado")
      title-box(v-else :routeBack="['UsersList', null]" :title="(is_perfil_personal) ? 'Mi Perfil' : `Perfil de ${user_data.first_name} ${user_data.last_name}`")

      template(v-if="!loading && !user_data")
        h1 El usuario no existe o no tienes acceso a este perfil

      template(v-else-if="!loading && (is_perfil_personal || $helpers.isAdmin())")
        .profile-data-box
          .profile-main-info-box
            .profile-img-box
              .profile-picture-box(v-if="!user_data.imagen")
                svg-icon.profile-img(icon-class="user_default")
              .profile-picture-box(v-else)
                .imagen-guardada(v-viewer="{ inline: false, button: true, navbar: false, title: false, toolbar: false, tooltip: false, movable: true, zoomable: true, rotatable: false, scalable: false, transition: false, fullscreen: false, keyboard: false }" class="images clearfix")
                  img(v-if="user_data.imagen" v-bind:src="$helpers.fileUrl('User', 'imagen', user_data.imagen)" width="100%" height="100%" style="cursor: pointer; border-radius: 50%; object-fit: cover;")
            .profile-action-buttons
              .profile-action-button(v-if="!imagen_loading")
                .upload-file-item(v-if="!imagen_loading")
                  el-upload(ref="user_image_ref" id="user-image" action="" :on-change="editProfileImg" :auto-upload="false" :limit="1" :show-file-list="false")
                    custom-button.add-file-button.profile-action-button(rounded @click.native.prevent="" thin zoom_animation)
                      i(class="el-icon-upload")
                      span.fake-space-left Subir imagen
                .upload-file-item(v-else)
                  custom-button.add-file-button.profile-action-button(rounded disabled @click.native.prevent="" thin zoom_animation)
                    i(class="el-icon-delete")
                    span.fake-space-left Subir imagen
                .delete-img-user(v-if="user_data.imagen")
                  custom-button.remove-file-button.profile-action-button(rounded :disabled="imagen_loading" @click.native.prevent="deleteUserImageDialog" thin zoom_animation)
                    i(class="el-icon-delete")
                    span.fake-space-left Quitar imagen
              custom-button.edit-user.profile-action-button(rounded @click.prevent.native="handleEditUser(user_data)" thin zoom_animation)
                i(class="el-icon-edit")
                span.fake-space-left Editar Usuario
              .profile-action-button(v-if="$helpers.getUser().id == user_data.id" style="height: 54px;")
              custom-button.delete-user.profile-action-button(v-if="$helpers.getUser().id != user_data.id" rounded @click.prevent.native="handleDeleteUser(user_data)" thin zoom_animation)
                i(class="el-icon-delete")
                span.fake-space-left Eliminar Usuario
            .data-info
              .user-info-item
                span.data-user-text Fecha de registro:&nbsp
                span.data-user-data {{ $moment(user_data.created_on * 1000).format('DD/MM/YYYY') }}
              .user-info-item
                span.data-user-text Última conexión:&nbsp
                span.data-user-data {{ $moment(user_data.last_login * 1000).format('DD/MM/YYYY') }}
              .user-info-item
                span.data-user-text Rol:&nbsp
                span.data-user-data(v-for="group in user_data.grupos") {{ group.grupo.description }}
          .material-card
            .user-item-box
              i.el-icon-user
              p.user-item-text {{ user_data.first_name }} {{ user_data.last_name }}
            .user-item-box
              i.el-icon-message
              p.user-item-text {{ user_data.email }}
            .user-item-box
              i.el-icon-phone
              p.user-item-text {{ user_data.phone }}
</template>

<script>
  import UserApi from '@/api/UserApi'
  import RouteFilters from '@/mixins/RouteFilters'

  export default {
    name: 'ExampleModuleView',
    mixins: [RouteFilters],
    created() {
      this.user_id = (this.$route.params.id_usuario) ? this.$route.params.id_usuario : this.$helpers.getUser().id

      if (this.is_perfil_personal || this.$helpers.isAdmin()) {
        this.fetchData()
      }
    },
    data() {
      return {
        loading: true,
        imagen_loading: false,
        user_id: null,
        user_data: {}
      }
    },
    methods: {
      fetchData() {
        UserApi.getById(this.user_id).then((response) => {
          this.user_data = response.data
          this.loading = false
        }).catch(() => { this.loading = false })
      },
      editProfileImg(file) {
        const formData = new FormData()
        const user_data = {
          id: this.user_data.id
        }

        if (file.raw) {
          formData.append('imagen', file.raw)
          formData.append('data', JSON.stringify(user_data))
        }

        UserApi.updateUserImage(formData).then(response => {
          this.$refs.user_image_ref.clearFiles()
          this.$bus.$emit('userAvatarChanged')
          this.fetchData()
        }).catch(() => {
          this.imagen_loading = true
          setTimeout(() => {
            this.imagen_loading = false
          }, 50)
        })
      },
      deleteUserImageDialog() {
        this.$modal.show('dialog', {
          title: 'Eliminar Imagen',
          text: '¿Seguro que quieres <b>eliminar</b> la imagen seleccionada?',
          buttons: [
            {
              title: 'No'
            },
            {
              title: 'Sí, borrar',
              default: true,
              handler: () => {
                this.$modal.hide('dialog')
                UserApi.deleteUserImage(this.user_data.id).then(response => {
                  this.$bus.$emit('userAvatarChanged')
                  this.fetchData()
                }).catch(resp => {})
              }
            }
          ]
        })
      },
      handleEditUser(user_data) {
        this.$modal.show('usuario', JSON.parse(JSON.stringify({ 'user': user_data })))
      },
      handleDeleteUser(user_data) {
        this.$modal.show('dialog', {
          title: 'Eliminar Usuario',
          text: `¿Seguro que quieres <b>eliminar</b> este usuario? <b>${user_data.username}</b>?`,
          buttons: [
            {
              title: 'No'
            },
            {
              title: 'Sí, borrar',
              default: true,
              handler: () => {
                UserApi.delete(user_data.id).then(response => {
                  this.$modal.hide('dialog')
                  this.encodeFilters('/usuarios', null, 'raw')
                  // La linea de arriba es equivalente a esta:      this.$router.push({ path: '/usuarios' })
                }).catch(() => {})
              }
            }
          ]
        })
      }
    },
    computed: {
      is_perfil_personal() {
        return (this.user_id == this.$helpers.getUser().id)
      },
      has_user_access() {
        let boo = false

        if (this.$helpers.isAdmin()) {
          boo = true
        }

        return boo
      }
    },
    watch: {
      '$route'() {
        this.loading = true
        this.user_id = (this.$route.params.id_usuario) ? this.$route.params.id_usuario : this.$helpers.getUser().id
        this.fetchData()
      }
    }
  }
</script>

<style lang="sass" scoped>
  $breakpoint-phones: 480px
  $breakpoint-tablets: 768px
  $breakpoint-desktop: 992px
  $breakpoint-large: 1200px

  #user-profile-view
    min-height: 60vh
    .loading-box
    .profile-data-box
      .profile-main-info-box
        display: flex
        flex-wrap: wrap
        // align-items: center
        align-items: stretch
        .profile-img-box
          display: flex
          flex-wrap: wrap
          align-items: center
        .profile-action-buttons
          display: flex
          flex-direction: row
          flex-wrap: wrap
          max-width: 220px
          // .profile-action-button, .profile-action-button .custom-button
          .profile-action-button-box
            margin: 5px 5px !important
            width: 200px
            .profile-action-button
              margin: 0px !important
          .profile-action-button
            margin: 5px !important
            width: 200px
            .add-file-button, .remove-file-button
              margin: 5px 0px !important
        .data-info
          display: flex
          flex-direction: column
          justify-content: center
          margin: 10px 0px
          padding-left: 50px
          border-left: 2px solid var(--secondary-color)
          .user-info-item
            margin: 5px
            .data-user-text
              font-weight: bold
              font-size: var(--hight-font-size)
            .data-user-data
              font-weight: normal
              font-size: 14px

        // .profile-action-buttons
          flex-direction: row
          width: auto
          display: flex
          flex-wrap: wrap
          .form-buttons
            display: flex
            flex-direction: column
            font-size: 14px
            justify-content: center
            margin-left: 10px
            margin-right: 50px
            .custom-button
              margin: 5px 5px
              width: 200px
            .buttons-container
              display: flex
              flex-direction: column
              .upload-file-item
                display: flex
                max-width: 200px
          .data-info
            display: flex
            flex-direction: column
            justify-content: center
            padding-left: 50px
            border-left: 2px solid var(--secondary-color)
            .user-info-item
              margin: 5px
              .data-user-text
                font-weight: bold
                font-size: var(--hight-font-size)
              .data-user-data
                font-weight: normal
                font-size: 14px
        .profile-picture-box
          border: 5px solid var(--secondary-color)
          border-radius: 50%
          width: 200px
          height: 200px
          display: flex
          justify-content: center
          align-items: center
          .imagen-guardada
            width: 100%
            height: 100%
            display: flex
            justify-content: center
            align-items: center
          .profile-img
            width: 96%
            height: 96%
            margin-bottom: 6%
        .profile-datos
          display: flex
          .profile-textos
            border-right: 1px solid var(--font-color)
            font-size: 20px
            padding: 10px 20px
            text-align: right
          .profile-fechas
            font-size: 20px
            padding: 10px 20px
            color: var(--main-color)
      .material-card
        display: flex
        justify-content: space-between
        flex-wrap: wrap
        width: 100%
        font-size: 1.2em
        padding: 50px
        margin-top: 50px
        border-radius: 8px
        background: #fff
        box-shadow: 0 6px 10px rgba(0,0,0,.08), 0 0 6px rgba(0,0,0,.05)
        transition: .3s transform cubic-bezier(.155,1.105,.295,1.12),.3s box-shadow,.3s -webkit-transform cubic-bezier(.155,1.105,.295,1.12)
        padding: 15px 30px
        // &:hover
          transform: scale(1.05)
          box-shadow: 0 10px 20px rgba(0,0,0,.12), 0 4px 8px rgba(0,0,0,.06)
        .user-item-box
          display: flex
          align-items: center
          min-width: 200px
          flex: 1
          .user-item-text
            margin-left: 10px

  // @media(max-width: 990px)
    .material-card
      margin: 20px
  // @media (max-width: $breakpoint-tablets)
    #user-profile-view
      .profile-data-box
        .profile-img-box
          .profile-picture-box
            width: 160px !important
            height: 160px !important
  // @media (max-width: $breakpoint-phones)
    #user-profile-view
      .profile-data-box
        .profile-img-box
          display: flex
          justify-content: center
          .profile-picture-box
            width: 140px !important
            height: 140px !important
          .profile-action-buttons
            display: flex
            flex-wrap: wrap
            margin-top: 10px
            justify-content: center
            .form-buttons
              display: flex
              flex-direction: column
              font-size: 14px
              justify-content: center
              margin-left: 0px
              margin-right: 0px
            .data-info
              font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif
              display: flex
              flex-direction: column
              text-align: center
              border-left: 0px
              padding-left: 0px
          .material-card
            max-height: none
            display: flex
            justify-content: space-between
            flex-wrap: wrap
            width: 100%
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
            font-size: 1.2em
            padding: 50px
            margin-top: 50px
            border-radius: 8px
            background: #fff
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.08), 0 0 6px rgba(0, 0, 0, 0.05)
            transition: 0.3s transform cubic-bezier(0.155, 1.105, 0.295, 1.12), 0.3s box-shadow, 0.3s -webkit-transform cubic-bezier(0.155, 1.105, 0.295, 1.12)
            padding: 15px 30px
            cursor: pointer
</style>
