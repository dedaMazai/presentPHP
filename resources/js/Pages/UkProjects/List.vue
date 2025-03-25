<template>
  <list-page
    :initial-filters="{
      is_published: null,
      name: '',
      creation_period: [],
    }"
    :title="title"
    resource="uk-projects"
  >
    <template #actions>
      <add-button></add-button>
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
      <cards-grid show="uk-projects.articles">
        <template #cover="{ item }">
          <img :alt="item.id" :src="item.image.url"/>
        </template>
        <template #default="{ item }">
          <a-card-meta :title="item.name">
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Статус">
                  <a-typography-text strong>
                    <select-cell
                      :options="PUBLISHED"
                      :record="item"
                      :value="item.is_published"
                      action-url="uk-projects.update-status"
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
import SelectCell from '~/Components/DataGrid/Cells/SelectCell'
import AddButton from '~/Components/AddButton'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import {PUBLISHED, PUBLISHED_FILTER} from '~/constants/statuses'
import {formatDateTime} from '~/utils'

export default {
  name: 'UkProjectsList',
  layout: BasicLayout,
  components: {
    SelectCell,
    CardsGrid,
    ListPage,
    AddButton,
    TextInput,
    SelectInput,
    DateRangeInput,
  },
  data() {
    return {
      PUBLISHED,
      PUBLISHED_FILTER,
      formatDateTime,
      title: 'Список проектов УК',
    }
  },
}
</script>

<style scoped></style>
