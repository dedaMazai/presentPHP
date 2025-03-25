<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="escrow-banks"
    resource-url="escrow-banks"
  >
    <template #default>
      <escrow-banks-form
        :initial-values="groupingRealityTypes"
        :banks="banks"
      ></escrow-banks-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import EscrowBanksForm from '~/Components/EscrowBanks/EscrowBanksForm'

export default {
  name: 'EscrowBanksEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    EscrowBanksForm,
  },
  props: {
    banks: Array,
  },
  data() {
    const title = 'Редактирование Банка эскроу : {{ escrow_bank_id }}'
    return {
      title,
      breadcrumb: [
        {
          path: this.route('escrow-banks'),
          breadcrumbName: 'Банки эскроу',
        },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
