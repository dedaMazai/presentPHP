<template>
  <create-page
    resource="bank-info"
    title="Создание записи о банке"
    :breadcrumb="breadcrumb"
  >
    <template #default>
      <bank-info-form></bank-info-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import BankInfoForm from '~/Components/BankInfo/BankInfoForm'

export default {
  name: 'BankInfoCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    BankInfoForm,
  },
  data() {
    return {
      breadcrumb: [
        { path: this.route('bank-info'), breadcrumbName: 'Список банков' },
        { breadcrumbName: 'Создание записи о банке' },
      ],
    }
  },
}
</script>

<style scoped></style>
