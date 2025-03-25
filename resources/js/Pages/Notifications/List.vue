<template>
  <list-page
    :initial-filters="{
      type: '',
      destination_type: '',
      creation_period: [],
    }"
    resource="notifications"
    title="Список уведомлений"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <select-input label="Тип" name="type" resource="types"></select-input>
      <select-input
        label="Аудитория"
        name="destination_type"
        resource="destinationTypes"
        ellipsis
        label-width="180px"
      ></select-input>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
    </template>
    <template #default>
      <data-grid>
        <text-cell name="title" title="Заголовок"></text-cell>
        <reference-cell
          name="type"
          resource="types"
          title="Тип"
        ></reference-cell>
        <reference-cell
          name="destination_type"
          resource="destinationTypes"
          title="Аудитория"
          ellipsis
          width="230px"
        ></reference-cell>
        <date-cell
          name="created_at"
          sorter="true"
          title="Дата создания"
          width="150px"
        ></date-cell>
        <actions-cell
          name="actions"
          title="Действия"
          width="210px"
          show
          show-only
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
import ActionsCell from '~/Components/DataGrid/Cells/ActionsCell'
import AddButton from '~/Components/AddButton'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'

export default {
  name: 'NotificationsList',
  layout: BasicLayout,
  components: {
    TextCell,
    ReferenceCell,
    DateCell,
    ActionsCell,
    DataGrid,
    ListPage,
    AddButton,
    TextInput,
    SelectInput,
    DateRangeInput,
  },
  props: {
    types: Array,
    destinationTypes: Array,
  },
}
</script>

<style scoped></style>
