import {createApp, h} from 'vue'
import {createInertiaApp, Link} from '@inertiajs/inertia-vue3'
import CKEditor from '@ckeditor/ckeditor5-vue'
import Antd from 'ant-design-vue'
import {InertiaProgress} from '@inertiajs/progress'
import PageWrapper from '~/Components/PageWrapper'
import 'ant-design-vue/dist/antd.css'

InertiaProgress.init({
  delay: 50,
  color: '#1890ff',
})

createInertiaApp({
  resolve: (name) => require(`./Pages/${name}`),
  setup({el, App, props, plugin}) {
    createApp({render: () => h(App, props)})
      .component('page-wrapper', PageWrapper)
      .component('inertia-link', Link)
      .mixin({
        methods: {
          route: window.route,
          hasAnyPermission: function (permissions) {
            const allPermissions = this.$page.props.auth.permissions;

            return permissions.some( (item) => allPermissions.includes(item));
          },
          hasAnyRole: function (roles) {
            const allRoles = this.$page.props.auth.roles;

            return roles.some( (item) => allRoles.includes(item));
          },
        }
      })
      .use(plugin)
      .use(Antd)
      .use(CKEditor)
      .mount(el)
  },
})
