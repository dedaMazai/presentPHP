<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
  >
    <template #default="{ model }">
      <text-input label="Наименование роли" name="role_name"></text-input>
      <text-input label="Код роли в CRM" name="role_code"></text-input>
    </template>
  </simple-form>
</template>

<script>
import {defineComponent} from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'

const defaultValues = {
  role_name: null,
  role_code: null,
}

export default defineComponent({
  name: 'ClientRoleTypesForm',
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
        role_name: [
          {required: true, message: 'Обязательное поле', trigger: 'blur'},
        ],
        role_code: [
          {required: true, message: 'Обязательное поле', trigger: 'blur'},
        ],
      },
    }
  },
})
</script>

<style scoped></style>
