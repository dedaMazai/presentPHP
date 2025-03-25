<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="groupingRealityTypes"
    resource-url="grouping-reality-types"
  >
    <template #default>
      <grouping-reality-types-form
        :initial-values="groupingRealityTypes"
        :realityTypes="realityTypes"
      ></grouping-reality-types-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import GroupingRealityTypesForm from '~/Components/GroupingRealityTypes/GroupingRealityTypesForm'

export default {
  name: 'GroupingRealityTypesEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    GroupingRealityTypesForm,
  },
  props: {
    groupingRealityTypes: Object,
    realityTypes: Array,
  },
  data() {
    const title = 'Редактирование группы типов недвижимости: {{ group_reality_name }}'
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
