<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
  >
    <template #default="{ model }">
      <select-input
        label="Эскроу банк"
        name="escrow_bank_id"
        :options="banks"
        label-field-id="name"
        value-field-id="id"
      ></select-input>
      <select-input
        label="Банки для открытия аккредитива"
        name="letterofbank_ids"
        mode="multiple"
        :options="banks"
        label-field-id="name"
        value-field-id="id"
      ></select-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import SelectInput from '~/Components/Form/Inputs/SelectInput'

const defaultValues = {
  escrow_bank_id: null,
  letterofbank_ids: [],
}

export default defineComponent({
  name: 'EscrowBanksForm',
  components: {
    SimpleForm,
    SelectInput,
  },
  props: {
    initialValues: Object,
    banks: Array,
  },
  data() {
    return {
      values: {...defaultValues, ...this.initialValues},
      rules: {
        escrow_bank_id: [
          { required: true, type: 'number', message: 'Обязательное поле', trigger: 'blur' },
        ],
        letterofbank_ids: [
          { required: true, type: 'array', message: 'Обязательное поле', trigger: 'blur' },
        ],
      },
    }
  },
  methods: {
    changeFormValues(newValue) {
      this.formValues = newValue
    },
  },
  mounted() {
    this.formValues = this.values
  },
})
</script>

<style scoped></style>
