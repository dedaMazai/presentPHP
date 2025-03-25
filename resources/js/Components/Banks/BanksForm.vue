<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
  >
    <template #default="{ model }">
      <text-input
        label="Наименование"
        name="name"
      ></text-input>
      <text-input
        label="CRM ID"
        name="bank_id"
      ></text-input>
      <image-upload-input
        label="Изображение"
        name="image_id"
      ></image-upload-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import ImageUploadInput from '~/Components/Form/Inputs/ImageUploadInput'

const defaultValues = {
  name: null,
  bank_id: null,
  image_id: null,
}

export default defineComponent({
  name: 'BanksForm',
  components: {
    SimpleForm,
    TextInput,
    ImageUploadInput,
  },
  props: {
    initialValues: Object,
  },
  data() {
    return {
      values: {...defaultValues, ...this.initialValues},
      rules: {
        name: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        bank_id: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        image_id: [
          { required: true, type: 'number', message: 'Обязательное поле', trigger: 'blur' },
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
