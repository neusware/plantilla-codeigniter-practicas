<template lang="pug">
  el-form-item.custom-form-item(v-if="prop !== null" :prop="prop" :label = "label")
    el-select.custom-select(
      :value="value"
      @input="change"
      :class="{transparent: transparent, 'small-height': small_height}"
      :placeholder="placeholder"
      :disabled="disabled"
      :clearable="clearable"
      :name = "name"
      :filterable = "filterable"
      :allow-create = "allow_create"
      :default-first-option = "default_first_option"
      :reserve-keyword = "reserve_keyword"
      :remote = "remote"
      :remote-method = "remote_method"
      :loading = "loading"
      :loading-text = "loading_text"
      :no-match-text = "no_match_text"
      :no-data-text = "no_data_text"
      :popper-class = "popper_class"
      :popper-append-to-body = "popper_append_to_body"
      :automatic-dropdown = "automatic_dropdown"
      @clear="emitClear"
    )
      template(v-if="controller_name")
        el-option(
          v-for="item in options_dropdown"
          :key="item[custom_value]"
          :label="createLabelWithExtraData(item[custom_label], item.datoExtra)"
          :value="item[custom_value]"
        )
      template(v-else)
        el-option(
          v-for="item in formatted_options"
          :key="item[custom_value]"
          :label="createLabelWithExtraData(item[custom_label], item.datoExtra)"
          :value="item[custom_value]"
        )

  el-select.custom-select(
    v-else
    :value="value"
    @input="change"
    :class="{transparent: transparent, 'small-height': small_height}"
    :placeholder="placeholder"
    :disabled="disabled"
    :clearable="clearable"
    :name = "name"
    :filterable = "filterable"
    :allow-create = "allow_create"
    :default-first-option = "default_first_option"
    :reserve-keyword = "reserve_keyword"
    :remote = "remote"
    :remote-method = "remote_method"
    :loading = "loading"
    :loading-text = "loading_text"
    :no-match-text = "no_match_text"
    :no-data-text = "no_data_text"
    :popper-class = "popper_class"
    :popper-append-to-body = "popper_append_to_body"
    :automatic-dropdown = "automatic_dropdown"
    @clear="emitClear"
  )
    template(v-if="controller_name")
      el-option(
        v-for="item in options_dropdown"
        :key="item[custom_value]"
        :label="createLabelWithExtraData(item[custom_label], item.datoExtra)"
        :value="item[custom_value]"
      )
    template(v-else)
      el-option(
        v-for="item in formatted_options"
        :key="item[custom_value]"
        :label="createLabelWithExtraData(item[custom_label], item.datoExtra)"
        :value="item[custom_value]"
      )
</template>

<script>
  export default {
    mounted() {
      if (this.controller_name) {
        if (this.module_name) {
          import('@/modules/' + this.module_name + '/api/' + this.$helpers.capitalizeEachWord(this.controller_name) + 'Api.js').then((controller_js) => {
            const ControllerApi = controller_js.default.constructor()
            ControllerApi.getDropdown().then(response => {
              this.options_dropdown = response.data
            })
          })
        } else {
          import('@/api/' + this.$helpers.capitalizeEachWord(this.controller_name) + 'Api.js').then((controller_js) => {
            const ControllerApi = controller_js.default.constructor()
            ControllerApi.getDropdown().then(response => {
              this.options_dropdown = response.data
            })
          })
        }
      }
    },
    props: {
      prop: {
        type: String,
        default: null
      },
      value: {
        type: String | Number,
        default: function() { return '' }
      },
      options: {
        type: Array,
        default: function() { return [] }
      },
      transparent: {
        Type: Boolean,
        Default: false
      },
      placeholder: {
        type: String,
        default: ''
      },
      disabled: {
        type: Boolean,
        default: false
      },
      clearable: {
        type: Boolean,
        default: false
      },
      name: {
        type: String,
        default: ''
      },
      filterable: {
        type: Boolean,
        default: false
      },
      allow_create: {
        type: Boolean,
        default: false
      },
      default_first_option: {
        type: Boolean,
        default: false
      },
      reserve_keyword: {
        type: Boolean,
        default: false
      },
      remote: {
        type: Boolean,
        default: false
      },
      remote_method: {
        type: Function
      },
      loading: {
        type: Boolean,
        default: false
      },
      loading_text: {
        type: String,
        default: 'Cargando'
      },
      no_match_text: {
        type: String,
        default: 'No coincide ninguna opción'
      },
      no_data_text: {
        type: String,
        default: 'No hay opciones'
      },
      popper_class: {
        type: String
      },
      popper_append_to_body: {
        type: Boolean,
        default: true
      },
      automatic_dropdown: {
        type: Boolean,
        default: false
      },
      label: {
        type: String
      },
      custom_label: {
        type: String,
        default: 'label'
      },
      custom_value: {
        type: String,
        default: 'value'
      },
      order_by: {
        type: String,
        default: null
      },
      small_height: {
        type: Boolean,
        default: false
      },
      strip_tags: {
        type: Boolean,
        default: false
      },
      controller_name: {
        type: String,
        default: null
      },
      module_name: {
        type: String,
        default: null
      }
      // put custom props for this project below
    },
    data() {
      return {
        options_dropdown: []
      }
    },
    methods: {
      change(val) {
        this.$emit('input', val)
      },
      emitClear() {
        this.$emit('clear')
      },
      createLabelWithExtraData(label, datoExtra) {
        return (datoExtra !== undefined) ? label + '                                                 ' + datoExtra : label
      }
    },
    computed: {
      formatted_options() {
        let f_options = []

        if (this.strip_tags) {
          this.options.forEach(option => {
            const op_label = this.$helpers.strip_html_tags(option.label)
            const f_opt = option
            f_opt.label = op_label
            f_options.push(f_opt)
          })
        } else {
          f_options = this.options
        }

        if (this.order_by) {
          f_options.sort((a, b) => (a[this.order_by] > b[this.order_by]) ? 1 : ((b[this.order_by] > a[this.order_by]) ? -1 : 0))
        }

        return f_options
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

  .el-select /deep/ .el-input > .el-input__suffix > .el-input__suffix-inner > i.el-icon-arrow-up:before
    content: "\E78F" !important
    color: #888 !important
  .el-select /deep/ .el-input > .el-input__suffix > .el-input__suffix-inner > i.el-icon-arrow-down:before
    content: "\E790" !important
    color: #888 !important

  .el-select.custom-select
    width: 100%
    &.small-height
      /deep/ .el-input input
        max-height: 33px !important
    /deep/ .el-input.is-focus > input
      border: 1px solid #666
    /deep/ .el-input input
      border: 1px solid #999
      border-radius: 10px !important
      color: #888
    /deep/ .el-select__caret
      color: #888 !important

  .custom-select.transparent
    border: none !important
    /deep/ input
      border: none !important

  .transparent
    .el-select.custom-select
      border: none !important
      /deep/ input
        border: none !important
</style>
