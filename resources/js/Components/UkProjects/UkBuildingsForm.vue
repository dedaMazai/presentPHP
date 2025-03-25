<template>
  <simple-form :initial-values="values" :rules="rules">
    <template #default="{ model }">
      <text-input label="Наименование корпуса" name="build_name"></text-input>
      <text-input label="Идентификатор корпуса" name="build_zid"></text-input>
      <file-upload-input
        label="Инструкция по эксплуатации объекта"
        name="instruction_url"
        link-name="Скачать документ"
      />
    </template>
  </simple-form>
</template>

<script>
import {defineComponent} from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import FileUploadInput from '~/Components/Form/Inputs/FileUploadInput'

const defaultValues = {
  build_name: null,
  build_zid: null,
  instruction_url: "",
}

export default defineComponent({
  name: 'UkBuildingsForm',
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
      values: {
        ...defaultValues,
        ...this.initialValues
      },
      rules: {
        build_name: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur'
          },
        ],
        build_zid: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur'
          },
        ],
        instruction_url: [
          {
            validator: this.validatePdfFile,
            message: 'Допускается только файлы PDF',
            trigger: 'change'
          },
        ],
      },
    }
  },
  methods: {
    changeFormValues (newValue) {
      this.formValues = newValue
    },
    validatePdfFile (rule, value, callback) {
      if (value !== "") {
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
