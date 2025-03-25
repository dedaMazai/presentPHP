<template>
  <simple-form :initial-values="values" :rules="rules">
    <template #default="{ model }">
      <text-input label="Заголовок" name="title"></text-input>
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
  is_published: false,
  title: null,
}

export default defineComponent({
  name: 'ArticlesForm',
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
        title: [
          { required: true, message: 'Заполните заголовок', trigger: 'blur' },
        ],
      },
    }
  },
})
</script>

<style scoped></style>
