<template lang="pug">
  .special-title-box(:class="(half) ? 'half-space' : ''")
    .special-title-section
      el-tooltip(v-if="routeBack != null" effect="dark" placement="top" content="Atrás")
        el-button.go-back(type="primary" icon="el-icon-back" circle @click.prevent.native="goBack()" style="margin-right: 15px")
      svg-icon.title-icon(v-if="icon" :icon-class="icon")
      .title {{title}}
    .slot-section
      slot
</template>

<script>
  import RouteFilters from '@/mixins/RouteFilters'

  export default {
    name: 'TitleBox',
    mixins: [RouteFilters],
    props: {
      routeBack: {
        type: Array,
        default: null
      },
      title: {
        type: String
      },
      icon: {
        type: String,
        default: null
      },
      half: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        goBack() {
          if (this.routeBack != null) {
            switch (this.routeBack.length) {
              case 1:
                this.encodeFilters(this.routeBack[0])
                break
              case 2:
                this.encodeFilters(this.routeBack[0], this.routeBack[1])
                break
              case 3:
                this.encodeFilters(this.routeBack[0], this.routeBack[1], this.routeBack[2])
                break
              case 4:
                this.encodeFilters(this.routeBack[0], this.routeBack[1], this.routeBack[2], this.routeBack[3])
                break
            }
          }
        }
      }
    }
  }
</script>

<style lang="sass" scoped>
  $breakpoint-phones: 480px

  .go-back
    background: var(--main-color)
    border-color: var(--main-color)
    transition: background 1s
  .go-back:hover
    background: var(--main-color-secondary)
    transition: background 1s
  .special-title-box
    display: flex
    align-items: center
    justify-content: space-between
    flex-wrap: wrap
    margin: 0px 0px 10px 0px
    padding: 10px 10px 0px 10px
    // border-bottom: 2px solid var(--main-color)
    width: 100%
    min-width: 300px
    @media (max-width: $breakpoint-phones)
      display: block
      width: 100% !important
    .special-title-section
      flex: 1
      display: flex
      align-items: center
      margin: 5px 0px
      .title-icon
        min-width: 50px
        min-height: 50px
        margin-right: 10px
      .title
        color: var(--font-color)
        font-weight: bold
        font-size: var(--title-font-size)
    .slot-section
      flex: 1
      min-width: 190px

  .half-space.special-title-box
    width: 50%
</style>
