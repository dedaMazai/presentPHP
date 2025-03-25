<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
  >
    <template #default="{ model }">
      <text-input label="Наименование группы" name="group_reality_name"></text-input>
      <select-input
        label="Тип недвижимости"
        name="group_reality_ids"
        mode="multiple"
        :options="realityTypes"
        label-field-id="reality_name"
        value-field-id="reality_id"
      ></select-input>
    </template>
  </simple-form>
</template>

<script>
import {defineComponent} from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'

const defaultValues = {
  group_reality_name: null,
}

export default defineComponent({
  name: 'GroupingRealityTypesForm',
  components: {
    SimpleForm,
    TextInput,
    SelectInput,
  },
  props: {
    initialValues: Object,
    realityTypes: Array,
  },
  computed: {
    values() {
      return{...defaultValues, ...this.initialValues}
    },
    rules() {
      return {
        group_reality_name: [
          {required: true, message: 'Обязательное поле', trigger: 'blur'},
        ],

      }
    },
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
