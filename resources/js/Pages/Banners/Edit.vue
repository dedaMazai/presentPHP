<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :extra-params="[place]"
    :title="title"
    resource="banner"
    resource-url="banners"
  >
    <template #default>
      <banner-form :place="place"></banner-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import BannerForm from '~/Components/Banners/BannerForm'

export default {
  name: 'BannerEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    BannerForm,
  },
  props: {
    place: String,
    places: Object,
  },
  data(props) {
    const title = 'Редактирование баннера: #{{ id }}'
    return {
      title,
      breadcrumb: [
        {
          path: this.route('banners.places'),
          breadcrumbName: 'Расположения баннеров',
        },
        {
          path: this.route('banners', [props.place]),
          breadcrumbName: `Список баннеров: "${props.places[props.place]}"`,
        },
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
