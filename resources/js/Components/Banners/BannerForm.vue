<template>
  <simple-form :initial-values="values" :rules="rules">
    <template #default="{ model }">
      <select-input
        :options="PUBLISHED"
        label="Статус баннера"
        name="is_published"
      ></select-input>
      <current-date-input
        label="Дата начала показа в МП"
        name="start_date"
      ></current-date-input>
      <date-input
        label="Дата окончания показа в МП"
        name="end_date"
      ></date-input>
      <radio-group-input
        :options="LINK_MODE"
        label="Режим"
        name="mode"
      ></radio-group-input>
      <image-upload-input
        label="Изображение баннера"
        name="image_id"
      ></image-upload-input>
      <select-input-with-delete
        v-if="model.mode === 'news'"
        label="Новость"
        label-field-id="title"
        name="news_id"
        resource="news"
        search
        value-field-id="id"
      ></select-input-with-delete>
      <text-input
        v-if="model.mode === 'url'"
        label="Внешняя ссылка"
        name="url"
      ></text-input>
      <text-input
        v-if="place === 'gallery_marketplace'"
        label="CRM ID Категории"
        name="category_crm_id"
      ></text-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import SelectInputWithDelete from '~/Components/Form/Inputs/SelectInputWithDelete'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import DateInput from '~/Components/Form/Inputs/DateInput'
import CurrentDateInput from '~/Components/Form/Inputs/CurrentDateInput'
import ImageUploadInput from '~/Components/Form/Inputs/ImageUploadInput'
import RadioGroupInput from '~/Components/Form/Inputs/RadioGroupInput'
import { globalDateFormat, globalDateTimeFormat, formatDateTime } from '~/utils'
import { PUBLISHED, LINK_MODE } from '~/constants/statuses'

const defaultValues = {
  mode: 'news',
  is_published: false,
  url: null,
  image_id: null,
  news_id: null,
  start_date: formatDateTime(new Date(), globalDateFormat),
  end_date: null,
}

export default defineComponent({
  name: 'BannerForm',
  components: {
    SimpleForm,
    TextInput,
    ImageUploadInput,
    SelectInput,
    DateRangeInput,
    RadioGroupInput,
    SelectInputWithDelete,
    DateInput,
    CurrentDateInput
  },
  props: {
    initialValues: Object,
    place: String,
    news: String
  },
  data() {
    return {
      PUBLISHED,
      LINK_MODE,
      values: {...defaultValues, ...this.initialValues},
      rules: {
        is_published: [
          {
            required: true,
            type: 'boolean',
            message: 'Обязательное поле',
            trigger: 'blur',
          },
        ],
        image_id: [
          {required: true, message: 'Загрузите изображение'},
        ],
        start_date: [
          {
            validator: (rule, value, callback) => {
              if (value && value < formatDateTime(new Date(), globalDateFormat)) {
                callback(new Error('Дата начала показа не может быть в прошлом'));
              } else {
                callback();
              }
            },
            message: 'Дата начала показа не может быть в прошлом',
            trigger: 'blur',
          },
        ],
        end_date: [
          {
            validator: (rule, value, callback) => {
              if (value && value < this.form.start_date) {
                callback(new Error('Дата окончания показа не может быть раньше даты начала показа'));
              } else {
                callback();
              }
            },
            message: 'Дата окончания показа не может быть раньше даты начала показа',
            trigger: 'blur',
          },
        ],
      },
    }
  },
})
</script>

<style scoped></style>
