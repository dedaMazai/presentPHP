<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :extra-params="[type.id, project.id]"
    :title="title"
    resource="article"
    resource-url="projects.articles"
  >
    <template #default>
      <articles-form></articles-form>
    </template>
  </edit-page>
  <content-items-editor
    :content-items="contentItems"
    @sort="onSort"
    @create="onCreate"
    @delete="onDelete"
    @update="onUpdate"/>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import ArticlesForm from '~/Components/Projects/ArticlesForm'
import ContentItemsEditor from '~/Components/ContentItems/ContentItemsEditor'

export default {
  name: 'ProjectArticleEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    ArticlesForm,
    ContentItemsEditor,
  },
  props: {
    type: Object,
    project: Object,
    article: Object,
    contentItems: Array,
  },
  data(props) {
    const title = 'Редактирование статьи: {{ title }}'
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
  methods: {
    onSort(order) {
      this.$inertia.put(
        this.route('projects.articles.content-items.sort', [
          this.type?.id,
          this.project?.id,
          this.article?.id,
        ]),
        {
          order,
        }
      )
    },
    onDelete(id) {
      this.$inertia.delete(
        this.route('projects.articles.content-items.destroy', [
          this.type?.id,
          this.project?.id,
          this.article?.id,
          id,
        ])
      )
    },
    onCreate(type, data) {
      const route = this.route('projects.articles.content-items.store', [
        this.type?.id,
        this.project?.id,
        this.article?.id,
        type,
      ])
      this.$inertia.post(route, data)
    },
    onUpdate(id, data) {
      const route = this.route('projects.articles.content-items.update', [
        this.type?.id,
        this.project?.id,
        this.article?.id,
        id,
      ])
      this.$inertia.put(route, data)
    },
  },
}
</script>

<style scoped></style>
