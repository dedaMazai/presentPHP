<template>
  <list-page
    :breadcrumb="breadcrumb"
    :initial-filters="{
      creation_period: [],
    }"
    :title="title"
    :extra-params="[type.id, project.id]"
    resource="mortgagePrograms"
    resource-url="projects.mortgage-programs"
  >
    <template #actions>
      <add-button :extra-params="[type.id, project.id]"></add-button>
    </template>
    <template #filters>
      <date-range-input
        label="Дата создания"
        name="creation_period"
      ></date-range-input>
    </template>
    <template #default>
      <cards-grid :extra-params="[type.id, project.id]">
        <template #default="{ item }">
          <a-card-meta :title="item.bank_info.title">
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
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import {formatDateTime} from '~/utils'

export default {
  name: 'MortgageProgramsList',
  layout: BasicLayout,
  components: {
    SelectCell,
    ListPage,
    AddButton,
    SelectInput,
    DateRangeInput,
    CardsGrid,
  },
  props: {
    types: Array,
    type: Object,
    project: Object,
  },
  data(props) {
    const title = 'Список предложений банков'
    return {
      formatDateTime,
      title,
      breadcrumb: [
        {
          path: this.route('project-types'),
          breadcrumbName: 'Типы проектов',
        },
        {
          path: this.route('project-types.edit', [props.type?.id]),
          breadcrumbName: props.type?.name,
        },
        {
          path: this.route('projects', [props.type?.id]),
          breadcrumbName: 'Список проектов',
        },
        {
          path: this.route('projects.edit', [
            props.type?.id,
            props.project?.id,
          ]),
          breadcrumbName: props.project?.name,
        },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
