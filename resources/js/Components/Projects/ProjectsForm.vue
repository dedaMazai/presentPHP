<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
    @on-field-change="changeFormValues"
  >
    <template #default="{ model }">
      <select-input
        :options="PUBLISHED"
        label="Статус публикации"
        name="is_published"
      ></select-input>
      <text-input label="Название" name="name"></text-input>
      <text-area-input label="Описание" name="description"></text-area-input>
      <color-input label="Цвет" name="color"></color-input>
      <image-upload-input
        label="Изображение витрины"
        name="showcase_image_id"
      ></image-upload-input>
      <image-upload-input
        label="Главное изображение"
        name="main_image_id"
      ></image-upload-input>
      <image-upload-input
        label="Изображение карты"
        name="map_image_id"
      ></image-upload-input>
      <image-upload-input
        label="Галерея"
        name="image_ids"
        multi
      ></image-upload-input>
      <text-input label="Широта" name="lat"></text-input>
      <text-input label="Долгота" name="long"></text-input>
      <select-input
        :options="cities"
        label="Город"
        name="city_id"
        value-field-id="id"
        label-field-id="name"
      ></select-input>
      <text-input label="Метро" name="metro"></text-input>
      <color-input label="Цвет ветки метро" name="metro_color"></color-input>
      <text-input label="Телефон офиса" name="office_phone"></text-input>
      <text-input label="Адрес офиса" name="office_address"></text-input>
      <text-input label="Широта офиса" name="office_lat"></text-input>
      <text-input label="Долгота офиса" name="office_long"></text-input>
      <text-area-input
        label="Рабочие часы офиса"
        name="office_work_hours"
      ></text-area-input>
      <property-type-params-input
        label="Параметры объектов"
        name="property_type_params"
        :property-types="propertyTypes"
      ></property-type-params-input>
      <crm-id-input
        label="CRM ID"
        name="crm_ids"
        :booking-property="values.booking_property"
        @addRow="addCrmRow"
        @removeRow="removeCrmRow"
      ></crm-id-input>
      <mortgage-calculator-id-input
        label="ID Ипотеки"
        name="mortgage_calculator_id"
      ></mortgage-calculator-id-input>
      <select-input
        :options="mortgageTypes"
        label="Типы ипотеки"
        name="mortgage_types"
        value-field-id="value"
        label-field-id="label"
        mode="multiple"
      ></select-input>
      <payroll-bank-programs-input
        label="Зарплатные проекты"
        name="payroll_bank_programs"
      ></payroll-bank-programs-input>
      <number-input
        label="Мин. стоимость ОН для ипотеки (млн)"
        name="mortgage_min_property_price"
      ></number-input>
      <number-input
        label="Макс. стоимость ОН для ипотеки (млн)"
        name="mortgage_max_property_price"
      ></number-input>
      <text-input label="Памятка собственника" name="url_memo"></text-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import toNumber from 'lodash/toNumber'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import NumberInput from '~/Components/Form/Inputs/NumberInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import ImageUploadInput from '~/Components/Form/Inputs/ImageUploadInput'
import TextAreaInput from '~/Components/Form/Inputs/TextAreaInput'
import ColorInput from '~/Components/Form/Inputs/ColorInput'
import PropertyTypeParamsInput from '~/Components/Projects/PropertyTypeParamsInput'
import CrmIdInput from '~/Components/Projects/CrmIdInput'
import MortgageCalculatorIdInput from '~/Components/Projects/MortgageCalculatorIdInput'
import PayrollBankProgramsInput from './PayrollBankProgramsInput'
import SwitchInput from '~/Components/Form/Inputs/SwitchInput'
import { PUBLISHED, LINK_MODE } from '~/constants/statuses'

const defaultValues = {
  is_published: false,
  name: null,
  showcase_image_id: null,
  main_image_id: null,
  map_image_id: null,
  metro: null,
  metro_color: null,
  crm_ids: [],
  mortgage_calculator_id: null,
  lat: null,
  long: null,
  office_phone: null,
  office_address: null,
  office_lat: null,
  office_long: null,
  office_work_hours: null,
  property_type_params: [],
  color: null,
  description: null,
  images_id: [],
  city_id: null,
  mortgage_types: [],
  payroll_bank_programs: [],
  mortgage_min_property_price: null,
  mortgage_max_property_price: null,
  booking_property: [],
  url_memo: null
}

export default defineComponent({
  name: 'ProjectForm',
  components: {
    SimpleForm,
    TextInput,
    NumberInput,
    SelectInput,
    ImageUploadInput,
    TextAreaInput,
    ColorInput,
    PropertyTypeParamsInput,
    PayrollBankProgramsInput,
    CrmIdInput,
    MortgageCalculatorIdInput,
    SwitchInput,
  },
  props: {
    initialValues: Object,
    propertyTypes: Object,
    cities: Array,
    mortgageTypes: Array,
  },
  data() {
    return {
      PUBLISHED,
      LINK_MODE,
      values: { ...defaultValues, ...this.initialValues },
      rules: {
        is_published: [
          {
            required: true,
            type: 'boolean',
            message: 'Обязательное поле',
            trigger: 'blur',
          },
        ],
        name: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        color: [
          { required: true, message: 'Обязательное поле', trigger: 'blur' },
        ],
        crm_ids: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'array',
          },
        ],
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
        main_image_id: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'number',
          },
        ],
        mortgage_calculator_id: [
          {
            type: 'number',
            message: 'Значение должно быть числом',
            transform(value) {
              return toNumber(value)
            },
          },
        ],
        property_type_params: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'array',
          },
        ],
        showcase_image_id: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'number',
          },
        ],
        city_id: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'number',
          },
        ],
        mortgage_types: [
          {
            required: true,
            message: 'Обязательное поле',
            trigger: 'blur',
            type: 'array',
          },
        ],
        payroll_bank_programs: [
          {
            required: false,
            trigger: 'blur',
            type: 'array',
            fields: [
              {
                type: 'object',
                fields: {
                  id: {
                    type: 'integer',
                    required: true,
                    message: 'ID должно быть целым числом',
                  },
                  name: {
                    type: 'string',
                    required: true,
                    message: 'Обязательное поле',
                  },
                },
              },
            ],
          },
        ],
      },
    }
  },
  methods: {
    changeFormValues(newValue) {
      this.formValues = newValue
    },

    addCrmRow(items) {
      this.formValues.crm_ids.push('')
      this.formValues.booking_property.push(items)
    },

    removeCrmRow(index) {
      this.formValues.crm_ids.splice(index, 1)
      this.formValues.booking_property.splice(index, 1)
    },
  },
  mounted() {
    this.formValues = this.values
  },
})
</script>

<style scoped></style>
