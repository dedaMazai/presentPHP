<template>
  <simple-form :initial-values="values" :rules="rules">
    <template #default="{ model }">
      <text-input label="Название" name="name"></text-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'

const defaultValues = {
  name: '',
}

export default defineComponent({
  name: 'ProjectTypeForm',
  components: {
    SimpleForm,
    TextInput,
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
      },
    }
  },
})
</script>

<style scoped></style>
