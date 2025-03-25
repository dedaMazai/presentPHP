<template>
  <list-page
    :initial-filters="{
      name: '',
      is_published: null,
      creation_period: [],
      update_period: [],
    }"
    :title="title"
    resource="support-topics"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <text-input label="Название" name="name"></text-input>
      <select-input
        :options="PUBLISHED_FILTER"
        label="Статус"
        name="is_published"
      ></select-input>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
      <date-range-input
        label="Дата обновления"
        name="update_period"
      ></date-range-input>
    </template>
    <template #default>
      <cards-grid>
        <template #default="{ item }">
          <a-card-meta>
            <template #description>
              <div :class="'ant-card-meta-title'" :style="{'text-wrap': 'wrap'}">
                {{ item.name }}
              </div>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Статус">
                  <a-typography-text strong>
                    <select-cell
                      :options="PUBLISHED"
                      :record="item"
                      :value="item.is_published"
                      action-url="support-topics.update-status"
                      name="is_published"
                    ></select-cell>
                  </a-typography-text>
                </a-descriptions-item>
              </a-descriptions>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Дата создания">
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
import {PUBLISHED, PUBLISHED_FILTER} from '~/constants/statuses'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import {formatDateTime} from '~/utils'

export default {
  name: 'SupportTopicsList',
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
      title: 'Список тематик обращений',
      PUBLISHED,
      PUBLISHED_FILTER,
      formatDateTime,
    }
  },
}
</script>

<style scoped></style>
