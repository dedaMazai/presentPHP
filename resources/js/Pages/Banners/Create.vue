<template>
  <create-page
    :breadcrumb="breadcrumb"
    :extra-params="[place]"
    resource="banners"
    title="Создание баннера"
  >
    <template #default>
      <banner-form :place="place"></banner-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import BannerForm from '~/Components/Banners/BannerForm'

export default {
  name: 'BannerCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    BannerForm,
  },
  props: {
    place: String,
    places: Object,
  },
  data(props) {
    return {
      breadcrumb: [
        {
          path: this.route('banners.places'),
          breadcrumbName: 'Расположения баннеров',
        },
        {
          path: this.route('banners', [props.place]),
          breadcrumbName: `Список баннеров: "${props.places[props.place]}"`,
        },
        {breadcrumbName: 'Создание баннера'},
      ],
    }
  },
}
</script>

<style scoped></style>
