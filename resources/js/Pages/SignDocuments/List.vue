<template>
  <list-page
    :initial-filters="{
      name: '',
      code: '',
    }"
    :title="title"
    resource="sign-documents"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <text-input label="Наименование инструкции" name="name"></text-input>
      <text-input label="Код" name="code"></text-input>
    </template>
    <template #default>
      <cards-grid>
        <template #default="{ item }">
          <a-card-meta :title="item.name">
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Код">
                  <a-typography-text strong>
                    {{ item.code }}
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

export default {
  name: 'SignDocumentsList',
  layout: BasicLayout,
  components: {
    ListPage,
    CardsGrid,
    SelectCell,
    AddButton,
    TextInput,
  },
  data() {
    return {
      title: 'Инструкции по приложению ЭП',
    }
  },
}
</script>

<style scoped></style>
