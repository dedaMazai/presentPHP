<template>
  <edit-page
    resource="ad"
    resource-url="ads"
    :title="title"
    :breadcrumb="breadcrumb"
  >
    <template #default>
      <ads-form></ads-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import AdsForm from '~/Components/Ads/AdsForm'

export default {
  name: 'AdsEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    AdsForm,
  },
  data() {
    const title = 'Редактирование объявления: {{ title }}'
    return {
      title,
      breadcrumb: [
        { path: this.route('ads'), breadcrumbName: 'Список объявлений' },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
