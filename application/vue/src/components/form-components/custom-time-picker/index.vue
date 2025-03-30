<template lang="pug">
  el-form-item(v-if="prop !== null" :prop="prop" :label="label")
    .custom-time-picker
      custom-select(v-model="hours_value" :options="hours_options" :disabled="disabled" @input="changedTime")
      span.text H
      span.text :
      custom-select(v-model="minutes_value" :options="minutes_options" :disabled="disabled" @input="changedTime")
      span.text m
  .custom-time-picker(v-else)
    custom-select(v-model="hours_value" :options="hours_options" :disabled="disabled" @input="changedTime")
    span.text H
    span.text :
    custom-select(v-model="minutes_value" :options="minutes_options" :disabled="disabled" @input="changedTime")
    span.text m
</template>

<script>
  export default {
    props: {
      prop: {
        type: String,
        default: null
      },
      disabled: {
        type: Boolean,
        default: false
      },
      value: {
        type: Number,
        default: 0
      },
      label: {
        type: String
      }
    },
    mounted() {
      this.updateHoursAndMinutesValuesFromTimeInSeconds(this.value)
    },
    data() {
      return {
        hours_value: 0,
        minutes_value: 0
      }
    },
    computed: {
      hours_options() {
        const array_horas = []
        for (var i = 0; i < 24; i++) {
          array_horas.push({ label: ('0' + (i)).slice(-2), value: i })
        }
        return array_horas
      },
      minutes_options() {
        const array_horas = []
        for (var i = 0; i < 60; i++) {
          array_horas.push({ label: ('0' + (i)).slice(-2), value: i })
        }
        return array_horas
      }
    },
    methods: {
      updateHoursAndMinutesValuesFromTimeInSeconds(seconds) {
        seconds = Number(seconds)
        this.hours_value = Math.floor(seconds / 3600)
        this.minutes_value = Math.floor(seconds % 3600 / 60)
      },
      changedTime() {
        let time_in_seconds = 0

        time_in_seconds += parseInt(this.hours_value) * 3600 // pasamos las horas a segundos
        time_in_seconds += parseInt(this.minutes_value) * 60 // pasamos los minutos a segundos

        this.$emit('input', time_in_seconds)
      }
    }
  }
</script>

<style lang="sass" scoped>
  .el-form-item
    margin: 5px 0px 20px 0px
    width: 100%
    &.is-error /deep/
      input
        border-color: #f56c6c !important

  .custom-time-picker
    margin: 5px 0px
    width: 100%
    display: flex
    align-items: center
    .el-select
      margin: 0px 5px
      width: 80px
    .text
      color: black
</style>
