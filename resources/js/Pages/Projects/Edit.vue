<template>
  <edit-page
    resource="project"
    resource-url="projects"
    :title="title"
    :breadcrumb="breadcrumb"
    :extra-params="[type.id]"
  >
    <template #actions="{ data }">
      <a-button
        @click="$inertia.get(route('projects.articles', [type.id, data.id]))"
      >
        Просмотр списка статей
      </a-button>
      <a-button
        @click="$inertia.get(route('projects.mortgage-programs', [type.id, data.id]))"
      >
        Просмотр списка предложений банков
      </a-button>
    </template>
    <template #default>
      <projects-form
        :property-types="propertyTypes"
        :cities="cities"
        :mortgageTypes="mortgageTypes"
      ></projects-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import ProjectsForm from '~/Components/Projects/ProjectsForm'

export default {
  name: 'ProjectEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    ProjectsForm,
  },
  props: {
    type: String,
    propertyTypes: Object,
    cities: Array,
    mortgageTypes: Array,
  },
  data(props) {
    const title = 'Редактирование проекта: {{ name }}'
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
