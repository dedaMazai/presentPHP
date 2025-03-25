<template>
  <create-page
    resource="notifications"
    title="Создание уведомления"
    :breadcrumb="breadcrumb"
  >
    <template #default>
      <notification-form
        :action-types="actionTypes"
        :action-types-payload-map="actionTypesPayloadMap"
        :destination-types="destinationTypesArr"
        :destination-types-payload-map="destinationTypesPayloadMap"
        :ukBuildings="ukBuildings"
        :ukClientRoleTypes="ukClientRoleTypes"
      ></notification-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import NotificationForm from '~/Components/Notifications/NotificationForm'

export default {
  name: 'NotificationsCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    NotificationForm,
  },
  props: {
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
        { path: this.route('notifications'), breadcrumbName: 'Список уведомлений', },
        { breadcrumbName: 'Создание уведомления' },
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
