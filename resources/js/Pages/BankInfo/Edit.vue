<template>
  <edit-page
    resource="bank-info"
    resource-url="bank-info"
    :title="title"
    :breadcrumb="breadcrumb"
  >
    <template #default>
      <bank-info-form></bank-info-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import BankInfoForm from '~/Components/BankInfo/BankInfoForm'

export default {
  name: 'BankInfoEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    BankInfoForm,
  },
  data() {
    const title = 'Редактирование записи о банке: {{ title }}'
    return {
      title,
      breadcrumb: [
        { path: this.route('bank-info'), breadcrumbName: 'Список банков' },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
