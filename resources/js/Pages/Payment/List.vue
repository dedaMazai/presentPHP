<template>
  <list-page
    :initial-filters="{
      user_id: '',
      account_number: '',
      title: '',
      creation_period: [],
    }"
    resource="payments"
    title="Оплаты"
  >
    <template #filters>
      <text-input label="Номер клиента" name="user_id"></text-input>
      <text-input label="Номер лицевого счета" name="account_number"></text-input>
      <text-input label="Назначение платежа" name="title"></text-input>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>

    </template>

    <template #actions>
      <a :href="'payments/export'" class="btn btn-primary">Выгрузить в excel</a>
    </template>

    <template #default>
      <data-grid>
        <date-cell
          name="created_at"
          sorter="true"
          title="Дата"
        ></date-cell>
        <text-cell name="account_number" title="Номер лицевого счета"></text-cell>
        <text-cell name="user_id" title="Номер клиента"></text-cell>
        <text-cell name="amount" title="Сумма"></text-cell>
        <text-cell name="status" title="Статус"></text-cell>
        <text-cell name="title" title="Назначение платежа"></text-cell>
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
  name: 'PaymentList',
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
    payments: Array,
  },
  methods: {
    exportTransactions() {
      this.$inertia.post(this.route('payments.export'))
    },
  },

}
</script>

<style scoped></style>
