<template>
  <simple-form :initial-values="values" :rules="rules" :userBanReset="values.ban_status === 'Заблокирован'">
    <template #default="{ model }">
      <text-input label="ID" name="id" readonly></text-input>
      <text-input label="CRM ID" name="crm_id"></text-input>
      <phone-text-input label="Телефон" name="phone"></phone-text-input>
      <radio-group-input
        :options="PASSWORD"
        label="Менеджер с ролью управления пользователями"
        name="manager_control"
      ></radio-group-input>
      <radio-group-input
        :options="PASSWORD"
        label="Активен"
        name="status"
      ></radio-group-input>
      <text-input label="Блокировка" name="ban_status" readonly></text-input>
      <text-input v-if="values.ban_status === 'Заблокирован'" label="Время разблокировки" name="unlock_time" readonly></text-input>
      <text-input label="Email" name="email" readonly></text-input>
      <text-input label="Имя" name="first_name" readonly></text-input>
      <text-input label="Фамилия" name="last_name" readonly></text-input>
      <text-input label="Отчество" name="middle_name" readonly></text-input>
      <date-input label="Дата рождения" name="birth_date" readonly></date-input>
      <radio-group-input
        :options="PASSWORD"
        label="Изменить пароль"
        name="mode"
      ></radio-group-input>
      <text-password-input v-if="model.mode === true" label="Пароль" name="password"></text-password-input>
      <text-password-input v-if="model.mode === true" label="Подтвердить пароль" name="suc_password"></text-password-input>
      <date-input
        label="Дата создания"
        name="created_at"
        readonly
        time
      ></date-input>
      <date-input
        label="Дата обновления"
        name="updated_at"
        readonly
        time
      ></date-input>
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
  id: null,
  crm_id: null,
  phone: null,
  ban_status: null,
  unlock_time: null,
  email: null,
  first_name: null,
  last_name: null,
  middle_name: null,
  birth_date: null,
  password: null,
  created_at: null,
  updated_at: null,
  mode: false,
  manager_control: false,
  status: true,
}

export default defineComponent({
  name: 'UsersForm',
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
      }
    },
  },
})
</script>

<style scoped></style>
