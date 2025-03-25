<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="client-role-types"
    resource-url="client-role-types"
  >
    <template #default>
      <client-role-types-form></client-role-types-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import ClientRoleTypesForm from '~/Components/ClientRoleTypes/ClientRoleTypesForm'

export default {
  name: 'ClientRoleTypesEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    ClientRoleTypesForm,
  },
  data() {
    const title = 'Редактирование Роли клиента: {{ role_name }}'
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
