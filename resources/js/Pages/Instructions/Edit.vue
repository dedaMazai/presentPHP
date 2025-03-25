<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="instruction"
    resource-url="instructions"
  >
    <template #default>
      <instruction-form></instruction-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import InstructionForm from '~/Components/Instructions/InstructionForm'

export default {
  name: 'InstructionEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    InstructionForm,
  },
  data() {
    const title = 'Редактирование инструкции: #{{ id }}'
    return {
      title,
      breadcrumb: [
        {
          path: this.route('instructions'),
          breadcrumbName: 'Список инструкций',
        },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
