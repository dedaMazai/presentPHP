<template>
  <list-page
    :initial-filters="{
      name: '',
      creation_period: [],
    }"
    :title="title"
    resource="projectTypes"
    resourceUrl="project-types"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <text-input label="Название" name="name"></text-input>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
    </template>
    <template #default>
      <cards-grid show="projects">
        <template #default="{ item }">
          <a-card-meta :title="item.name">
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Дата создание">
                  <a-typography-text strong>
                    {{ formatDateTime(item.created_at) }}
                  </a-typography-text>
                </a-descriptions-item>
                <a-descriptions-item label="Дата обновления">
                  <a-typography-text strong>
                    {{ formatDateTime(item.updated_at) }}
                  </a-typography-text>
                </a-descriptions-item>
              </a-descriptions>
            </template>
          </a-card-meta>
        </template>
      </cards-grid>
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
import ImageCell from '~/Components/DataGrid/Cells/ImageCell'
import ActionsCell from '~/Components/DataGrid/Cells/ActionsCell'
import AddButton from '~/Components/AddButton'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SwitchInput from '~/Components/Form/Inputs/SwitchInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import CardsGrid from '~/Components/CardsGrid/CardsGrid'
import { PUBLISHED, PUBLISHED_FILTER } from '~/constants/statuses'
import { formatDateTime } from '~/utils'

export default {
  name: 'ProjectTypesList',
  layout: BasicLayout,
  components: {
    TextCell,
    ReferenceCell,
    DateCell,
    StatusCell,
    SelectCell,
    ImageCell,
    ActionsCell,
    DataGrid,
    ListPage,
    AddButton,
    SimpleForm,
    TextInput,
    SwitchInput,
    SelectInput,
    DateRangeInput,
    CardsGrid,
  },
  data() {
    return {
      PUBLISHED,
      PUBLISHED_FILTER,
      formatDateTime,
      title: 'Типы проектов',
    }
  },
}
</script>

<style scoped></style>
