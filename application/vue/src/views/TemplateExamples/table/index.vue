<template lang="pug">
  .container#table-example-view
    title-box(:icon="$helpers.getRouteIcon()" :title="$helpers.getRouteTitle()")

    el-table(:data="list", v-loading.body="listLoading", element-loading-text="Loading", border="", fit="", highlight-current-row="")
      el-table-column(align="center", label="ID", width="95")
        template(slot-scope="scope")
          | {{scope.$index}}
      el-table-column(label="Title")
        template(slot-scope="scope")
          | {{scope.row.title}}
      el-table-column(label="Author", width="110", align="center")
        template(slot-scope="scope")
          span {{scope.row.author}}
      el-table-column(label="Pageviews", width="110", align="center")
        template(slot-scope="scope")
          | {{scope.row.pageviews}}
      el-table-column(class-name="status-col", label="Status", width="110", align="center")
        template(slot-scope="scope")
          el-tag(:type="scope.row.status | statusFilter") {{scope.row.status}}
      el-table-column(align="center", prop="created_at", label="Display_time", width="200")
        template(slot-scope="scope")
          i.el-icon-time
          span {{scope.row.display_time}}
</template>

<script>
import { getList } from '@/api/table'

export default {
  data() {
    return {
      list: null,
      listLoading: true
    }
  },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'gray',
        deleted: 'danger'
      }
      return statusMap[status]
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.listLoading = true
      getList(this.listQuery).then(response => {
        this.list = response.data.items
        this.listLoading = false
      })
    }
  }
}
</script>
