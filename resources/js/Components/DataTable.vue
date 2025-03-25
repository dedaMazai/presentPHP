<script>
import {h} from 'vue'
import {Table} from 'ant-design-vue'
import {prepareFiltersQuery} from '~/utils'
import {isEmpty} from 'lodash'

const prepareSorter = sorter => {
  if (!sorter.order) {
    return null
  }

  return sorter.order === 'descend' ? '-' + sorter.field : sorter.field
}

export default {
  props: {
    ...Table.props,
    ...{
      fetchData: {
        type: Function,
        required: true
      },
      defaultSorter: {
        type: String,
        default: null
      }
    }
  },
  data() {
    return {
      localLoading: false
    }
  },
  computed: {
    localColumns() {
      return this.columns.map(column => {
        const columnName = column.dataIndex
        const isColumnSorted = this.defaultSorter && new RegExp(`^-?${columnName}$`).test(this.defaultSorter)

        if (column.sorter && isColumnSorted) {
          column.sortOrder = this.defaultSorter.substring(0, 1) === '-' ? 'descend' : 'ascend'
        } else {
          delete column.sortOrder
        }

        return column
      })
    }
  },
  methods: {
    refresh() {
      this.handleChange()
    },
    handleChange(pagination, filters, sorter) {
      this.localLoading = true

      const params = {
        ...prepareFiltersQuery(filters)
      }
      if (pagination && pagination.current) {
        params.page = pagination.current
      }
      const sort = !isEmpty(sorter) ? prepareSorter(sorter) : this.defaultSorter
      if (sort) {
        params.sort = sort
      }

      this.fetchData(params)
    }
  },
  render() {
    const localKeys = Object.keys({...this.$data, ...this.$options.computed})
    const props = {}

    Object.keys(Table.props).forEach(key => {
      const localKey = `local${key.substring(0, 1).toUpperCase()}${key.substring(1)}`
      if (localKeys.includes(localKey)) {
        props[key] = this[localKey]
        return props[key]
      }

      if (this[key]) {
        props[key] = this[key]
      }
      return props[key]
    })

    return h(
      Table,
      {
        ...props,
        onChange: this.handleChange
      },
      this.$slots
    )
  }
}
</script>
