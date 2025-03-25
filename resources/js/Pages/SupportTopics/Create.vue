<template>
  <create-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="support-topics"
  >
    <template #default>
      <support-topics-form></support-topics-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import SupportTopicsForm from '~/Components/SupportTopics/SupportTopicsForm'

export default {
  name: 'SupportTopicsCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    SupportTopicsForm,
  },
  props: {},
  data() {
    const title = `Создание тематики обращений`
    return {
      title,
      breadcrumb: [
        {
          path: this.route('support-topics'),
          breadcrumbName: 'Список тематик обращений',
        },
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
