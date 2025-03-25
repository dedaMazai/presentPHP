<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="support-topics"
    resource-url="support-topics"
  >
    <template #default>
      <support-topics-form></support-topics-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import SupportTopicsForm from '~/Components/SupportTopics/SupportTopicsForm'

export default {
  name: 'SupportTopicsEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    SupportTopicsForm,
  },
  data() {
    const title = 'Редактирование тематики обращения: {{ name }}'
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
