<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
    @on-field-change="computeRules"
  >
    <template #default="{ model }">
      <text-input label="Название" name="title"></text-input>
      <image-upload-input
        label="Иконка"
        name="icon_image_id"
      ></image-upload-input>
      <select-input
        :options="types"
        label="Тип"
        name="type"
        value-field-id="value"
        label-field-id="label"
      ></select-input>
      <text-input
        v-if="model.type === 'map'"
        label="Широта"
        name="lat"
      ></text-input>
      <text-input
        v-if="model.type === 'map'"
        label="Долгота"
        name="long"
      ></text-input>
      <text-input
        v-if="model.type === 'email'"
        label="Email"
        name="email"
      ></text-input>
      <text-input
        v-if="model.type === 'phone'"
        label="Телефон"
        name="phone"
      ></text-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import toNumber from 'lodash/toNumber'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import ImageUploadInput from '~/Components/Form/Inputs/ImageUploadInput'

const defaultValues = {
  title: null,
  type: null,
  icon_image_id: null,
  lat: null,
  long: null,
  email: null,
  phone: null,
}

const defaultRules = {
  title: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
  icon_image_id: [
    {
      required: true,
      message: 'Обязательное поле',
      trigger: 'blur',
      type: 'number',
    },
  ],
  type: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
  lat: [
    { required: true, message: 'Обязательное поле', trigger: 'blur' },
    {
      type: 'number',
      message: 'Значение должно быть числом',
      transform(value) {
        return toNumber(value)
      },
    },
  ],
  long: [
    { required: true, message: 'Обязательное поле', trigger: 'blur' },
    {
      type: 'number',
      message: 'Значение должно быть числом',
      transform(value) {
        return toNumber(value)
      },
    },
  ],
  email: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
  phone: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
}

export default defineComponent({
  name: 'UkProjectsContactsForm',
  components: {
    SimpleForm,
    TextInput,
    SelectInput,
    ImageUploadInput,
  },
  props: {
    initialValues: Object,
    contact: Object,
    types: Array,
  },
  data() {
    return {
      values: { ...defaultValues, ...this.contact },
      rules: { ...defaultRules },
    }
  },
  methods: {
    computeRules(model) {
      switch (model.type) {
        case 'phone':
          this.rules = {
            title: defaultRules.title,
            icon_image_id: defaultRules.icon_image_id,
            type: defaultRules.type,
            phone: defaultRules.phone,
          }
          break
        case 'email':
          this.rules = {
            title: defaultRules.title,
            icon_image_id: defaultRules.icon_image_id,
            type: defaultRules.type,
            email: defaultRules.email,
          }
          break
        case 'map':
          this.rules = {
            title: defaultRules.title,
            icon_image_id: defaultRules.icon_image_id,
            type: defaultRules.type,
            lat: defaultRules.lat,
            long: defaultRules.long,
          }
          break
        default:
          this.rules = {
            title: defaultRules.title,
            icon_image_id: defaultRules.icon_image_id,
            type: defaultRules.type,
          }
      }
    },
  },
  mounted() {
    if (this.contact) {
      this.computeRules(this.contact)
    }
  },
})
</script>
