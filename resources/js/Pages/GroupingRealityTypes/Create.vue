<template>
  <create-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="groupingRealityTypes"
    resource-url="grouping-reality-types"
  >
    <template #default>
      <grouping-reality-types-form
        :realityTypes="realityTypes"
      ></grouping-reality-types-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import GroupingRealityTypesForm from '~/Components/GroupingRealityTypes/GroupingRealityTypesForm'

export default {
  name: 'GroupingRealityTypesCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    GroupingRealityTypesForm,
  },
  props: {
    realityTypes: Array,
  },
  data() {
    const title = `Создание группы типов недвижимости`
    return {
      title,
      breadcrumb: [
        {
          path: this.route('grouping-reality-types'),
          breadcrumbName: 'Список групп типов недвижимости',
        },
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
