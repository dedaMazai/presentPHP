<template>
  <list-page
    :initial-filters="{
      name: '',
    }"
    :title="title"
    resource="documents-name"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <text-input label="Код документа в CRM" name="code"></text-input>
      <text-input label="Наименование документа" name="name"></text-input>
      <text-input label="Описание" name="description"></text-input>
      <number-input
        label="Код типа объекта"
        name="object_type_code"
        precision="0"
      ></number-input>
    </template>
    <template #default>
      <data-grid>
        <text-cell name="name" title="Код документа в CRM"></text-cell>
        <text-cell name="code" title="Код документа в CRM"></text-cell>
        <text-cell name="description" title="Код документа в CRM"></text-cell>
        <text-cell name="object_type_code" title="Код документа в CRM"></text-cell>
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
import SelectCell from '~/Components/DataGrid/Cells/SelectCell'
import AddButton from '~/Components/AddButton'
import TextInput from '~/Components/Form/Inputs/TextInput'
import NumberInput from '~/Components/Form/Inputs/NumberInput'
import TextCell from '~/Components/DataGrid/Cells/TextCell'
import ActionsCell from '~/Components/DataGrid/Cells/ActionsCell'

export default {
  name: 'DocumentsNameList',
  layout: BasicLayout,
  components: {
    ListPage,
    DataGrid,
    SelectCell,
    AddButton,
    TextInput,
    NumberInput,
    ActionsCell,
    TextCell
  },
  data() {
    return {
      title: 'Наименования документов сделки',
    }
  },
}
</script>

<style scoped></style>
