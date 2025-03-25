<template>
  <create-page
    :breadcrumb="breadcrumb"
    resource="instructions"
    title="Создание Инструкции"
  >
    <template #default>
      <instruction-form></instruction-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import InstructionForm from '~/Components/Instructions/InstructionForm'

export default {
  name: 'InstructionCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    InstructionForm,
  },
  data() {
    return {
      breadcrumb: [
        {
          path: this.route('instructions'),
          breadcrumbName: 'Список инструкций',
        },
        { breadcrumbName: 'Создание инструкции' },
      ],
    }
  },
}
</script>

<style scoped></style>
