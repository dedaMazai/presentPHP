<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :extra-params="[ukProject.id]"
    :title="title"
    resource="building"
    resource-url="uk-projects.buildings"
  >
    <template #default>
      <uk-buildings-form></uk-buildings-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import UkBuildingsForm from '~/Components/UkProjects/UkBuildingsForm'

export default {
  name: 'UkProjectBuildingEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    UkBuildingsForm,
  },
  props: {
    ukProject: Object,
    building: Object,
  },
  data(props) {
    const title = 'Редактирование корпуса: {{ build_name }}'
    return {
      title,
      breadcrumb: [
        {
          path: this.route('uk-projects'),
          breadcrumbName: 'Список проектов УК',
        },
        {
          path: this.route('uk-projects.edit', [props.ukProject?.id]),
          breadcrumbName: props.ukProject?.name,
        },
        {
          path: this.route('uk-projects.buildings', [props.ukProject?.id]),
          breadcrumbName: 'Список корпусов',
        },
        {breadcrumbName: title},
      ],
    }
  }
}
</script>

<style scoped></style>
