<template lang="pug">
  .container#dashboard-view
    title-box(:icon="$helpers.getRouteIcon(true)" :title="$helpers.getRouteTitle()")
    h2.example-user-info-title Datos del usuario autenticado:
    .example-info-box
      .example-info-item
        .example-info-title - ID:
        .example-info-data {{ $helpers.getUser().id }}
      .example-info-item
        .example-info-title - Nombre:
        .example-info-data {{ $helpers.getUser().first_name }}
      .example-info-item
        .example-info-title - Apellido:
        .example-info-data {{ $helpers.getUser().last_name }}
      .example-info-item
        .example-info-title - Nombre completo:
        .example-info-data {{ $helpers.getUser().name }}
      .example-info-item
        .example-info-title - Email:
        .example-info-data {{ $helpers.getUser().email }}

    .read-more-examples-box
      h2 Read more Examples

      read-more(
        text="Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
      )
      read-more(
        text="Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
        :line_clamp="2"
        :initial_line_clamp_px="37"
      )
      read-more(
        text="Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?"
        :line_clamp="3"
        :initial_line_clamp_px="57"
      )
</template>

<script>
  export default {
    name: 'DashboardView',
    data() {
      return {
      }
    }
  }
</script>

<style lang="sass" scoped>
  #dashboard-view
    .example-user-info-title
    .example-info-box
      .example-info-item
        display: flex
        align-items: center
        margin-bottom: 5px
        .example-info-title
          font-weight: 500
        .example-info-data
          margin-left: 10px

    .chart-wrapper
      background: #fff
      padding: 16px 16px 0
      margin-bottom: 32px
</style>
