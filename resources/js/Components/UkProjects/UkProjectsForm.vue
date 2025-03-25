<template>
  <simple-form :initial-values="values" :rules="rules">
    <template #default="{ model }">
      <select-input
        :options="PUBLISHED"
        label="Статус публикации"
        name="is_published"
      ></select-input>
      <text-input label="Название" name="name"></text-input>
      <text-area-input label="Описание" name="description"></text-area-input>
      <text-input label="Индекс" name="postcode"></text-input>
      <image-upload-input
        label="Изображение"
        name="image_id"
      ></image-upload-input>
      <text-input label="ID Адреса в 1C" name="crm_1c_id"></text-input>
      <image-upload-input
        label="Изображение для маркетплейса"
        name="market_image_id"
      ></image-upload-input>
      <text-input label="Телефон аварийной службы УК" name="uk_emergency_claim_phone"></text-input>
    </template>
  </simple-form>
</template>

<script>
import {defineComponent} from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import ImageUploadInput from '~/Components/Form/Inputs/ImageUploadInput'
import TextAreaInput from '~/Components/Form/Inputs/TextAreaInput'
import {PUBLISHED} from '~/constants/statuses'

const defaultValues = {
  is_published: false,
  name: null,
  description: null,
  image_id: null,
  market_image_id: null,
  crm_1c_id: null,
  postcode: null,
  uk_emergency_claim_phone: null
}

export default defineComponent({
  name: 'UkProjectForm',
  components: {
    SimpleForm,
    TextInput,
    SelectInput,
    ImageUploadInput,
    TextAreaInput,
  },
  props: {
    initialValues: Object,
  },
  data() {
    return {
      PUBLISHED,
      values: {...defaultValues, ...this.initialValues},
      rules: {
        is_published: [
          {
            required: true,
            type: 'boolean',
            message: 'Обязательное поле',
            trigger: 'blur',
          },
        ],
        name: [
          {required: true, message: 'Обязательное поле', trigger: 'blur'},
        ],
        postcode: [
          {required: true, message: 'Обязательное поле', trigger: 'blur'},
        ],
        description: [
          {required: true, message: 'Обязательное поле', trigger: 'blur'},
        ],
        image_id: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'number',
          },
        ],
        crm_1c_id: [
          {required: true, message: 'Обязательное поле', trigger: 'blur'},
        ],
      },
    }
  },
})
</script>

<style scoped></style>
