export const CellProps = {
  name: {
    type: String,
    required: true,
  },
  title: {
    type: String,
  },
  dataIndex: {
    type: String,
  },
  width: {
    type: String,
  },
  sorter: {
    type: Boolean,
  },
  value: {
    type: [String, Number, Array, Object],
  },
  record: {
    type: Object,
  },
}
