<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
    @on-field-change="changeFormValues"
    @on-submit="onSubmit"
    @on-submit-as-new="onSubmitAsNew"
  >
    <template #default="{ model }">
      <text-input
        label="Заголовок"
        name="title"
      ></text-input>
      <text-area-input
        label="Описание"
        name="description"
        label-tooltip="Описание будет отправлено в push-уведомлении"
      ></text-area-input>
      <select-input
        label="Категория новости"
        name="category"
        resource="categories"
      ></select-input>
      <select-input
        label="Тип"
        name="type"
        resource="types"
      ></select-input>
      <select-input
        v-if="model.type === 'uk'"
        label="Аудитория"
        name="destination"
        :options="availableDestinations"
        value-field-id="name"
      ></select-input>
      <select-input
        v-if="model.type === 'uk' && model.destination === 'users_by_uk_and_building'"
        label="Проект УК"
        name="uk_project_id"
        :options="ukProjects"
        label-field-id="name"
        value-field-id="id"
      ></select-input>
      <select-input
        v-if="model.type === 'uk' && model.destination === 'users_by_uk_and_building' && model.uk_project_id !== null"
        :label=label(model.uk_project_id)
        name="buildings_id"
        mode="multiple"
        :options="availableBuildings"
        label-field-id="build_name"
        value-field-id="id"
      ></select-input>
      <image-upload-input
        label="Превью изображение"
        name="preview_image_id"
      ></image-upload-input>
      <text-input label="Счетчик" name="count"></text-input>
      <text-input label="Тэг" name="tag"></text-input>
      <text-input label="Ссылка" name="url"></text-input>
      <select-input
        :options="PUBLISHED"
        label="Статус публикации"
        name="is_published"
      ></select-input>
      <news-switch-input
        label="Отправить уведомление"
        name="should_send_notification"
        :is_published="status"
      ></news-switch-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import DateRangeInput from '~/Components/Form/Inputs/DateRangeInput'
import ImageUploadInput from '~/Components/Form/Inputs/ImageUploadInput'
import TextAreaInput from '~/Components/Form/Inputs/TextAreaInput'
import NewsSwitchInput from '~/Components/Form/Inputs/NewsSwitchInput'
import SwitchInput from '~/Components/Form/Inputs/SwitchInput'
import { PUBLISHED } from '~/constants/statuses'
import find from "lodash/find"
import filter from "lodash/filter"
import { isEmpty } from "lodash"

const defaultValues = {
  title: null,
  description: null,
  category: null,
  type: null,
  destination: null,
  uk_project_id: null,
  buildings_id: [],
  preview_image_id: null,
  tag: null,
  url: null,
  is_published: false,
  should_send_notification: false,
  is_sent: false,
  count: null,
}

export default defineComponent({
  name: 'NewsForm',
  components: {
    SimpleForm,
    TextInput,
    ImageUploadInput,
    SelectInput,
    DateRangeInput,
    TextAreaInput,
    NewsSwitchInput,
    SwitchInput,
  },
  props: {
    initialValues: Object,
    destinations: Array,
    ukProjects: Array,
    ukBuildings: Array,
    isSent: Boolean,
  },
  data() {
    return {
      PUBLISHED,
      formValues: {},
      isEmpty,
    }
  },
  computed: {
    values() {
      return { ...defaultValues, ...this.initialValues }
    },

    rules() {
      return {
        title: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        category: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        type: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        destination: [
          {
            required: this.formValues.type
              ? this.formValues.type === 'uk'
              : true,
            message: 'Обязательное поле',
            trigger: 'blur'
          },
        ],
        is_published: [
          { required: true, type: 'boolean', message: 'Обязательное поле', trigger: 'blur' },
        ],
      }
    },

    availableDestinations() {
      const selected_news_type = this.formValues.type
      const destinations = this.destinations

        switch (selected_news_type) {
          case 'uk':
            let available_destinations = filter(destinations, (item) =>
              item.name.includes('all_uk_users') ||
              item.name.includes('users_by_uk_and_building')
            )

            if (!find(available_destinations, (item) => item.name.includes(this.formValues.destination))) {
              this.formValues.destination = ''
            }

            return available_destinations
          default:
            return destinations
        }
    },

    availableBuildings() {
      const selected_uk_project_id = this.formValues.uk_project_id
      const buildings = this.ukBuildings

      return filter(buildings, { project_id: selected_uk_project_id })
    },

    status() {
      return !this.isSent && this.formValues.is_published
    },
  },
  methods: {
    changeFormValues(newValue) {
      this.formValues = newValue
    },

    label(current_project_id) {
      return 'Корпус в Проекте УК "' + find(this.ukProjects, (item) =>
        item.id === current_project_id
      ).name + '"'
    },
    onSubmit(values) {
      if (values.type !== 'uk' || isEmpty(values.destination)) {
        values.destination = 'company_news_subscribers'
      }

      if (values.destination !== 'users_by_uk_and_building') {
        values.buildings_id = []
      }

      values.is_sent = this.isSent
    },
  },
  mounted() {
    this.formValues = this.values
  },
})
</script>

<style scoped></style>
