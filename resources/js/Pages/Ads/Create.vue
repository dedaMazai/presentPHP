<template>
  <create-page
    resource="ads"
    title="Создание объявления"
    :breadcrumb="breadcrumb"
  >
    <template #default>
      <ads-form></ads-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import AdsForm from '~/Components/Ads/AdsForm'

export default {
  name: 'AdsCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    AdsForm,
  },
  data() {
    return {
      breadcrumb: [
        { path: this.route('ads'), breadcrumbName: 'Список объявлений' },
        { breadcrumbName: 'Создание объявления' },
      ],
    }
  },
}
</script>

<style scoped></style>
