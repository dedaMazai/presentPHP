<template>
  <create-page
    resource="projectTypes"
    resourceUrl="project-types"
    title="Создание типа проекта"
    :breadcrumb="breadcrumb"
  >
    <template #default>
      <project-type-form></project-type-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import ProjectTypeForm from '~/Components/Projects/ProjectTypeForm'

export default {
  name: 'ProjectTypeCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    ProjectTypeForm,
  },
  data() {
    return {
      breadcrumb: [
        { path: this.route('project-types'), breadcrumbName: 'Типы проектов' },
        { breadcrumbName: 'Создание типа проекта' },
      ],
    }
  },
}
</script>

<style scoped></style>
