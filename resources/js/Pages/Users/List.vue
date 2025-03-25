<template>
  <list-page
    :initial-filters="{
      is_published: null,
      type: '',
      title: '',
      tag: '',
      phone: '',
      ban: '',
      names: '',
      unlock_time: '',
      creation_period: [],
    }"
    resource="users"
    title="Список клиентов"
  >
    <template #actions>
      <add-button></add-button>
      <a :href="'users/export'" class="btn btn-primary">Выгрузить в excel</a>
    </template>
    <template #filters>
      <text-input label="ID" name="id"></text-input>
      <text-input label="ФИО" name="names"></text-input>
      <text-input label="CRM ID" name="crm_id"></text-input>
      <text-input label="Телефон" name="phone"></text-input>
      <select-input
        :options="BLOCK"
        labelInValue="TEST"
        label="Статус"
        name="ban"
      ></select-input>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
    </template>
    <template #default>
      <data-grid>
        <text-cell name="id" title="ID"></text-cell>
        <text-cell name="phone" title="Телефон"></text-cell>
        <text-cell name="status" title="Статус"></text-cell>
        <text-cell name="ban_status" title="Блокировка"></text-cell>
        <text-cell name="first_name" title="Имя"></text-cell>
        <text-cell name="last_name" title="Фамилия"></text-cell>
        <text-cell name="middle_name" title="Отчество"></text-cell>
        <date-only-cell name="birth_date" title="Дата рождения"></date-only-cell>
        <text-cell name="email" title="Email"></text-cell>
        <text-cell name="crm_id" title="CRM ID"></text-cell>
        <text-cell name="id_projects_uk" title="Проекты УК"></text-cell>
        <text-cell name="account_numbers" title="Лицевые счета"></text-cell>
        <date-cell
          name="created_at"
          sorter="true"
          title="Дата создания"
        ></date-cell>
        <actions-cell
          name="actions"
          title="Действия"
          width="210px"
          edit-only
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
import { PUBLISHED, PUBLISHED_FILTER, BLOCK } from '~/constants/statuses'

export default {
  name: 'UsersList',
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
    places: Array,
  },
  data() {
    return {
      PUBLISHED,
      PUBLISHED_FILTER,
      BLOCK,
    }
  },
  methods: {
    exportUsers() {
      this.$inertia.get(this.route('users.export'))
    },
  },
}
</script>

<style scoped></style>
