<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
  >
    <template #default="{ model }">
      <text-input label="Наименование типа недвижимости" name="reality_name"></text-input>
      <text-input label="Код типа недвижимости" name="reality_id"></text-input>
    </template>
  </simple-form>
</template>

<script>
import {defineComponent} from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'

const defaultValues = {
  reality_name: null,
  reality_id: null,
}

export default defineComponent({
  name: 'RealityTypesForm',
  components: {
    SimpleForm,
    TextInput,
  },
  props: {
    initialValues: Object,
  },
  data() {
    return {
      values: {...defaultValues, ...this.initialValues},
      rules: {
        reality_name: [
          {required: true, message: 'Обязательное поле', trigger: 'blur'},
        ],
        reality_id: [
          {required: true, message: 'Обязательное поле', trigger: 'blur'},
        ],
      },
    }
  },
})
</script>

<style scoped></style>
