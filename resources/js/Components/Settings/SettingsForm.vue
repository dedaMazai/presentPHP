<template>
  <simple-form :initial-values="values" :rules="rules" @on-submit="onSubmit">
    <text-input label="Телефон" name="phone"></text-input>
    <a-divider orientation="left">Главный офис</a-divider>
    <text-input label="Заголовок" name="main_office_title"></text-input>
    <text-input label="Адрес" name="main_office_address"></text-input>
    <text-input label="Телефон" name="main_office_phone"></text-input>
    <text-input label="Email" name="main_office_email"></text-input>
    <text-input label="Широта" name="main_office_lat"></text-input>
    <text-input label="Долгота" name="main_office_long"></text-input>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import { message } from 'ant-design-vue'

const defaultValues = {
  main_office_title: null,
  main_office_address: null,
  main_office_phone: null,
  main_office_email: null,
  main_office_lat: null,
  main_office_long: null,
  phone: null,
}

export default defineComponent({
  name: 'SettingsForm',
  components: {
    SimpleForm,
    TextInput,
  },
  props: {
    settings: Object,
  },
  data() {
    return {
      values: { ...defaultValues, ...this.settings },
      rules: {
        main_office_address: [
          { required: true, message: 'Заполните поле', trigger: 'blur' },
        ],
        main_office_phone: [
          { required: true, message: 'Заполните поле', trigger: 'blur' },
        ],
        main_office_email: [
          { required: true, message: 'Заполните поле', trigger: 'blur' },
        ],
        main_office_lat: [
          { required: true, message: 'Заполните поле', trigger: 'blur' },
        ],
        main_office_long: [
          { required: true, message: 'Заполните поле', trigger: 'blur' },
        ],
        phone: [{ required: true, message: 'Заполните поле', trigger: 'blur' }],
      },
    }
  },
  methods: {
    onSubmit(data) {
      this.$inertia.put(this.route('settings.update'), data, {
        onFinish: () => message.success('Настройки сохранены.'),
      })
    },
  },
})
</script>
