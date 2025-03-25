<template>
  <list-page
    :breadcrumb="breadcrumb"
    :extra-params="[ukProject.id]"
    :initial-filters="{
      build_name: '',
      build_zid: '',
      creation_period: [],
    }"
    :title="title"
    resource="buildings"
    resource-url="uk-projects.buildings"
  >
    <template #actions>
      <add-button :extra-params="[ukProject.id]"></add-button>
    </template>
    <template #filters>
      <text-input label="Наименование корпуса" name="build_name"></text-input>
      <text-input label="Идентификатор корпуса" name="build_zid"></text-input>
      <date-range-input label="Дата создания" name="creation_period"></date-range-input>
    </template>
    <template #default>
      <cards-grid :extra-params="[ukProject.id]">
        <template #default="{ item }">
          <a-card-meta>
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Наименование корпуса">
                  <a-typography-text strong>
                    {{ item.build_name }}
                  </a-typography-text>
                </a-descriptions-item>
                <a-descriptions-item label="Идентификатор корпуса">
                  <a-typography-text strong>
                    {{ item.build_zid }}
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
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import {formatDateTime} from '~/utils'

export default {
  name: 'UkBuildingsList',
  layout: BasicLayout,
  components: {
    ListPage,
    AddButton,
    SelectCell,
    TextInput,
    DateRangeInput,
    CardsGrid,
  },
  props: {
    ukProject: Object,
  },
  data(props) {
    const title = 'Список корпусов'
    return {
      formatDateTime,
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
