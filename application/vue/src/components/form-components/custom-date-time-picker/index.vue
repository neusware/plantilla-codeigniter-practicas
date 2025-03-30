<template lang="pug">
  el-form-item(v-if="prop !== null" :prop="prop" :label="label")
    el-date-picker.custom-date-time-picker(
      v-model = "current_value"
      @input = "change"
      type = "datetime"
      :readonly = "readonly"
      :disabled = "disabled"
      :size = "size"
      :editable = "editable"
      :clearable = "clearable"
      :placeholder = "placeholder"
      :start-placeholder = "start_placeholder"
      :end-placeholder = "end_placeholder"
      :format = "custom_format"
      :align = "align"
      :popper-class = "popper_class"
      :picker-options = "picker_options"
      :range-separator = "range_separator"
      :default-value = "default_value"
      :default-time = "default_time"
      :value-format = "custom_format"
      :name = "name"
      :unlink-panels = "unlink_panels"
      :prefix-icon = "prefix_icon"
      :clear-icon = "clear_icon"
    )

  el-date-picker.custom-date-time-picker(
    v-else
    v-model = "current_value"
    @input = "change"
    type = "datetime"
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
    :value-format = "custom_format"
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
      type: {
        type: String,
        default: 'datetime'
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
      format: {
        type: String,
        default: 'dd-MM-yyyy HH:mm:ss' // Set yyyy-MM-dd HH:mm for type datetime
      },
      align: {
        type: 'left' | 'center' | 'right'
      },
      popper_class: {
        type: String
      },
      picker_options: {
        type: Object
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
        default: '12:00:00'
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
    data() {
      return {
        current_value: this.value && new Date(this.value),
        custom_format: this.value_format || 'yyyy-MM-dd HH:mm:ss'
      }
    },
    watch: {
      value(val) {
        this.current_value = val && new Date(val)
      }
    },
    methods: {
      change(val) {
        this.current_value = val && new Date(val)
        let parsed_date = null

        if (this.type === 'timestamp') {
          if (val) parsed_date = (new Date(val)).getTime()
        } else if (this.type === 'datetime') {
          if (val) parsed_date = this.$moment(val).format('YYYY-MM-DD HH:mm:ss')
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

  .custom-date-time-picker
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
