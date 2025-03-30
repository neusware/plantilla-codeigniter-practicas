<template lang="pug">
  div(v-if="type == 'number'")
    el-form-item(v-if="prop !== null" :prop="prop" :label="label")
      el-input-number(
        :value = "value"
        @input = "change"
        :min = "num_min"
        :max = "num_max"
        :step = "num_step"
        :step_strictly = "num_step_strictly"
        :precision = "num_precision"
        :size = "num_size"
        :disabled = "num_disabled"
        :controls = "num_controls"
        :controls-position = "num_controls_position"
        :label = "num_label"
        :placeholder = "num_placeholder"
      )
    .custom-input-box(v-else)
      el-input-number(
        :value = "value"
        @input = "change"
        :min = "num_min"
        :max = "num_max"
        :step = "num_step"
        :step_strictly = "num_step_strictly"
        :precision = "num_precision"
        :size = "num_size"
        :disabled = "num_disabled"
        :controls = "num_controls"
        :controls-position = "num_controls_position"
        :label = "num_label"
        :placeholder = "num_placeholder"
      )
  div(v-else-if="type == 'text' || type == 'password'")
    el-form-item(v-if="prop !== null" :prop="prop" :label="label")
      el-input.custom-input(
        :value = "value"
        @input = "change"
        :class="{transparent: transparent}"
        :type = "type_data"
        step=".01"
        :maxlength = "maxlength"
        :minlength = "minlength"
        :placeholder = "placeholder"
        :clearable = "clearable"
        :disabled = "disabled"
        :size = "size"
        :prefix-icon = "prefix_icon"
        :suffix-icon = "suffix_icon"
        :name = "name"
        :readonly = "readonly"
        :resize = "resize"
        :autofocus = "autofocus"
      )
      .show-pwd(v-if="type == 'password'" @click="showPwd")
        svg-icon.password-icon(v-if="light_password_icon" :icon-class="`${password_icon}_light`")
        svg-icon.password-icon(v-else :icon-class="password_icon")

    .custom-input-box(v-else)
      el-input.custom-input(
        :value = "value"
        @input = "change"
        :class="{transparent: transparent}"
        :type = "type_data"
        :maxlength = "maxlength"
        :minlength = "minlength"
        :placeholder = "placeholder"
        :clearable = "clearable"
        :disabled = "disabled"
        :size = "size"
        :prefix-icon = "prefix_icon"
        :suffix-icon = "suffix_icon"
        :name = "name"
        :readonly = "readonly"
        :resize = "resize"
        :autofocus = "autofocus"
      )
      .show-pwd(v-if="type == 'password'" @click="showPwd")
        svg-icon.password-icon(v-if="light_password_icon" :icon-class="`${password_icon}_light`")
        svg-icon.password-icon(v-else :icon-class="password_icon")
</template>

<script>
  export default {
    props: {
      prop: {
        type: String,
        default: null
      },
      transparent: {
        Type: Boolean,
        default: false
      },
      type: {
        type: String,
        default: 'text'
      },
      light_password_icon: {
        type: Boolean,
        default: false
      },
      value: {
        type: String | Number,
        default: ''
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
      clearable: {
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
      prefix_icon: {
        type: String
      },
      suffix_icon: {
        type: String
      },
      name: {
        type: String
      },
      readonly: {
        type: Boolean,
        default: false
      },
      resize: {
        type: 'none' | 'both' | 'horizontal' | 'vertical'
      },
      autofocus: {
        type: Boolean,
        default: false
      },
      label: {
        type: String
      },
      // Props del input number
      num_min: {
        type: Number,
        default: -Infinity
      },
      num_max: {
        type: Number,
        default: Infinity
      },
      num_step: {
        type: Number,
        default: 1
      },
      num_step_strictly: {
        type: Boolean,
        default: false
      },
      num_precision: {
        type: Number
      },
      num_size: {
        type: 'large' | 'small'
      },
      num_disabled: {
        type: Boolean,
        default: false
      },
      num_controls: {
        type: Boolean,
        default: true
      },
      num_controls_position: {
        type: 'right'
      },
      num_label: {
        type: String
      },
      num_placeholder: {
        type: String,
        default: ''
      }
    },
    data() {
      return {
        type_data: this.type,
        password_icon: 'Eye'
      }
    },
    methods: {
      change(val) {
        if (this.type === 'number') {
          if (val !== '') {
            this.$emit('input', parseFloat(val))
          } else {
            this.$emit('input', null)
          }
        } else {
          this.$emit('input', val)
        }
      },
      showPwd() {
        if (this.type_data === 'password') {
          this.type_data = 'text'
          this.password_icon = 'EyeClosed'
        } else {
          this.type_data = 'password'
          this.password_icon = 'Eye'
        }
      }
    }
  }
</script>

<style lang="sass" scoped>
  .display-flex
  .el-form-item
    margin: 5px 0px 20px 0px
    width: 100%
    line-height: 40px
    position: relative
    font-size: 14px
    /deep/ .el-form-item__content
      width: 100%
      display: flex
      align-items: center

  .custom-input-box
    display: flex
    align-items: center
  .show-pwd
    width: 0px !important
    .password-icon
      position: relative
      right: 25px
      cursor: pointer

  .custom-input
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

  .transparent
    /deep/ input
      border: none !important
      background: transparent
      &:hover
        border: none
      &:focus
        border: none
  .centered-text
    /deep/ input
      text-align: center
  .min-padding
    /deep/ input
      padding: 0px 5px
</style>
