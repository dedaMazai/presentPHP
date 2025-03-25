<template>
  <list-page
    :breadcrumb="breadcrumb"
    :extra-params="[place]"
    :initial-filters="{
      is_published: null,
      type: '',
      title: '',
      tag: '',
      creation_period: [],
      start_date: '',
      end_date: ''
    }"
    :title="title"
    resource="banners"
  >
    <template #actions>
      <add-button :extra-params="[place]"></add-button>
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
      <date-input
        label="Дата начала показа в МП"
        name="start_date"
      ></date-input>
      <date-input
        label="Дата окончания показа в МП"
        name="end_date"
      ></date-input>
    </template>
    <template #default>
      <cards-grid :extra-params="[place]">
        <template #cover="{ item }">
          <img :alt="item.id" :src="item.image.url"/>
        </template>
        <template #default="{ item }">
          <a-card-meta>
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Порядковый номер"
                >
                  <a-typography-text strong>{{
                      item.order
                    }}
                  </a-typography-text>
                </a-descriptions-item
                >
                <a-descriptions-item label="Статус"
                >
                  <a-typography-text strong
                  >
                    <select-cell
                      :extra-params="[place]"
                      :options="PUBLISHED"
                      :record="item"
                      :value="item.is_published"
                      action-url="banners.update-status"
                      name="is_published"
                    ></select-cell>
                  </a-typography-text
                  >
                </a-descriptions-item>
                <a-descriptions-item v-if="item.news_id" label="Новость"
                >
                  <a-typography-text strong>
                    {{ item.news.title }}
                  </a-typography-text
                  >
                </a-descriptions-item>
                <a-descriptions-item v-if="item.url" label="Внешняя ссылка"
                >
                  <a-typography-text strong>{{
                      item.url
                    }}
                  </a-typography-text>
                </a-descriptions-item
                >
                <a-descriptions-item label="Дата создание"
                >
                  <a-typography-text strong>
                    {{ formatDateTime(item.created_at) }}
                  </a-typography-text
                  >
                </a-descriptions-item>
                <a-descriptions-item label="Дата обновления"
                >
                  <a-typography-text strong>
                    {{ formatDateTime(item.updated_at) }}
                  </a-typography-text
                  >
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
import DateInput from '~/Components/Form/Inputs/DateInput'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import CardsGrid from '~/Components/CardsGrid/CardsGrid'
import {PUBLISHED, PUBLISHED_FILTER} from '~/constants/statuses'
import {formatDateTime} from '~/utils'

export default {
  name: 'NewsList',
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
    DateInput,
    DateRangeInput,
    CardsGrid,
  },
  props: {
    types: Array,
    place: String,
    places: String,
  },
  data(props) {
    const title = `Список баннеров: "${props.places[props.place]}"`
    return {
      PUBLISHED,
      PUBLISHED_FILTER,
      formatDateTime,
      title: title,
      breadcrumb: [
        {
          path: this.route('banners.places'),
          breadcrumbName: 'Расположения баннеров',
        },
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
