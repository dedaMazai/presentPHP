<template>
  <simple-form :initial-values="values" :rules="rules">
    <template #default="{ model }">
      <select-input
        :options="PUBLISHED"
        label="Статус публикации"
        name="is_published"
      ></select-input>
      <text-input label="Название" name="title"></text-input>
      <select-input label="Тип" name="type" resource="types"></select-input>
      <number-input
        label="Стоимость открытия аккредитива"
        name="price"
      ></number-input>
      <text-input label="Ссылка" name="link"></text-input>
      <text-input label="CRM ID" name="crm_id"></text-input>
      <image-upload-input
        label="Логотип"
        name="logo_image_id"
      ></image-upload-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import NumberInput from '~/Components/Form/Inputs/NumberInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import ImageUploadInput from '~/Components/Form/Inputs/ImageUploadInput'
import RadioGroupInput from '~/Components/Form/Inputs/RadioGroupInput'
import { PUBLISHED, LINK_MODE } from '~/constants/statuses'

const defaultValues = {
  is_published: false,
  title: null,
  logo_image_id: null,
  price: null,
  link: null,
  crm_id: null,
  type: null,
}

export default defineComponent({
  name: 'BankInfoForm',
  components: {
    SimpleForm,
    TextInput,
    ImageUploadInput,
    SelectInput,
    DateRangeInput,
    RadioGroupInput,
    NumberInput,
  },
  props: {
    initialValues: Object,
  },
  data() {
    return {
      PUBLISHED,
      LINK_MODE,
      values: { ...defaultValues, ...this.initialValues },
      rules: {
        is_published: [
          {
            required: true,
            type: 'boolean',
            message: 'Выберите статус публикации',
            trigger: 'blur',
          },
        ],
        type: [{required: true, message: 'Выберите тип', trigger: 'blur'}],
        title: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        price: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'number',
          },
        ],
        crm_id: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        logo_image_id: [
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
