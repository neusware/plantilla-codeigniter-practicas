<template lang="pug">
  .custom-tasks-calendar
    .calendar-info
      .calendario-move-to-specific-date
        div(style='width: 208px; display:block; margin: 0px 29px 20px 0px')
          span(style='display:block') Mover a fecha
          custom-date-picker(v-model="specific_date" @input="moveToDate" style="width: 206px;")
        div(style='width: 208px; display:block')
          span(style='display:block') Tipo de calendario
          custom-select.select-box(:options="caledarTypes", v-model="tipo" @input="changeType" style="width: 206px;")
      .calendario-change
      .calendar-date-info
        .render-range
          b.render-date-message {{rango}}: {{dateRange}}
      .calendar-buttons()
        custom-button.calendar-button.move-day(@click.native="onClickNavi('prev')" ) Anterior
        custom-button.calendar-button.move-today(dark @click.native="onClickNavi('today')" ) Actual
        custom-button.calendar-button.move-day(@click.native="onClickNavi('next')" ) Siguiente
    //- Componente calendario custom
    vue-tuicalendar.tui-custom-calendar(
      ref="customTasksCalendar"
      :options="final_options"
      :schedules="schedules"
      @click-schedule="handleClickSchedule"
      @after-render-schedule="handleAfterRenderSchedule"
      @before-create-schedule="disableBlueBox"
    )
</template>

