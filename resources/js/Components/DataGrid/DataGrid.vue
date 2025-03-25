<script>
import { h, inject } from 'vue'
import { Table, TableColumn } from 'ant-design-vue'
import isEmpty from 'lodash/isEmpty'
import { prepareFiltersQuery } from '~/utils'

const prepareSorter = (sorter) => {
  if (!sorter.order) {
    return null
  }

  return sorter.order === 'descend' ? '-' + sorter.field : sorter.field
}

export default {
  name: 'DataGrid',
  props: {
    ...Table.props,
    scroll: {
      type: Object,
      default: {
        x: 2000,
      },
    },
  },
  setup() {
    const resource = inject('$resource')
    return {
      data: resource.data,
      pagination: resource.pagination,
      sorter: resource.sorter,
      fetchResource: resource.changeParams,
    }
  },
  data() {
    return {}
  },
  methods: {
    handleChange(pagination, filters, sorter) {
      const sort = !isEmpty(sorter) ? prepareSorter(sorter) : this.sorter
      const params = {
        ...prepareFiltersQuery(filters),
      }
      if (pagination && pagination.current) {
        params.page = pagination.current
      }
      if (sort) {
        params.sort = sort
      }
      this.fetchResource(params)
    },
    getSort(columnName) {
      const isColumnSorted =
        this.sorter && new RegExp(`^-?${columnName}$`).test(this.sorter)
      if (isColumnSorted) {
        return this.sorter.substring(0, 1) === '-' ? 'descend' : 'ascend'
      }
      return false
    },
  },
  render() {
    const columns = this.$slots.default().map((column) => {
      return h(
        TableColumn,
        {
          key: column.props.name,
          title: column.props.title || column.props.name,
          dataIndex: column.props.dataIndex || column.props.name,
          width: column.props.width,
          sorter: column.props.sorter,
          sortOrder: column.props.sorter && this.getSort(column.props.name),
          fixed: column.props.fixed,
        },
        {
          default: ({ record, text }) => {
            return h(column, { value: text, record })
          },
        }
      )
    })

    return h(
      Table,
      {
        attrs: this.$attrs,
        dataSource: this.data,
        rowKey: (record) => record.id,
        pagination: this.pagination,
        scroll: this.scroll,
        onChange: this.handleChange,
      },
      columns
    )
  },
}
</script>

<style scoped></style>
