<template>
  <create-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="client-role-types"
  >
    <template #default>
      <client-role-types-form></client-role-types-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import ClientRoleTypesForm from '~/Components/ClientRoleTypes/ClientRoleTypesForm'

export default {
  name: 'ClientRoleTypeCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    ClientRoleTypesForm,
  },
  props: {},
  data() {
    const title = `Создание Роли клиента`
    return {
      title,
      breadcrumb: [
        {
          path: this.route('client-role-types'),
          breadcrumbName: 'Список Ролей клиентов',
        },
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
