<template>
  <create-page
    :breadcrumb="breadcrumb"
    :extra-params="[type.id, project.id]"
    :title="title"
    resource="projects.articles"
  >
    <template #default>
      <articles-form></articles-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import ArticlesForm from '~/Components/Projects/ArticlesForm'

export default {
  name: 'ProjectArticleCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    ArticlesForm,
  },
  props: {
    types: Array,
    type: Object,
    project: Object,
  },
  data(props) {
    const title = 'Создание статьи'
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
        {
          path: this.route('projects.edit', [
            props.type?.id,
            props.project?.id,
          ]),
          breadcrumbName: props.project?.name,
        },
        {
          path: this.route('projects.articles', [props.type?.id, props.project?.id]),
          breadcrumbName: 'Список статей',
        },
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
