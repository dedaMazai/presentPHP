<template>
  <list-page
    :breadcrumb="breadcrumb"
    :extra-params="[ukProject.id]"
    :initial-filters="{
      is_published: null,
      title: '',
      creation_period: [],
    }"
    :title="title"
    resource="articles"
    resource-url="uk-projects.articles"
  >
    <template #actions>
      <add-button :extra-params="[ukProject.id]"></add-button>
    </template>
    <template #filters>
      <select-input
        :options="PUBLISHED_FILTER"
        label="Статус"
        name="is_published"
      ></select-input>
      <text-input label="Заголовок" name="title"></text-input>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
    </template>
    <template #default>
      <cards-grid :extra-params="[ukProject.id]">
        <template #default="{ item }">
          <a-card-meta :title="item.title">
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Статус">
                  <a-typography-text strong>
                    <select-cell
                      :extra-params="[ukProject.id]"
                      :options="PUBLISHED"
                      :record="item"
                      :value="item.is_published"
                      action-url="uk-projects.articles.update-status"
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
  name: 'UkArticlesList',
  layout: BasicLayout,
  components: {
    ListPage,
    AddButton,
    SelectCell,
    TextInput,
    SelectInput,
    DateRangeInput,
    CardsGrid,
  },
  props: {
    ukProject: Object,
  },
  data(props) {
    const title = 'Список статей'
    return {
      formatDateTime,
      PUBLISHED,
      PUBLISHED_FILTER,
      title,
      breadcrumb: [
        {
          path: this.route('uk-projects'),
          breadcrumbName: 'Список проектов УК',
        },
        {
          path: this.route('uk-projects.edit', [props.ukProject?.id]),
          breadcrumbName: props.ukProject?.name,
        },
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
