<template lang="pug">
  .container
    title-box(:icon="$helpers.getRouteIcon()" :title="$helpers.getRouteTitle()")
    h1.flex-center-center(style="margin-top: 0px;") Custom form items with rows
    read-more(
      text=`Formulario <b>"el-form.flex-form-with-rows"</b> va ajustandose al tamaño disponible por filas con <b>".form-row"</b>. <br> Se puede ajustar los <b>"min-width"</b> con <b>".form-flex-item-1" (min-width: 160px)</b> o con <b>".form-flex-item-2" (min-width: 320px)</b>`
      :line_clamp="3"
      :initial_line_clamp_px="57"
    )
    custom-button(animated @click.native.prevent="$router.push({ name : 'FormRoute'})") Ver "flex-form"
    el-form.flex-form-with-rows(ref="form" :model="form" :rules="userRules")
      h1.line(style="margin-top: 0px;") Custom form items with rows
      .form-row
        .form-flex-item-1
          custom-input(v-model="form.name" label="Input" placeholder="Input text" prop="name")
        .form-flex-item-1
          custom-multiselect(v-model="form.multiSelectValue" label="Multiselect" :options="multiSelectOptions" prop="multiSelectValue")
        .form-flex-item-1
          custom-select(v-model="form.selectValue" label="Select" :options="selectOptions" prop="selectValue" clearable)
      .form-row
        .form-flex-item-1
          custom-date-picker(v-model="form.date1" label="Date" placeholder="Pick a date" prop="date1")
        .form-flex-item-1
          custom-time-picker(v-model="form.date2" label="Time" prop="date2")
        .form-flex-item-1
          custom-date-time-picker(type="fixed-time" label="Date-time" placeholder="Pick a date time" v-model="form.date3", prop="date3")
        .form-flex-item-2
          custom-textarea(v-model="form.desc" label="Textarea" prop="desc")
        .form-flex-item-1

      h1.line(style="margin-top: 0px;") Element form items with rows and Vue Editor
      .form-flex-item-2
      el-form-item(label="Vue Editor" prop="desc_vue_editor" style="display: flex;")
        vue-editor(v-model="form.desc_vue_editor" )
      .form-flex-item-2
        el-form-item(label="Switch")
          el-switch(v-model="form.switch" prop="switch")

      .form-flex-item-2
        el-form-item(label="Checkbox-group")
          el-checkbox-group(v-model="form.check" prop="check")
            el-checkbox(label="Check1", name="Check1")
            el-checkbox(label="Check2", name="Check2")
      .form-flex-item-2
        el-form-item(label="Custom Checkbox-group")
          el-checkbox-group(v-model="form.check" prop="check")
            el-checkbox.custom-checkbox(label="Check3", name="Check3")
            el-checkbox.custom-checkbox(label="Check4", name="Check4")

      .form-flex-item-2
        el-form-item(label="Radio-group")
          el-radio-group(v-model="form.radio" prop="radio")
            el-radio(label="Test1" name="Test1")
            el-radio(label="Test2" name="Test2")

      .form-flex-item-2
        el-form-item(label="Custom Radio-group")
          el-radio-group(v-model="form.radio2" prop="radio2")
            el-radio.custom-radio(label="Test3")
            el-radio.custom-radio(label="Test4")

      h1.line(style="margin-top: 50px;") Submit button
      div.flex-center-center(style="padding-bottom: 30px;")
        custom-button(animated @click.native.prevent="$helpers.showNewCustomNotification('cancel!', 'warning')" dark) Cancel
        custom-button(animated @click.native.prevent='onSubmit("form")' style="margin-left: 20px;") Create
</template>

<script>
  export default {
    data() {
      return {
        multiSelectOptions: [{ label: 'Opción 1', value: '1' }, { label: 'Opción 2', value: '2' }, { label: 'Opción 3', value: '3' }],
        selectOptions: [{ label: 'shanghai', value: 'shan' }, { label: 'beijing', value: 'beij' }],
        form: {
          name: null,
          date1: null,
          date2: null,
          date3: null,
          desc: null,
          desc_vue_editor: null,
          selectValue: null,
          radio: null,
          radio2: null,
          switch: false,
          multiSelectValue: [],
          check: []
        },
        userRules: {
          name: [{ required: true, message: 'Activity name required', trigger: ['blur', 'change'] }],
          selectValue: [{ required: true, message: 'Activity zone required', trigger: ['blur', 'change'] }],
          multiSelectValue: [{ required: true, message: 'Multiselect required', trigger: ['blur', 'change'] }],
          date1: [{ required: true, message: 'Date required', trigger: ['blur', 'change'] }],
          date2: [{ required: true, message: 'Date time required', trigger: ['blur', 'change'] }],
          date3: [{ required: true, message: 'DateTime required', trigger: ['blur', 'change'] }],
          desc: [{ required: true, message: 'Description required', trigger: ['blur', 'change'] }],
          desc_vue_editor: [{ required: true, message: 'Description required', trigger: ['blur', 'change'] }]
        }
      }
    },
    methods: {
      onSubmit(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            // this.$message('submit!')
            this.$helpers.showNewCustomNotification('submit!')
          }
        })
      }
    }
  }
</script>

<style lang="sass" scoped>
  .line
    width: 100%
    text-align: center
</style>
