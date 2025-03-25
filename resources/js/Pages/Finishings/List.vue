<template>
  <list-page
    :initial-filters="{
      is_published: null,
      finishing_id: '',
      description: '',
    }"
    :title="title"
    resource="finishings"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <text-input label="CRM ID" name="finishing_id"></text-input>
      <text-input label="Описание" name="description"></text-input>
      <select-input
        :options="PUBLISHED_FILTER"
        label="Статус"
        name="is_published"
      ></select-input>
    </template>
    <template #default>
      <cards-grid>
        <template #cover="{ item }">
          <img :alt="item.id" :src="item.images[0] && item.images[0].url ? item.images[0].url : ''"/>
        </template>
        <template #default="{ item }">
          <a-card-meta :title="item.finishing_id">
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Статус">
                  <a-typography-text strong>
                    <select-cell
                      :options="PUBLISHED"
                      :record="item"
                      :value="item.is_published"
                      action-url="finishings.update-status"
                      name="is_published"
                    ></select-cell>
                  </a-typography-text>
                </a-descriptions-item>
                <a-descriptions-item label="Описание">
                  <a-typography-text strong>
                    {{ item.description }}
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

export default {
  name: 'FinishingsList',
  layout: BasicLayout,
  components: {
    SelectCell,
    CardsGrid,
    ListPage,
    AddButton,
    TextInput,
    SelectInput,
  },
  data() {
    return {
      title: 'Варианты отделки',
      PUBLISHED,
      PUBLISHED_FILTER,
    }
  },
}
</script>

<style scoped></style>
