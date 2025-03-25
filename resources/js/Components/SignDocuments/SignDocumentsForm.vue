<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
  >
    <template #default="{ model }">
      <text-input label="Наименование инструкции" name="name"></text-input>
      <text-input label="Код" name="code"></text-input>
      <file-upload-input
        label="Загрузить документ"
        name="document_id"
        link-name="Скачать документ"
      />
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import FileUploadInput from '~/Components/Form/Inputs/FileUploadInput'

const defaultValues = {
  name: '',
  code: '',
  document_id: '',
}

export default defineComponent({
  name: 'SignDocumentsForm',
  components: {
    SimpleForm,
    TextInput,
    FileUploadInput,
  },
  props: {
    initialValues: Object,
  },
  data() {
    return {
      values: { ...defaultValues, ...this.initialValues },
      rules: {
        name: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        code: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        document_id: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
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
