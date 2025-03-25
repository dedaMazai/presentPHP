<template>
  <create-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="reality-types"
  >
    <template #default>
      <reality-types-form></reality-types-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import RealityTypesForm from '~/Components/RealityTypes/RealityTypesForm'

export default {
  name: 'RealityTypesCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    RealityTypesForm,
  },
  props: {},
  data() {
    const title = `Создание Типа недвижимости`
    return {
      title,
      breadcrumb: [
        {
          path: this.route('reality-types'),
          breadcrumbName: 'Список Типов недвижимости',
        },
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
