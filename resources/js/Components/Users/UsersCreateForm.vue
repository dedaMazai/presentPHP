<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
    @on-field-change="changeFormValues"
  >
    <template #default="{ model }">
      <phone-text-input label="Телефон" name="phone"></phone-text-input>
      <text-input label="Имя" name="first_name"></text-input>
      <text-input label="Фамилия" name="last_name"></text-input>
      <text-input label="Отчество" name="middle_name"></text-input>
      <date-input label="Дата рождения" name="birth_date"></date-input>
      <text-input label="Email" name="email"></text-input>
      <text-input label="CRM ID" name="crm_id"></text-input>
      <radio-group-input
        :options="PASSWORD"
        label="Установить пароль"
        name="mode"
      ></radio-group-input>
      <text-password-input v-if="model.mode === true" label="Пароль" name="password"></text-password-input>
      <text-password-input v-if="model.mode === true" label="Подтвердить пароль" name="suc_password"></text-password-input>
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
import toNumber from 'lodash/toNumber'

const defaultValues = {
  crm_id: null,
  phone: null,
  email: null,
  first_name: null,
  last_name: null,
  middle_name: null,
  birth_date: null,
  created_at: null,
  updated_at: null,
  mode: false,
  password: null,
}

export default defineComponent({
  name: 'UsersCreateForm',
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
  computed: {
    rules() {
      return {
        crm_id: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        phone: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
          {
            type: 'number',
            message: 'Значение должно быть числом',
            transform(value) {
              return toNumber(value)
            },
            trigger: 'blur'
          },
        ],
        first_name: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
          { min: 2, message: 'Некорректное значение', trigger: 'blur' },
          { max: 255, message: 'Некорректное значение', trigger: 'blur' },
          { pattern: /^[А-я]/, message: 'Некорректное значение', trigger: 'blur'},
          { pattern: /[А-я]/, message: 'Некорректное значение', trigger: 'blur'}
        ],
        last_name: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
          { min: 2, message: 'Некорректное значение', trigger: 'blur' },
          { max: 255, message: 'Некорректное значение', trigger: 'blur' },
          { pattern: /^[А-я]/, message: 'Некорректное значение', trigger: 'blur'},
          { pattern: /[А-я]/, message: 'Некорректное значение', trigger: 'blur'}
        ],
        middle_name: [
          { min: 2, message: 'Некорректное значение', trigger: 'blur' },
          { max: 255, message: 'Некорректное значение', trigger: 'blur' },
          { pattern: /[А-я]/, message: 'Некорректное значение', trigger: 'blur'}
        ],
        birth_date: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
          { date_format:'dd/MM/yyyy', message: 'Возраст должен быть больше 18', trigger: 'blur' },
        ],
        email: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        password: [
          { min: 2, message: 'Некорректное значение', trigger: 'blur' },
        ],
        suc_password: [
          { min: 2, message: 'Некорректное значение', trigger: 'blur' },
        ],
      }
    },
  },
  methods: {
    changeFormValues(newValue) {
      this.formValues = newValue
    },
  },
})
</script>

<style scoped></style>
