<template>
  <create-page
    resource="news"
    title="Создание новости"
    :breadcrumb="breadcrumb"
  >
    <template #default>
      <news-form
        :initial-values="newsCopy"
        :destinations="destinations"
        :ukProjects="ukProjects"
        :ukBuildings="ukBuildings"
      />
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import NewsForm from '~/Components/News/NewsForm'

export default {
  name: 'NewsCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    NewsForm,
  },
  props: {
    destinations: Array,
    ukProjects: Array,
    ukBuildings: Array,
    newsCopy: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      breadcrumb: [
        { path: this.route('news'), breadcrumbName: 'Список новостей' },
        { breadcrumbName: 'Создание новости' },
      ],
    }
  },
}
</script>
