<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="banks"
    resource-url="banks"
  >
    <template #default>
      <banks-form></banks-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import BanksForm from '~/Components/Banks/BanksForm'

export default {
  name: 'BanksEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    BanksForm,
  },
  data() {
    const title = 'Редактирование Банка: {{ bank_id }}'
    return {
      title,
      breadcrumb: [
        {
          path: this.route('banks'),
          breadcrumbName: 'Справочник Банков',
        },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
