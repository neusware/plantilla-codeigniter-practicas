<template lang="pug">
  .container
    title-box(:icon="$helpers.getRouteIcon()" :title="$helpers.getRouteTitle()")

    h1.flex-center-center(style="margin-top: 0px;") Description
    read-more(
      text=`Formulario <b>"el-form.flex-form"</b> va ajustandose al tamaño disponible. <br> Se puede ajustar los <b>"min-width"</b> con <b>".form-flex-item-1" (min-width: 160px)</b> o con <b>".form-flex-item-2" (min-width: 320px)</b>.`
      :line_clamp="3"
      :initial_line_clamp_px="57"
    )
    custom-button(animated @click.native.prevent="$router.push({ name : 'FormWithRowsRoute'})") Ver "flex-form-with-rows"
    el-form.flex-form(ref="form" :model="form" :rules="userRules")
      h1.flex-center-center(style="width: 100%; margin-top: 20px;") Custom form items
      .form-flex-item-1
        custom-input(v-model="form.name" label="Input" placeholder="Input text" prop="name")
      .form-flex-item-1
        custom-multiselect(v-model="form.multiSelectValue" label="Multiselect" :options="multiSelectOptions" prop="multiSelectValue")
      .form-flex-item-1
        custom-select(v-model="form.selectValue" controller_name="user" module_name="UsersModule" label="Select" :options="selectOptions" prop="selectValue" clearable)
      .form-flex-item-1
        custom-select( v-model="form.selectValue" label="Select" :options="filtered_options" :remote_method="searchDropdown" filterable remote reserve_keyword prop="selectValue" clearable)
      .form-flex-item-1
        custom-date-picker(v-model="form.date1" label="Date" placeholder="Pick a date" prop="date1")
      .form-flex-item-1
        custom-time-picker(v-model="form.date2" label="Time" prop="date2")
      .form-flex-item-1
        custom-date-time-picker(type="fixed-time" label="Date-time" placeholder="Pick a date time" v-model="form.date3", prop="date3")
      .form-flex-item-2
        custom-textarea(v-model="form.desc" label="Textarea" prop="desc")
      .form-flex-item-1

      h1.flex-center-center(style="width: 100%; margin-top: 0px;") Element form items and Vue Editor
      .form-flex-item-2
        el-form-item(label="Vue Editor" prop="desc_vue_editor" style="display: flex;")
          vue-editor(v-model="form.desc_vue_editor")
        el-form-item(label="Switch")
          el-switch(v-model="form.switch" prop="switch")
        el-form-item(label="Checkbox-group")
          el-checkbox-group(v-model="form.check" prop="check")
            el-checkbox(label="Check1", name="Check1")
            el-checkbox(label="Check2", name="Check2")
        el-form-item(label="Custom Checkbox-group")
          el-checkbox-group(v-model="form.check" prop="check")
            el-checkbox.custom-checkbox(label="Check3", name="Check3")
            el-checkbox.custom-checkbox(label="Check4", name="Check4")
        el-form-item(label="Radio-group")
          el-radio-group(v-model="form.radio" prop="radio")
            el-radio(label="Test1" name="Test1")
            el-radio(label="Test2" name="Test2")
        el-form-item(label="Custom Radio-group")
          el-radio-group(v-model="form.radio2" prop="radio2")
            el-radio.custom-radio(label="Test3")
            el-radio.custom-radio(label="Test4")

      h1.flex-center-center(style="width: 100%; margin-top: 50px;") Submit button
      div.flex-center-center(style="width: 100%; padding-bottom: 30px;")
        custom-button(animated @click.native.prevent="$helpers.showNewCustomNotification('cancel!', 'warning')" dark) Cancel
        custom-button(animated @click.native.prevent='onSubmit("form")' style="margin-left: 20px;") Create
</template>

<script>
  import GroupsApi from '@/api/GroupsApi'
  export default {
    data() {
      return {
        multiSelectOptions: [{ label: 'Opción 1', value: '1' }, { label: 'Opción 2', value: '2' }, { label: 'Opción 3', value: '3' }],
        selectOptions: [{ label: 'shanghai', value: 'shan' }, { label: 'beijing', value: 'beij' }],
        filtered_options: [],
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
      },
      searchDropdown(query) {
        if (query.length > 2) {
          const data = { description: query }
          GroupsApi.getFilteredDropdown(data).then(response => {
            this.filtered_options = response.data
          })
            .catch(e => {})
        } else {
          this.filtered_options = []
        }
      }
    }
  }
</script>

<style lang="sass" scoped>
</style>
