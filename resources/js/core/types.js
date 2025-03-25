export const PageProps = {
  resource: {
    type: String,
    required: true,
  },
  resourceUrl: {
    type: String,
  },
  title: {
    type: String,
    default: '',
  },
  breadcrumb: {
    type: Array,
  },
  extraParams: {
    type: Array,
    default: [],
  },
}
