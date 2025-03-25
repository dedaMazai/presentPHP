<template>
  <edit-page
    resource="user"
    resource-url="users"
    :title="title"
    :breadcrumb="breadcrumb"
    edit-only
  >
    <template #default>
      <users-form></users-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import UsersForm from '~/Components/Users/UsersForm'

export default {
  name: 'UserEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    UsersForm,
  },
  props: {
    user: Object,
  },
  data() {
    const title = 'Редактирование данных клиента'
    return {
      title,
      breadcrumb: [
        {path: this.route('users'), breadcrumbName: 'Список клиентов'},
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
