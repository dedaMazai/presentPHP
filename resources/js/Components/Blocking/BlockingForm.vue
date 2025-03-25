<template>
  <simple-form :initial-values="values" :banReset=true :readonly=true>
    <template #default="{ model }">
      <text-input label="IP-адрес" name="ip_address" readonly></text-input>
      <text-input label="Время блокировки" name="block_time" readonly></text-input>
      <text-input label="Время разблокировки" name="unlock_time" readonly></text-input>
      <text-input label="Количество попыток" name="attempts" readonly></text-input>
      <text-input label="Номера телефонов" name="phone_numbers" readonly></text-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import TextPasswordInput from '~/Components/Form/Inputs/TextPasswordInput'
import PhoneTextInput from '~/Components/Form/Inputs/PhoneTextInput'
import DateInput from '~/Components/Form/Inputs/DateInput'
import RadioGroupInput from '~/Components/Form/Inputs/RadioGroupInput'
import { PASSWORD } from '~/constants/statuses'

const defaultValues = {
  ip_address: null,
  block_time: null,
  unlock_time: null,
  attempts: null,
}

export default defineComponent({
  name: 'BlockingForm',
  components: {
    SimpleForm,
    TextInput,
    TextPasswordInput,
    DateInput,
    PhoneTextInput,
    RadioGroupInput
  },
  props: {
    initialValues: Object,
  },
  data() {
    return {
      PASSWORD,
      values: { ...defaultValues, ...this.initialValues },
      formValues: {},
    }
  },
})
</script>

<style scoped></style>
