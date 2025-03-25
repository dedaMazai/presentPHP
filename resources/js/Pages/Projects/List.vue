<template>
  <list-page
    :breadcrumb="breadcrumb"
    :initial-filters="{
      is_published: null,
      name: '',
      creation_period: [],
    }"
    :title="title"
    :extra-params="[type.id]"
    resource="projects"
  >
    <template #actions>
      <add-button :extra-params="[type.id]"></add-button>
    </template>
    <template #filters>
      <select-input
        :options="PUBLISHED_FILTER"
        label="Статус"
        name="is_published"
      ></select-input>
      <text-input label="Название" name="name"></text-input>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
    </template>
    <template #default>
      <cards-grid :extra-params="[type.id]" show="projects.articles">
        <template #cover="{ item }">
          <img :alt="item.id" :src="item.main_image.url"/>
        </template>
        <template #default="{ item }">
          <a-card-meta :title="item.name">
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Статус">
                  <a-typography-text strong>
                    <select-cell
                      :extra-params="[type.id]"
                      :options="PUBLISHED"
                      :record="item"
                      :value="item.is_published"
                      action-url="projects.update-status"
                      name="is_published"
                    ></select-cell>
                  </a-typography-text>
                </a-descriptions-item>
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
import CardsGrid from '~/Components/CardsGrid/CardsGrid'
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
import { PUBLISHED, PUBLISHED_FILTER } from '~/constants/statuses'
import {formatDateTime} from '~/utils'

export default {
  name: 'ProjectsList',
  layout: BasicLayout,
  components: {
    TextCell,
    ReferenceCell,
    DateCell,
    StatusCell,
    SelectCell,
    ActionsCell,
    DataGrid,
    CardsGrid,
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
    type: String,
  },
  data(props) {
    const title = `Список проектов`
    return {
      PUBLISHED,
      PUBLISHED_FILTER,
      formatDateTime,
      title: title,
      breadcrumb: [
        {
          path: this.route('project-types'),
          breadcrumbName: 'Типы проектов',
        },
        {
          path: this.route('project-types.edit', [props.type?.id]),
          breadcrumbName: props.type?.name,
        },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
