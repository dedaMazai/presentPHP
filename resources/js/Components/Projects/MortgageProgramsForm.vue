<template>
  <simple-form :initial-values="values" :rules="rules">
    <template #default="{ model }">
      <select-input
        :options="banks"
        label="Банк"
        name="bank_info_id"
        value-field-id="id"
        label-field-id="title"
      ></select-input>
      <number-input label="Первоначальный взнос" name="initial_payment"></number-input>
      <text-input label="Гражданство" name="citizenship"></text-input>
      <number-input label="Срок кредита в годах" name="period" precision="0"></number-input>
      <text-input label="Аккредитованные корпуса" name="addresses"></text-input>
    </template>
  </simple-form>
</template>

<script>
import {defineComponent} from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import NumberInput from '~/Components/Form/Inputs/NumberInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'

const defaultValues = {
  bank_info_id: null,
  initial_payment: null,
  citizenship: null,
  period: null,
  addresses: null,
}

export default defineComponent({
  name: 'MortgageProgramsForm',
  components: {
    SimpleForm,
    TextInput,
    NumberInput,
    SelectInput,
  },
  props: {
    initialValues: Object,
    banks: Array,
  },
  data() {
    return {
      values: { ...defaultValues, ...this.initialValues },
      rules: {
        bank_info_id: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'number',
          },
        ],
        initial_payment: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'number',
          },
        ],
        citizenship: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        period: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'number',
          },
        ],
      },
    }
  },
})
</script>

<style scoped></style>
