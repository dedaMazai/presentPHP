<template>
  <list-page
    :initial-filters="{
      reality_name: '',
      reality_id: '',
      creation_period: [],
    }"
    :title="title"
    resource="reality-types"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <text-input label="Наименование типа недвижимости" name="reality_name"></text-input>
      <text-input label="Код типа недвижимости" name="reality_id"></text-input>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
    </template>
    <template #default>
      <cards-grid show="uk-projects.articles">
        <template #default="{ item }">
          <a-card-meta :title="item.reality_name">
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
import CardsGrid from '~/Components/CardsGrid/CardsGrid'
import SelectCell from '~/Components/DataGrid/Cells/SelectCell'
import AddButton from '~/Components/AddButton'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import {formatDateTime} from '~/utils'

export default {
  name: 'RealityTypesList',
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
      formatDateTime,
      title: 'Типы недвижимости',
    }
  },
}
</script>

<style scoped></style>
