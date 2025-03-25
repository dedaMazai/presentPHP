<template>
  <edit-page
    resource="news"
    :title="title"
    :breadcrumb="breadcrumb"
  >
    <template #default>
      <news-form
        :initial-values="news"
        :destinations="destinations"
        :ukProjects="ukProjects"
        :ukBuildings="ukBuildings"
        :isSent="isSent"
      ></news-form>
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
import NewsForm from '~/Components/News/NewsForm'
import ContentItemsEditor from '~/Components/ContentItems/ContentItemsEditor'

export default {
  name: 'NewsEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    NewsForm,
    ContentItemsEditor,
  },
  props: {
    news: Object,
    contentItems: Array,
    destinations: Array,
    ukProjects: Array,
    ukBuildings: Array,
    isSent: Boolean,
  },
  data() {
    const title = 'Редактирование новости: {{ title }}'
    return {
      title,
      breadcrumb: [
        {path: this.route('news'), breadcrumbName: 'Список новостей'},
        {breadcrumbName: title},
      ],
    }
  },
  methods: {
    onSort(order) {
      this.$inertia.put(this.route('news.content-items.sort', this.news.id), {
        order,
      })
    },
    onDelete(id) {
      this.$inertia.delete(
        this.route('news.content-items.destroy', [this.news.id, id])
      )
    },
    onCreate(type, data) {
      const route = this.route('news.content-items.store', [this.news.id, type])
      this.$inertia.post(route, data)
    },
    onUpdate(id, data) {
      const route = this.route('news.content-items.update', [this.news.id, id])
      this.$inertia.put(route, data)
    },
  },
}
</script>

<style scoped></style>
