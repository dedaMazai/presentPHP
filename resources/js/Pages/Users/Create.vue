<template>
  <create-page
    resource="users"
    title="Создание пользователя"
    :breadcrumb="breadcrumb"
  >
    <template #default>
      <users-create-form></users-create-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import UsersCreateForm from '~/Components/Users/UsersCreateForm'

export default {
  name: 'UsersCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    UsersCreateForm,
  },
  props: {
    ukProjects: Array,
  },
  data() {
    return {
      breadcrumb: [
        {path: this.route('users'), breadcrumbName: 'Список пользователей'},
        {breadcrumbName: 'Создание пользователя'},
      ],
    }
  },
}
</script>

<style scoped></style>
