<template>
  <create-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="banks"
  >
    <template #default>
      <banks-form></banks-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import BanksForm from '~/Components/Banks/BanksForm'

export default {
  name: 'BanksCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    BanksForm,
  },
  data() {
    const title = `Создание Банка`
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
