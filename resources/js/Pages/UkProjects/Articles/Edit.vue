<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :extra-params="[ukProject.id]"
    :title="title"
    resource="article"
    resource-url="uk-projects.articles"
  >
    <template #default>
      <uk-articles-form></uk-articles-form>
    </template>
  </edit-page>
  <content-items-editor
    :content-items="contentItems"
    @create="onCreate"
    @delete="onDelete"
    @sort="onSort"
    @update="onUpdate"
  />
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import UkArticlesForm from '~/Components/UkProjects/UkArticlesForm'
import ContentItemsEditor from '~/Components/ContentItems/ContentItemsEditor'

export default {
  name: 'UkProjectArticleEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    UkArticlesForm,
    ContentItemsEditor,
  },
  props: {
    ukProject: Object,
    article: Object,
    contentItems: Array,
  },
  data(props) {
    const title = 'Редактирование статьи: {{ title }}'
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
          path: this.route('uk-projects.articles', [props.ukProject?.id]),
          breadcrumbName: 'Список статей',
        },
        {breadcrumbName: title},
      ],
    }
  },
  methods: {
    onSort(order) {
      this.$inertia.put(
        this.route('uk-projects.articles.content-items.sort', [
          this.ukProject?.id,
          this.article?.id,
        ]),
        {
          order,
        }
      )
    },
    onDelete(id) {
      this.$inertia.delete(
        this.route('uk-projects.articles.content-items.destroy', [
          this.ukProject?.id,
          this.article?.id,
          id,
        ])
      )
    },
    onCreate(type, data) {
      const route = this.route('uk-projects.articles.content-items.store', [
        this.ukProject?.id,
        this.article?.id,
        type,
      ])
      this.$inertia.post(route, data)
    },
    onUpdate(id, data) {
      const route = this.route('uk-projects.articles.content-items.update', [
        this.ukProject?.id,
        this.article?.id,
        id,
      ])
      this.$inertia.put(route, data)
    },
  },
}
</script>

<style scoped></style>
