<template>
  <simple-form :initial-values="values" :rules="rules" @on-submit="onSubmit">
    <text-input
      label="CRM ID Главной категории"
      name="claim_root_category_crm_id"
    ></text-input>
    <text-input
      label="CRM ID услуги для заявки Пропуск (Автомобиль)"
      name="claim_pass_car_crm_service_id"
    ></text-input>
    <text-input
      label="CRM ID услуги для заявки Пропуск (Человек)"
      name="claim_pass_human_crm_service_id"
    ></text-input>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import { message } from 'ant-design-vue'

const defaultValues = {
  claim_root_category_crm_id: null,
  claim_pass_car_crm_service_id: null,
  claim_pass_human_crm_service_id: null,
}

export default defineComponent({
  name: 'ServicesForm',
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
        claim_root_category_crm_id: [
          { required: true, message: 'Заполните поле', trigger: 'blur' },
        ],
        claim_pass_car_crm_service_id: [
          { required: true, message: 'Заполните поле', trigger: 'blur' },
        ],
        claim_pass_human_crm_service_id: [
          { required: true, message: 'Заполните поле', trigger: 'blur' },
        ],
      },
    }
  },
  methods: {
    onSubmit(data) {
      this.$inertia.put(this.route('settings.services.update'), data, {
        onFinish: () => message.success('Настройки сохранены.'),
      })
    },
  },
})
</script>
