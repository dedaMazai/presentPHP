<template>
  <list-page
    :initial-filters="{
      account_number: '',
    }"
    resource="accounts"
    title="Лицевые счета"
  >
    <template #filters>
      <text-input label="Номер счета" name="account_number"></text-input>
    </template>
    <template #actions>
      <a-button type="primary" @click="updateAccounts"
      >Обновить</a-button
      >
    </template>
    <template #default>
      <data-grid>
        <text-cell name="account_number" title="Номер счета"></text-cell>
        <text-cell name="address" title="Адрес"></text-cell>
        <text-cell name="balance" title="Баланс счета"></text-cell>
        <text-cell name="not_paid_months" title="Количество месяцев"></text-cell>
        <text-cell name="users_id" title="Номер клиента"></text-cell>
        <text-cell name="is_meter_enter_period_active" title="Счетчики активные"></text-cell>
        <text-cell name="project_crm_1c_id" title="CRM id"></text-cell>
        <text-cell name="meter_enter_period_start" title="Период начала"></text-cell>
        <text-cell name="meter_enter_period_end" title="Период окончания"></text-cell>
      </data-grid>
    </template>
  </list-page>
</template>

<script>
import ListPage from '~/core/ListPage'
import BasicLayout from '~/Layouts/BasicLayout'
import DataGrid from '~/Components/DataGrid/DataGrid'
import TextCell from '~/Components/DataGrid/Cells/TextCell'
import ReferenceCell from '~/Components/DataGrid/Cells/ReferenceCell'
import DateCell from '~/Components/DataGrid/Cells/DateCell'
import DateOnlyCell from '~/Components/DataGrid/Cells/DateOnlyCell'
import StatusCell from '~/Components/DataGrid/Cells/StatusCell'
import SelectCell from '~/Components/DataGrid/Cells/SelectCell'
import ActionsCell from '~/Components/DataGrid/Cells/ActionsCell'
import AddButton from '~/Components/AddButton'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SwitchInput from '~/Components/Form/Inputs/SwitchInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'

export default {
  name: 'AccountList',
  layout: BasicLayout,
  components: {
    TextCell,
    ReferenceCell,
    DateCell,
    DateOnlyCell,
    StatusCell,
    SelectCell,
    ActionsCell,
    DataGrid,
    ListPage,
    AddButton,
    SimpleForm,
    TextInput,
    SwitchInput,
    SelectInput,
    DateRangeInput,
  },
  props: {
    accounts: Array,
  },
  methods: {
    updateAccounts() {
      this.$inertia.post(this.route('accounts.update'))
    },
  },
}
</script>

<style scoped></style>
