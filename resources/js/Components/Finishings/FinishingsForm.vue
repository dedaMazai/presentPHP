<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
  >
    <template #default="{ model }">
      <text-input
        label="CRM ID"
        name="finishing_id"
      ></text-input>
      <text-area-input
        label="Нaименование отделки"
        name="name"
      ></text-area-input>
      <text-area-input
        label="Описание"
        name="description"
      ></text-area-input>
      <image-upload-input
        label="Изображение"
        name="images_id"
        multi
      ></image-upload-input>
      <file-upload-input
        label="Кaталог отделки"
        name="catalog_url"
        link-name="Скачать каталог"
      />
      <select-input
        :options="PUBLISHED"
        label="Статус публикации"
        name="is_published"
      ></select-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import TextAreaInput from '~/Components/Form/Inputs/TextAreaInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import ImageUploadInput from '~/Components/Form/Inputs/ImageUploadInput'
import FileUploadInput from '~/Components/Form/Inputs/FileUploadInput'

import { PUBLISHED } from '~/constants/statuses'

const defaultValues = {
  is_published: false,
  finishing_id: null,
  description: null,
  images_id: [],
  name: null,
  catalog_url: null,
}

export default defineComponent({
  name: 'FinishingsForm',
  components: {
    SimpleForm,
    TextInput,
    TextAreaInput,
    SelectInput,
    ImageUploadInput,
    FileUploadInput,
  },
  props: {
    initialValues: Object,
  },
  data() {
    return {
      PUBLISHED,
      values: {...defaultValues, ...this.initialValues},
      rules: {
        finishing_id: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        is_published: [
          { required: true, type: 'boolean', message: 'Обязательное поле', trigger: 'blur' },
        ],
        name: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
          { max: 255, message: 'Максимальное количество символов - 255', trigger: 'blur' },
        ],
        catalog_url: [
          { validator: this.validatePdfFile, message: 'Допускается только файлы PDF', trigger: 'change' },
        ],
      },
    }
  },
  methods: {
    changeFormValues (newValue) {
      this.formValues = newValue
    },
    validatePdfFile (rule, value, callback) {
      console.log(value)
      if (value != null) {
        const allowedExtensions = ['pdf'];
        const fileExtension = value.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(fileExtension)) {
          callback(new Error());
        } else {
          callback();
        }
      }
      callback();
    },
  },
  mounted() {
    this.formValues = this.values
  },
})
</script>

<style scoped></style>
