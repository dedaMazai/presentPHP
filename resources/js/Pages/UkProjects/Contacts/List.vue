<template>
  <list-page
    :breadcrumb="breadcrumb"
    :extra-params="[ukProject.id]"
    resource="contacts"
    resource-url="uk-projects.contacts"
    :title="title"
  >
    <template #actions>
      <add-button :extra-params="[ukProject.id]"></add-button>
    </template>
    <template #default>
      <cards-grid :extra-params="[ukProject.id]">
        <template #default="{ item }">
          <a-card-meta :title="item.title">
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Тип">
                  <a-typography-text strong>
                    {{ typeLabel(item.type) }}
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
import AddButton from '~/Components/AddButton'
import { formatDateTime } from '~/utils'

export default {
  name: 'UkProjectsContactsList',
  layout: BasicLayout,
  components: {
    CardsGrid,
    ListPage,
    AddButton,
  },
  props: {
    ukProject: Object,
  },
  data(props) {
    const title = 'Список контактов'
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
        { breadcrumbName: title },
      ],
    }
  },
  methods: {
    typeLabel(type) {
      switch (type) {
        case 'map':
          return 'Карта'
        case 'email':
          return 'Email'
        case 'phone':
          return 'Телефон'
      }
    },
  },
}
</script>

<style scoped></style>
