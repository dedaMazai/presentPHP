<template>
  <create-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="escrow-banks"
  >
    <template #default>
      <escrow-banks-form
        :banks="banks"
      ></escrow-banks-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import EscrowBanksForm from '~/Components/EscrowBanks/EscrowBanksForm'

export default {
  name: 'EscrowBanksCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    EscrowBanksForm,
  },
  props: {
    banks: Array,
  },
  data() {
    const title = `Создание Банка эскроу`
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
