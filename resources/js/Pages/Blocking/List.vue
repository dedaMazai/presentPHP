<template>
  <list-page
    :initial-filters="{
      id: '',
    }"
    resource="blocking"
    title="Список блокировок"
  >
    <template #default>
      <data-grid>
        <text-cell
          name="ip_address"
          title="Ip"
          resource="blocking"
        ></text-cell>
        <text-cell
          name="attempts"
          title="Количество попыток"
          resource="blocking"
        ></text-cell>
        <text-cell
          name="block_time"
          title="Дата блокировки"
          resource="blocking"
        ></text-cell>
        <text-cell
          name="unlock_time"
          title="Дата разблокировки"
          resource="blocking"
        ></text-cell>
        <actions-cell
          name="actions"
          title="Действия"
          width="210px"
        ></actions-cell>
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
import { PUBLISHED, PUBLISHED_FILTER } from '~/constants/statuses'

export default {
  name: 'BlockingList',
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
  data() {
    return {
      PUBLISHED,
      PUBLISHED_FILTER,
    }
  },
  props: {
    blocking: Array,
  },
}
</script>

<style scoped></style>
