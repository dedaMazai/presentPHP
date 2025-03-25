<template>
  <edit-page
    resource="blocking"
    :title="title"
    :breadcrumb="breadcrumb"
    edit-only
  >
    <template #default>
      <blocking-form></blocking-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import BlockingForm from '~/Components/Blocking/BlockingForm'

export default {
  name: 'BlockingEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    BlockingForm,
  },
  props: {
    user: Object,
  },
  data() {
    const title = 'Информация о блокировке'
    return {
      title,
      breadcrumb: [
        {path: this.route('blocking'), breadcrumbName: 'Список блокировок'},
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
