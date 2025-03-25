<template>
  <edit-page
    resource="projectType"
    resourceUrl="project-types"
    :title="title"
    :breadcrumb="breadcrumb"
  >
    <template #actions="{ data }">
      <a-button @click="$inertia.get(route('projects', [data.id]))">
        Просмотр списка проектов
      </a-button>
    </template>
    <template #default>
      <project-type-form></project-type-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import ProjectTypeForm from '~/Components/Projects/ProjectTypeForm'

export default {
  name: 'AdsEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    ProjectTypeForm,
  },
  data() {
    const title = 'Редактирование типа проекта: {{ name }}'
    return {
      title,
      breadcrumb: [
        { path: this.route('project-types'), breadcrumbName: 'Типы проектов' },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
