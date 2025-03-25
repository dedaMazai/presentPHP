<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
  >
    <template #default="{ model }">
      <text-input
        label="Название"
        name="name"
      ></text-input>
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
import SelectInput from '~/Components/Form/Inputs/SelectInput'

import { PUBLISHED } from '~/constants/statuses'

const defaultValues = {
  name: null,
  is_published: false,
}

export default defineComponent({
  name: 'SupportTopicsForm',
  components: {
    SimpleForm,
    TextInput,
    SelectInput,
  },
  props: {
    initialValues: Object,
  },
  data() {
    return {
      PUBLISHED,
      values: {...defaultValues, ...this.initialValues},
      rules: {
        name: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
          { max: 255, message: 'Максимальное количество символов - 255', trigger: 'blur' },
        ],
        is_published: [
          { required: true, type: 'boolean', message: 'Обязательное поле', trigger: 'blur' },
        ],
      },
    }
  },
  methods: {
    changeFormValues (newValue) {
      this.formValues = newValue
    },
  },
  mounted() {
    this.formValues = this.values
  },
})
</script>

<style scoped></style>
