<template lang="pug">
  .container
    title-box(:icon="$helpers.getRouteIcon(true)" :title="$helpers.getRouteTitle()")
      add-new-thing(v-if="$helpers.isAdmin()" text="Añadir Cita" @click="$modal.show('cita')")

    //- .header
      .titulo
        svg-icon.logo-img(icon-class="BLA_sesiones_verde" style="font-size:35px; margin-right:15px")
        .title-listado
          span Sesiones
        .add-cita
          .add-pago-gasto(@click="$modal.show('cita')")
            .icon-pago +
            .title-add-pago Añadir Cita
    custom-tasks-calendar.calendario-tareas(:options="calendar_options" :schedules="events" @click-schedule="handleClickSchedule")
</template>

<script>
  export default {
    data() {
      return {
        calendar_options: {
          defaultView: 'week',
          template: {
            monthGridHeaderExceed: function(hiddenSchedules) {
              return '<span class="calendar-more-schedules">+' + hiddenSchedules + ' más</span>'
            }
          },
          week: {
            startDayOfWeek: 1,
            hourStart: 5
          }
        },
        tareas_desde_la_api: [
          {
            id: 1,
            descripcion: 'Evento primero',
            fecha_inicio: new Date().setDate(new Date().getDate() + 1),
            fecha_fin: new Date().setDate(new Date().getDate() + 1)
          },
          {
            id: 2,
            descripcion: 'Evento segundo',
            fecha_inicio: new Date().setDate(new Date().getDate() + 2),
            fecha_fin: new Date().setDate(new Date().getDate() + 2)
          },
          {
            id: 3,
            descripcion: 'Evento tercero',
            fecha_inicio: new Date().setDate(new Date().getDate() + 1),
            fecha_fin: new Date().setDate(new Date().getDate() + 1)
          },
          {
            id: 4,
            descripcion: 'Evento cuarto',
            fecha_inicio: new Date().setDate(new Date().getDate() + 1),
            fecha_fin: new Date().setDate(new Date().getDate() + 1)
          },
          {
            id: 5,
            descripcion: 'Evento quinto',
            fecha_inicio: new Date().setDate(new Date().getDate() - 1),
            fecha_fin: new Date().setDate(new Date().getDate() - 1)
          },
          {
            id: 6,
            descripcion: 'Evento sexto',
            fecha_inicio: new Date().setDate(new Date().getDate() - 7),
            fecha_fin: new Date().setDate(new Date().getDate() - 7)
          },
          {
            id: 7,
            descripcion: 'Evento séptimo',
            fecha_inicio: new Date().setDate(new Date().getDate() - 1),
            fecha_fin: new Date().setDate(new Date().getDate() - 1)
          }]
      }
    },
    methods: {
      handleClickSchedule(sesion) {
        sesion = sesion.schedule
        this.$modal.show('dialog', {
          title: 'Datos sesion',
          text: '<p><b>Datos de la sesion</b></p> <p>Descripción de evento: <b>' + sesion.recurrenceRule + '</b></p>',
          buttons: [
            {
              title: 'Eliminar sesion',
              default: true,
              handler: () => {
                this.$modal.hide('dialog')
              }
            },
            {
              title: 'Editar',
              default: true,
              handler: () => {
                this.$modal.hide('dialog')
              }
            },
            {
              title: 'Cerrar'
            }
          ]
        })
      }
    },
    computed: {
      events() {
        const event_list = []
        this.tareas_desde_la_api.forEach((tarea, index) => {
          event_list.push({
            id: tarea.id,
            title: tarea.descripcion + ' | ' + tarea.id,
            recurrenceRule: tarea.descripcion,
            category: 'time',
            // location: (tarea.fecha_inicio).format('HH:mm'),
            // attendees: tarea.trabajadores.toString()
            start: tarea.fecha_inicio,
            end: tarea.fecha_fin,
            color: '#FFF',
            borderColor: '#dd1c5f',
            bgColor: '#553684',
            dragBgColor: '#553684',
            raw: tarea
          })
        })
        return event_list
      }
    }
  }
</script>


<style lang="sass" scoped>
</style>