<script>
  import Schedule from './Schedule.vue'
  import { VueTuicalendar } from '@lkmadushan/vue-tuicalendar'
  import 'tui-calendar/dist/tui-calendar.min.css'

  export default {
    name: 'App',
    components: {
      VueTuicalendar
    },
    props: {
      options: {
        type: Object
      },
      schedules: {
        type: Array
      }
    },
    mounted() {
      this.init()
    },
    data() {
      return {
        dateRange: '2018-12-24 ~ 12-28',
        meses: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        rango: 'Semana',
        specific_date: null,
        tipo: 'Semanal',
        default_options: {
          defaultView: 'week',
          template: {
            time: function(schedule) {
              if (schedule.borderColor !== '#AAA') {
                return '<div style="color: #fff; padding-left: 3px; border-radius: 2px;border-left:15px solid' + schedule.borderColor + ';background-color:' + schedule.bgColor + '" >' + schedule.title + '</div>'
              } else {
                return '<div style="color: #fff; padding-left: 3px; border-radius: 2px;background-color:' + schedule.bgColor + '" >' + schedule.title + '</div>'
              }
            }
          },
          useCreationPopup: false,
          useDetailPopup: false,
          showTimezoneCollapseButton: true,
          timezonesCollapsed: true,
          taskView: false, // can be also true or ['milestone', 'task']
          scheduleView: ['time'], // can be also true/false or ['allday', 'time']
          week: {
            daynames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            startDayOfWeek: 1,
            narrowWeekend: true, // para poner el fin de semana más pequeño
            // workweek: true, // para no mostrar sábado ni domingo
            hourStart: 1,
            hourEnd: 23
          },
          month: {
            daynames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            startDayOfWeek: 1,
            narrowWeekend: true, // para poner el fin de semana más pequeño
            moreLayerSize: { // para que la caja de +X más se ajuste a la cantidad de tareas
              height: 'auto'
            }
            // workweek: true, // para no mostrar sábado ni domingo
          }

        },
        caledarTypes: [{ key: 'Diario', value: 'Diario' }, { key: 'Semanal', value: 'Semanal' }, { key: 'Mensual', value: 'Mensual' }]
      }
    },
    methods: {
      init() {
        this.setRenderRangeText()
      },
      setRenderRangeText() {
        const view = this.$refs.customTasksCalendar.fireMethod('getViewName')
        const calDate = this.$refs.customTasksCalendar.fireMethod('getDate')
        const rangeStart = this.$refs.customTasksCalendar.fireMethod('getDateRangeStart')
        const rangeEnd = this.$refs.customTasksCalendar.fireMethod('getDateRangeEnd')
        let year = calDate.getFullYear()
        let month = calDate.getMonth()
        let date = calDate.getDate()
        let dateRangeText = ''
        let rangoText = ''
        let endMonth, endDate, endYear, start, end
        switch (view) {
          case 'month':
            // Sirve para refrescar las tareas del calendario
            this.$bus.$emit('change-date', new Date(`${month}/01/${year}`), 'month')
            dateRangeText = `${this.meses[month]}/${year}`
            rangoText = 'Mes'
            break
          case 'week':
            year = rangeStart.getFullYear()
            month = rangeStart.getMonth() + 1
            date = rangeStart.getDate()
            endMonth = rangeEnd.getMonth() + 1
            endDate = rangeEnd.getDate()
            endYear = rangeEnd.getFullYear()
            start = `${date}/${month}/${year}`
            end = `${endDate}/${endMonth}/${endYear}`
            // Sirve para refrescar las tareas del calendario
            this.$bus.$emit('change-date', new Date(`${month}/${date}/${year}`), 'week')
            dateRangeText = `${start} ~ ${end}`
            rangoText = 'Semana'
            break
          default:
            dateRangeText = `${date}/${month}/${year}`
            rangoText = 'Día'
            // Sirve para refrescar las tareas del calendario
            this.$bus.$emit('change-date', new Date(`${month}/${date}/${year}`), 'week')
        }
        this.dateRange = dateRangeText
        this.rango = rangoText
      },
      onClickNavi(action) {
        this.$refs.customTasksCalendar.fireMethod(action)
        this.setRenderRangeText()
      },
      moveToDate() {
        this.$refs.customTasksCalendar.fireMethod('setDate', new Date(this.specific_date))
        this.setRenderRangeText()
      },
      handleClickSchedule(schedule) {
        this.$emit('click-schedule', schedule)
      },
      disableBlueBox(e) { // Elimina el bug que mostraba un cuadro azul en la vista mensual
        e.guide.clearGuideElement()
      },
      handleAfterRenderSchedule(schedule) {
        const element = this.$refs.customTasksCalendar.fireMethod(
          'getElement',
          schedule.id,
          schedule.calendarId
        )
        const instance = new Schedule({
          propsData: { schedule }
        })
        if (element) {
          element.innerHTML = ''
          instance.$mount()
          element.appendChild(instance.$el)

          this.$emit('after-render-schedule', schedule)
        }
      },
      changeType(input) { // Cambia el tipo de vista (dia, semana, mes)
        switch (input) {
          case 'Diario':
            this.default_options.defaultView = 'day'
            break
          case 'Semanal':
            this.default_options.defaultView = 'week'
            break
          case 'Mensual':
            this.default_options.defaultView = 'month'
            // this.$refs.customTasksCalendar.template = {
            // }
            break
        }
        this.$refs.customTasksCalendar.fireMethod('changeView', this.default_options.defaultView, true)
        this.setRenderRangeText()
      }
    },
    computed: {
      // // IMPORTANTE: Aquí dejo un ejemplo de cómo serían los datos de "schedules"
      // //             que hay que pasarle al componente, lo suyo es hacer un computed
      // //             en la vista donde se llame al componente del calendario. Así:
      // tareas_formateadas() {
      //   const lista_tareas = []
      //   this.tareas_desde_la_api.forEach((tarea, index) => {
      //     lista_tareas.push({
      //       id: tarea.id,
      //       title: tarea.descripcion,
      //       category: 'time',
      //       location: tarea.texto_zona_trabajo,
      //       attendees: tarea.trabajadores.toString(), // asistentes
      //       start: tarea.fecha_inicio * 1000, // la fecha pasada a ms (milisegundos)
      //       end: tarea.fecha_fin * 1000, // la fecha pasada a ms (milisegundos)
      //       color: '#FFF',
      //       borderColor: this.getTaskColor(tarea),
      //       bgColor: '#553684',
      //       dragBgColor: '#553684'
      //     })
      //   })
      //
      //   return lista_tareas
      // }
      final_options() {
        return this.$helpers.mergeRecursive(this.default_options, this.options)
      }
    }
  }
</script>

<style lang="sass" scoped>
  /* La libreria trae un autoheight y por defecto se pone a 0 y hay que cambiarlo */
  .custom-tasks-calendar
    /deep/ .tui-custom-calendar
      height: 70vh
  .calendar-info
    display: flex
    flex-wrap: wrap
    align-items: center
    justify-content: space-between
    .calendar-date-info
      font-size: 22px
      .render-range
        display: block
        margin: 20px 0px
        .render-date-message
          margin-right: 5px
    .calendario-move-to-specific-date
      display: flex
      width: 50%
    .calendar-buttons
      padding-bottom: 10px
      .calendar-button
        font-size: 15px
        margin: 0px 5px
</style>
