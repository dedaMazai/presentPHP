<template>
  <list-page
    :initial-filters="{
      escrow_bank_id: '',
      letterofbank_ids: [],
    }"
    :title="title"
    resource="escrow-banks"
  >
    <template #actions>
      <add-button></add-button>
    </template>
    <template #filters>
      <select-input
        label="Эскроу банк"
        name="escrow_bank_id"
        :options="banks"
        label-field-id="name"
        value-field-id="id"
      ></select-input>
    </template>
    <template #default>
      <cards-grid>
        <template #default="{ item }">
          <a-card-meta :title="bankName(item.escrow_bank_id)">
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Банки для открытия аккредитива">
                  <a-typography-text strong>
                    {{ item.letterofbank_ids }}
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
import isArray from 'lodash/isArray'
import filter from "lodash/filter";
import includes from "lodash/includes";

export default {
  name: 'EscrowBanksList',
  layout: BasicLayout,
  components: {
    ListPage,
    CardsGrid,
    SelectCell,
    AddButton,
    TextInput,
    SelectInput,
  },
  props: {
    banks: Array,
  },
  data() {
    return {
      title: 'Банки эскроу',
    }
  },
  methods: {
    bankName(bank_id) {
      return filter(this.banks, {id: bank_id})[0].name;
    },
  }
}
</script>

<style scoped></style>
