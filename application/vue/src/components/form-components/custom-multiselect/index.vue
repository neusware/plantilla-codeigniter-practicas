<template lang="pug">
  el-form-item.custom-form-item(v-if="prop !== null" :prop="prop" :label="label")
    el-select.custom-multiselect(
      multiple
      :value = "value"
      @input = "change"
      :class="{transparent: transparent}"
      :placeholder="placeholder"
      :disabled = "disabled"
      :collapse-tags = "collapse_tags"
      :multiple-limit = "multiple_limit"
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
      el-option(
        v-for="item in options"
        :key="item[custom_value]"
        :label="item[custom_label]"
        :value="item[custom_value]"
      )
  el-select.custom-multiselect(
    v-else
    multiple
    :value = "value"
    @input = "change"
    :class="{transparent: transparent}"
    :placeholder="placeholder"
    :disabled = "disabled"
    :collapse-tags = "collapse_tags"
    :multiple-limit = "multiple_limit"
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
    el-option(
      v-for="item in options"
      :key="item[custom_value]"
      :label="item[custom_label]"
      :value="item[custom_value]"
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
        type: Array,
        default: function() { return [] }
      },
      options: {
        type: Array,
        default: []
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
      collapse_tags: {
        type: Boolean,
        default: false
      },
      multiple_limit: {
        type: Number,
        default: 0
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
      }
    },
    methods: {
      change(val) {
        this.$emit('input', val)
      },
      emitClear() {
        this.$emit('clear')
      }
    }
  }
</script>

<style lang="sass" scoped>
  .el-form-item
    margin: 5px 0px 20px 0px
    width: 100%

  .custom-multiselect /deep/ .el-input > .el-input__suffix > .el-input__suffix-inner > i.el-icon-arrow-up:before
    content: "\E78F" !important
    color: #888 !important
  .custom-multiselect /deep/ .el-input > .el-input__suffix > .el-input__suffix-inner > i.el-icon-arrow-down:before
    content: "\E790" !important
    color: #888 !important

  .custom-multiselect
    width: 100%
    /deep/ .el-input.is-focus > input
      border: 1px solid #666
    /deep/ .el-input input
      border: 1px solid #999
      border-radius: 10px !important
      color: #888
    // /deep/ input
      border-radius: 10px
      transition: .3s
      &:hover
        transition: .3s
      &:focus
        transition: .3s
    /deep/ .el-tag
      font-size: 14px

  .custom-multiselect.transparent
    border: none !important
    /deep/ input
      border: none !important

  .custom-form-item.is-error
    /deep/ .custom-multiselect
      .el-input
        .el-input__inner, .el-input__inner:focus
          border-color: #F56C6C !important
</style>
