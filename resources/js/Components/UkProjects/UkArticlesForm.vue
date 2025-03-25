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
        label="Иконка"
        name="icon_image_id"
      ></image-upload-input>
    </template>
  </simple-form>
</template>

<script>
import {defineComponent} from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import {PUBLISHED} from '~/constants/statuses'
import ImageUploadInput from '~/Components/Form/Inputs/ImageUploadInput'

const defaultValues = {
  is_published: false,
  title: null,
  icon_image_id: null,
}

export default defineComponent({
  name: 'UkArticlesForm',
  components: {
    SimpleForm,
    TextInput,
    SelectInput,
    ImageUploadInput,
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
            message: 'Выберите статус публикации',
            trigger: 'blur',
          },
        ],
        title: [
          {required: true, message: 'Заполните заголовок', trigger: 'blur'},
        ],
        icon_image_id: [
          {required: true, message: 'Загрузите иконку'},
        ],
      },
    }
  },
})
</script>

<style scoped></style>
