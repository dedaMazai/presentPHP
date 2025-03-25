<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
  >
    <template #default="{ model }">
      <text-input label="Код документа в CRM" name="code"></text-input>
      <text-input label="Наименование документа" name="name"></text-input>
      <text-area-input
        label="Описание"
        name="description"
      ></text-area-input>
      <number-input
        label="Код типа объекта"
        name="object_type_code"
        precision="0"
      ></number-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import TextAreaInput from '~/Components/Form/Inputs/TextAreaInput'
import NumberInput from '~/Components/Form/Inputs/NumberInput'

const defaultValues = {
  code: '',
  name: '',
  description: '',
  object_type_code: null,
}

export default defineComponent({
  name: 'DocumentsNameForm',
  components: {
    SimpleForm,
    TextInput,
    TextAreaInput,
    NumberInput,
  },
  props: {
    initialValues: Object,
  },
  data() {
    return {
      values: {...defaultValues, ...this.initialValues},
      rules: {
        code: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        name: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        object_type_code: [
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
