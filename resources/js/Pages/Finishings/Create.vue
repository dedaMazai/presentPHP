<template>
  <create-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="finishings"
  >
    <template #default>
      <finishings-form></finishings-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import FinishingsForm from '~/Components/Finishings/FinishingsForm'

export default {
  name: 'FinishingsCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    FinishingsForm,
  },
  props: {},
  data() {
    const title = `Создание Варианта отделки`
    return {
      title,
      breadcrumb: [
        {
          path: this.route('finishings'),
          breadcrumbName: 'Список Вариантов отделки',
        },
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
