<template>
  <list-page
    :initial-filters="{
      is_published: null,
      type: '',
      title: '',
      tag: '',
      creation_period: [],
      // TODO
      //creation_period_from
    }"
    resource="news"
    title="Список новостей"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <text-input label="Заголовок" name="title"></text-input>
      <select-input label="Тип" name="type" resource="types"></select-input>
      <text-input label="Тэг" name="tag"></text-input>
      <select-input
        :options="PUBLISHED_FILTER"
        label="Статус"
        name="is_published"
      ></select-input>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
    </template>
    <template #default>
      <data-grid>
        <text-cell name="title" title="Заголовок"></text-cell>
        <select-cell
          :options="PUBLISHED"
          action-url="news.update-status"
          name="is_published"
          sorter="true"
          title="Статус"
          width="160px"
        ></select-cell>
        <reference-cell
          name="type"
          resource="types"
          title="Тип"
          width="160px"
        ></reference-cell>
        <text-cell name="count" title="Счетчик" width="160px"></text-cell>
        <text-cell name="tag" title="Тэг" width="160px"></text-cell>
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
      </data-grid
      >
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
import {PUBLISHED, PUBLISHED_FILTER} from '~/constants/statuses'

export default {
  name: 'NewsList',
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
  },
  props: {
    types: Array,
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
