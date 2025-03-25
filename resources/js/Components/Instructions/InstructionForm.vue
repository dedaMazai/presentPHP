<template>
  <simple-form :initial-values="values" :rules="rules">
    <template #default="{ model }">
      <text-input label="Заголовок" name="title"></text-input>
      <select-input
        :options="PUBLISHED"
        label="Статус публикации"
        name="is_published"
      ></select-input>
      <image-upload-input
        label="Изображение"
        name="image_id"
      ></image-upload-input>
      <text-editor-input label="Текст" name="text"></text-editor-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import TextEditorInput from '~/Components/Form/Inputs/TextEditorInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import ImageUploadInput from '~/Components/Form/Inputs/ImageUploadInput'
import { PUBLISHED } from '~/constants/statuses'

const defaultValues = {
  is_published: false,
  title: null,
  image_id: null,
  text: null,
}

export default defineComponent({
  name: 'InstructionForm',
  components: {
    SimpleForm,
    TextInput,
    ImageUploadInput,
    SelectInput,
    TextEditorInput,
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
        image_id: [{ required: true, message: 'Загрузите изображение' }],
      },
    }
  },
})
</script>

<style scoped></style>
