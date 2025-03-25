<template>
  <list-page
    :initial-filters="{
      name: '',
      bank_id: '',
    }"
    :title="title"
    resource="banks"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <text-input label="Наименование" name="name"></text-input>
      <text-input label="CRM ID" name="bank_id"></text-input>
    </template>
    <template #default>
      <cards-grid>
        <template #cover="{ item }">
          <img :alt="item.id" :src="item.image.url"/>
        </template>
        <template #default="{ item }">
          <a-card-meta :title="item.name">
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="CRM ID">
                  <a-typography-text strong>
                    {{ item.bank_id }}
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
  name: 'BanksList',
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
      title: 'Справочник банков',
    }
  },
}
</script>

<style scoped></style>
