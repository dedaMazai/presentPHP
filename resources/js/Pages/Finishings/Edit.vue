<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="finishing"
    resource-url="finishings"
  >
    <template #default>
      <finishings-form></finishings-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import FinishingsForm from '~/Components/Finishings/FinishingsForm'

export default {
  name: 'FinishingsEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    FinishingsForm,
  },
  data() {
    const title = 'Редактирование Варианта отделки: {{ finishing_id }}'
    return {
      title,
      breadcrumb: [
        {
          path: this.route('finishings'),
          breadcrumbName: 'Список Вариантов отделки',
        },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
