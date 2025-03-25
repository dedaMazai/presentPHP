<template>
  <create-page
    resource="projects"
    :title="title"
    :breadcrumb="breadcrumb"
    :extra-params="[type.id]"
  >
    <template #default>
      <projects-form
        :property-types="propertyTypes"
        :cities="cities"
        :mortgageTypes="mortgageTypes"
      ></projects-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import ProjectsForm from '~/Components/Projects/ProjectsForm'

export default {
  name: 'ProjectsCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    ProjectsForm,
  },
  props: {
    type: String,
    propertyTypes: Object,
    cities: Array,
    mortgageTypes: Array,
  },
  data(props) {
    const title = `Создание проекта`
    return {
      title,
      breadcrumb: [
        {
          path: this.route('project-types'),
          breadcrumbName: 'Типы проектов',
        },
        {
          path: this.route('project-types.edit', [props.type?.id]),
          breadcrumbName: props.type?.name,
        },
        {
          path: this.route('projects', [props.type?.id]),
          breadcrumbName: 'Список проектов',
        },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
