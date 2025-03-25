<script>
import {h, resolveComponent} from 'vue'
import {PageHeader} from 'ant-design-vue'


const breadcrumbItemRender = ({route, routes}) => {
  if (routes.indexOf(route) === routes.length - 1) {
    return h('span', route.breadcrumbName)
  }

  return h(
    resolveComponent('inertia-link'),
    {href: route.path},
    route.breadcrumbName
  )
}

export default {
  props: {
    title: {
      type: String,
      required: true,
    },
    breadcrumb: {
      type: Object,
      required: false,
    },
    onBack: {
      type: Function,
      required: false,
    },
  },
  computed: {
    backPath() {
      const {breadcrumb} = this
      if (breadcrumb && breadcrumb.length >= 2) {
        return breadcrumb[breadcrumb.length - 2]?.path
      }
      return null
    },
  },
  render() {
    const {title, breadcrumb, onBack, backPath} = this
    const defaultOnBackHandler =
      backPath &&
      (() => {
        this.$inertia.get(backPath)
      })

    return h(
      PageHeader,
      {
        title,
        breadcrumb: {routes: breadcrumb, itemRender: breadcrumbItemRender},
        ghost: true,
        onBack: onBack || defaultOnBackHandler,
      },
      this.$slots
    )
  },
}
</script>

<style></style>
