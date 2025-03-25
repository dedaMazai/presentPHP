<template>
  <page-wrapper :breadcrumb="breadcrumb" title="Просмотр уведомления">
    <a-card>
      <notification-form
        :initial-values="notification"
        :action-types="actionTypes"
        :action-types-payload-map="actionTypesPayloadMap"
        :destination-types="destinationTypesArr"
        :destination-types-payload-map="destinationTypesPayloadMap"
        :ukBuildings="ukBuildings"
        :ukClientRoleTypes="ukClientRoleTypes"
        show
      ></notification-form>
    </a-card>
  </page-wrapper>
</template>

<script>
import PageWrapper from '~/Components/PageWrapper'
import BasicLayout from '~/Layouts/BasicLayout'
import NotificationForm from '~/Components/Notifications/NotificationForm'

export default {
  name: 'NotificationsShow',
  layout: BasicLayout,
  components: {
    PageWrapper,
    NotificationForm,
  },
  props: {
    notification: Object,
    actionTypes: Array,
    actionTypesPayloadMap: Object,
    destinationTypes: Array,
    destinationTypesPayloadMap: Object,
    ukBuildings: Array,
    ukClientRoleTypes: Array,
  },
  data() {
    return {
      breadcrumb: [
        {
          path: this.route('notifications'),
          breadcrumbName: 'Список уведомлений',
        },
        { breadcrumbName: 'Просмотр уведомления' },
      ],
    }
  },
  computed: {
    destinationTypesArr() {
      return Object.values(this.destinationTypes)
    },
  },
}
</script>

<style scoped></style>
