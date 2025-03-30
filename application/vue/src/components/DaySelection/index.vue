<template lang="pug">
  .day-selection
      span Día
      el-checkbox-group(v-model="value.dias" @change="emitValue" :disabled="disabled")
        el-checkbox-button.selection(v-for="day in days" :label="day.value" :key="day.value") {{day.label}}
</template>

<script>
export default {
  name: 'DaySelection',
  props: {
    value: {
      type: Object,
      default: () => { return { dias: [] } }
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      days: [
        { label: 'L', value: 1 },
        { label: 'M', value: 2 },
        { label: 'X', value: 3 },
        { label: 'J', value: 4 },
        { label: 'V', value: 5 },
        { label: 'S', value: 6 },
        { label: 'D', value: 7 }]
    }
  },
  methods: {
    emitValue() {
      this.$emit('input', this.value)
    }
  }
}
</script>

<style lang="sass" scoped>
  .day-selection
    display: flex
    align-items: center
    min-width: 220px
    span
      font-weight: 500
      font-size: 15px
      margin-right: 10px
    .selection
      /deep/ .el-checkbox-button__inner
        margin-right: 5px
        padding: 10px 10px !important
        background-color: #E6E6E6
        border: none
        border-radius: 50px
        width: 16px
        height: 16px
        display: flex
        justify-content: center
        align-items: center
        font-size: 10px
        transition: 0.4
        // &:hover
        //   font-size: 10px
    .selection.is-checked
      /deep/ .el-checkbox-button__inner
        background-color: #3D7DDE


</style>
