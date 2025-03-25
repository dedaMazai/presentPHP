<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="reality-types"
    resource-url="reality-types"
  >
    <template #default>
      <reality-types-form></reality-types-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import RealityTypesForm from '~/Components/RealityTypes/RealityTypesForm'

export default {
  name: 'RealityTypesEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    RealityTypesForm,
  },
  data() {
    const title = 'Редактирование Типа недвижимости: {{ reality_name }}'
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
