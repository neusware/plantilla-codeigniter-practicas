<template lang="pug">
  el-form-item(v-if="prop !== null" :prop="prop" :label="label")
    el-input.custom-textarea(
      :value = "value"
      @input = "change"
      type = "textarea"
      :class="{transparent: transparent}"
      :maxlength = "maxlength"
      :minlength = "minlength"
      :placeholder = "placeholder"
      :disabled = "disabled"
      :size = "size"
      :name = "name"
      :readonly = "readonly"
      :resize = "resize"
      :autofocus = "autofocus"
      :rows = "rows"
      :autosize = "autosize"
    )

  el-input.custom-textarea(
    v-else
    :value = "value"
    @input = "change"
    type = "textarea"
    :class="{transparent: transparent}"
    :maxlength = "maxlength"
    :minlength = "minlength"
    :placeholder = "placeholder"
    :disabled = "disabled"
    :size = "size"
    :name = "name"
    :readonly = "readonly"
    :resize = "resize"
    :autofocus = "autofocus"
    :rows = "rows"
    :autosize = "autosize"
  )
</template>

<script>
  export default {
    props: {
      value: {
        type: String | Number,
        default: ''
      },
      transparent: {
        Type: Boolean,
        Default: false
      },
      minlength: {
        type: Number | String
      },
      maxlength: {
        type: Number | String
      },
      placeholder: {
        type: String,
        default: ''
      },
      disabled: {
        type: Boolean,
        default: false
      },
      size: {
        type: 'medium' | 'small' | 'mini'
      },
      name: {
        type: String
      },
      readonly: {
        type: Boolean,
        default: false
      },
      resize: {
        type: 'none' | 'both' | 'horizontal' | 'vertical',
        default: 'none'
      },
      autofocus: {
        type: Boolean,
        default: false
      },
      label: {
        type: String
      },
      rows: {
        type: Number | String,
        default: 2
      },
      prop: {
        type: String,
        default: null
      },
      autosize: {
        type: Boolean | Object,
        default: false
      }
    },
    methods: {
      change(val) {
        this.$emit('input', val)
      }
    }
  }
</script>

<style lang="sass" scoped>
  .el-form-item
    margin: 5px 0px 20px 0px
    width: 100%

  .custom-textarea
    /deep/ textarea
      border: 1px solid #BBB
      color: var(--font-color)
      border-radius: 10px
      transition: .3s
      &:hover
        border: 1px solid #999
        transition: .3s
      &:focus
        border: 1px solid #666
        transition: .3s

  .transparent
    /deep/ textarea
      border: none !important
      background: transparent !important
      &:hover
        border: none
      &:focus
        border: none

  .no-borders
    .custom-textarea
      border: none !important

</style>
