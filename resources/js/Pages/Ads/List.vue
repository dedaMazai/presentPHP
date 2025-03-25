<template>
  <list-page
    :initial-filters="{
      is_published: null,
      type: '',
      title: '',
      tag: '',
      creation_period: [],
      start_date: '',
      end_date: ''
    }"
    resource="ads"
    title="Список объявлений"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <text-input label="Заголовок" name="title"></text-input>
      <select-input
        :options="PUBLISHED_FILTER"
        label="Статус"
        name="is_published"
      ></select-input>
      <select-input
        label="Расположение"
        name="place"
        resource="places"
      ></select-input>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
      <date-input
        label="Дата начала показа в МП"
        name="start_date"
      ></date-input>
      <date-input
        label="Дата окончания показа в МП"
        name="end_date"
      ></date-input>
    </template>
    <template #default>
      <data-grid>
        <text-cell name="title" title="Заголовок"></text-cell>
        <select-cell
          :options="PUBLISHED"
          action-url="ads.update-status"
          name="is_published"
          title="Статус"
          sorter="true"
          width="160px"
        ></select-cell>
        <reference-cell
          name="place"
          resource="places"
          title="Расположение"
        ></reference-cell>
        <date-cell
          name="created_at"
          sorter="true"
          title="Дата создания"
          width="150px"
        ></date-cell>
        <date-cell
          name="updated_at"
          sorter="true"
          title="Дата обновления"
          width="170px"
        ></date-cell>
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
import StatusCell from '~/Components/DataGrid/Cells/StatusCell'
import SelectCell from '~/Components/DataGrid/Cells/SelectCell'
import ActionsCell from '~/Components/DataGrid/Cells/ActionsCell'
import AddButton from '~/Components/AddButton'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SwitchInput from '~/Components/Form/Inputs/SwitchInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import DateInput from '~/Components/Form/Inputs/DateInput'
import { PUBLISHED, PUBLISHED_FILTER } from '~/constants/statuses'

export default {
  name: 'AdsList',
  layout: BasicLayout,
  components: {
    TextCell,
    ReferenceCell,
    DateCell,
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
    DateInput,
  },
  props: {
    places: Array,
  },
  data() {
    return {
      PUBLISHED,
      PUBLISHED_FILTER,
    }
  },
}
</script>

<style scoped></style>
