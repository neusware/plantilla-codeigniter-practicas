<template lang="pug">
  .schedule(:style="custom_schedule_style") {{ schedule.title }} - {{ schedule.location }} - {{ schedule.attendees }}
</template>

<script>
  import Vue from 'vue'

  export default Vue.extend({
    props: ['schedule'],
    methods: {
    },
    computed: {
      custom_schedule_style() {
        return { 'border-left': '4px solid ' + ((this.schedule.borderColor) ? this.schedule.borderColor : this.schedule.bgColor) }
      }
    }
  })
</script>

<style lang="sass" scoped>
  .schedule
    padding: 5px
    width: 100%
    height: 100%
</style>
