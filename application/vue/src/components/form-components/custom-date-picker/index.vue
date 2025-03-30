<template lang="pug">
  el-form-item(v-if="prop !== null" :prop="prop" :label="label")
    el-date-picker.custom-date-picker(
      :value = "value"
      @input = "change"
      type = "date"
      :readonly = "readonly"
      :disabled = "disabled"
      :size = "size"
      :editable = "editable"
      :clearable = "clearable"
      :placeholder = "placeholder"
      :start-placeholder = "start_placeholder"
      :end-placeholder = "end_placeholder"
      :format = "format"
      :align = "align"
      :popper-class = "popper_class"
      :picker-options = "picker_options"
      :range-separator = "range_separator"
      :default-value = "default_value"
      :default-time = "default_time"
      :value-format = "value_format"
      :name = "name"
      :unlink-panels = "unlink_panels"
      :prefix-icon = "prefix_icon"
      :clear-icon = "clear_icon"
    )

  el-date-picker.custom-date-picker(
    v-else
    :value = "value"
    @input = "change"
    type = "date"
    :readonly = "readonly"
    :disabled = "disabled"
    :size = "size"
    :editable = "editable"
    :clearable = "clearable"
    :placeholder = "placeholder"
    :start-placeholder = "start_placeholder"
    :end-placeholder = "end_placeholder"
    :format = "format"
    :align = "align"
    :popper-class = "popper_class"
    :picker-options = "picker_options"
    :range-separator = "range_separator"
    :default-value = "default_value"
    :default-time = "default_time"
    :value-format = "value_format"
    :name = "name"
    :unlink-panels = "unlink_panels"
    :prefix-icon = "prefix_icon"
    :clear-icon = "clear_icon"
  )
</template>

<script>
  export default {
    props: {
      prop: {
        type: String,
        default: null
      },
      value: {
        type: Date | String
      },
      readonly: {
        type: Boolean,
        default: false
      },
      disabled: {
        type: Boolean,
        default: false
      },
      size: {
        type: 'medium' | 'small' | 'mini'
      },
      editable: {
        type: Boolean,
        default: true
      },
      clearable: {
        type: Boolean,
        default: true
      },
      placeholder: {
        type: String
      },
      start_placeholder: {
        type: String
      },
      end_placeholder: {
        type: String
      },
      type: {
        type: String,
        default: 'date'
      },
      format: {
        type: String,
        default: 'dd/MM/yyyy' // Set yyyy-MM-dd HH:mm for type datetime
      },
      align: {
        type: 'left' | 'center' | 'right'
      },
      popper_class: {
        type: String
      },
      picker_options: {
        type: Object,
        default: () => {
          return {
            firstDayOfWeek: 1
          }
        }
      },
      range_separator: {
        type: String,
        default: '-'
      },
      default_value: {
        type: String
      },
      default_time: {
        type: String,
        default: ''
      },
      value_format: {
        type: String
      },
      name: {
        type: String
      },
      unlink_panels: {
        type: Boolean,
        default: false
      },
      label: {
        type: String
      },
      prefix_icon: {
        type: String,
        default: 'el-icon-date'
      },
      clear_icon: {
        type: String,
        default: 'el-icon-circle-close'
      }
    },
    methods: {
      change(val) {
        let parsed_date = null

        if (this.type === 'timestamp') {
          if (val) parsed_date = (new Date(val)).getTime()
        } else if (this.type === 'datetime') {
          if (val) parsed_date = this.$moment(val).format('YYYY-MM-DD HH:mm')
        } else if (this.type === 'date') {
          if (val) parsed_date = this.$moment(val).format('YYYY-MM-DD')
        }

        this.$emit('input', parsed_date)
      }
    }
  }
</script>

<style lang="sass" scoped>
  .el-form-item
    margin: 5px 0px 20px 0px
    width: 100%

  .el-date-editor.el-input
    width: 100%

  .custom-date-picker
    /deep/ input
      border: 1px solid #BBB
      border-radius: 10px
      transition: .3s
      &:hover
        border: 1px solid #999
        transition: .3s
      &:focus
        border: 1px solid #666
        transition: .3s
  .no-borders
    /deep/ input
      border: none !important
  .transparent
    /deep/ input
      border: none !important
  .centered-text
    /deep/ input
      text-align: center

</style>
