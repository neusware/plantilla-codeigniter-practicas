import Vue from 'vue'

// custom input component created through el-input
import CustomInput from '@/components/form-components/custom-input'
Vue.component('custom-input', CustomInput)

// custom textarea component created through el-input
import CustomTextarea from '@/components/form-components/custom-textarea'
Vue.component('custom-textarea', CustomTextarea)

// custom select component created through el-select
import CustomSelect from '@/components/form-components/custom-select'
Vue.component('custom-select', CustomSelect)

// custom multiselect component created through el-select
import CustomMultiselect from '@/components/form-components/custom-multiselect'
Vue.component('custom-multiselect', CustomMultiselect)

// custom datepicker component created through el-select
import CustomDatePicker from '@/components/form-components/custom-date-picker'
Vue.component('custom-date-picker', CustomDatePicker)

// custom time picker component
import CustomTimePicker from '@/components/form-components/custom-time-picker'
Vue.component('custom-time-picker', CustomTimePicker)

// custom datepicker component created through el-select
import CustomDateTimePicker from '@/components/form-components/custom-date-time-picker'
Vue.component('custom-date-time-picker', CustomDateTimePicker)

// custom button component
import CustomButton from '@/components/form-components/custom-button'
Vue.component('custom-button', CustomButton)

// title-box component
import TitleBox from '@/components/TitleBox'
Vue.component('title-box', TitleBox)

// add-new-thing component
import AddNewThing from '@/components/AddNewThing'
Vue.component('add-new-thing', AddNewThing)

// WYSIWYG Editor
import { VueEditor } from 'vue2-editor'
Vue.component('vue-editor', VueEditor)

// Leyenda para los colores de las puntuaciones
import ReadMore from '@/components/ReadMore'
Vue.component('read-more', ReadMore)

// // custom calendar component
// // Para utilizar el calendario hace falta instalar las dependencias:
// // "@lkmadushan/vue-tuicalendar": "version"
// // "@toast-ui/vue-calendar": "version"
// custom task-calendar component
import CustomTasksCalendar from '@/components/CustomTasksCalendar'
Vue.component('CustomTasksCalendar', CustomTasksCalendar)

import Calendar from '@/components/Calendar'
Vue.component('Calendar', Calendar)
