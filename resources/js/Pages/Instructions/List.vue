<template>
  <list-page
    :breadcrumb="breadcrumb"
    :initial-filters="{
      is_published: null,
      title: '',
      creation_period: [],
    }"
    :title="title"
    resource="instructions"
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
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
    </template>
    <template #default>
      <cards-grid>
        <template #cover="{ item }">
          <img :alt="item.id" :src="item.image.url" />
        </template>
        <template #default="{ item }">
          <a-card-meta>
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Порядковый номер">
                  <a-typography-text strong
                    >{{ item.order }}
                  </a-typography-text>
                </a-descriptions-item>
                <a-descriptions-item label="Статус">
                  <a-typography-text strong>
                    <select-cell
                      :options="PUBLISHED"
                      :record="item"
                      :value="item.is_published"
                      action-url="instructions.update-status"
                      name="is_published"
                    ></select-cell>
                  </a-typography-text>
                </a-descriptions-item>
                <a-descriptions-item label="Заголовок">
                  <a-typography-text strong>
                    {{ item.title }}
                  </a-typography-text>
                </a-descriptions-item>
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
import SelectCell from '~/Components/DataGrid/Cells/SelectCell'
import AddButton from '~/Components/AddButton'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import CardsGrid from '~/Components/CardsGrid/CardsGrid'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import { PUBLISHED, PUBLISHED_FILTER } from '~/constants/statuses'
import { formatDateTime } from '~/utils'

export default {
  name: 'InstructionsList',
  layout: BasicLayout,
  components: {
    SelectCell,
    ListPage,
    AddButton,
    DateRangeInput,
    CardsGrid,
    SelectInput,
  },
  data() {
    const title = 'Список инструкций'
    return {
      PUBLISHED,
      PUBLISHED_FILTER,
      formatDateTime,
      title: title,
      breadcrumb: [{ breadcrumbName: title }],
    }
  },
}
</script>

<style scoped></style>
