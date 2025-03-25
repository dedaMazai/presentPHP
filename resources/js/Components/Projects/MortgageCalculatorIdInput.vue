<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <a-input :value="value" @change="handleChange" />
  </a-form-item>
</template>

<script>
import useField from '~/composables/useField'
import { InputProps } from '~/Components/Form/Inputs/types'

export default {
  name: 'MortgageCalculatorIdInput',
  props: {
    ...InputProps,
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)

    const handleChange = (e) => {
      const val = e.target.value;
      if (!val) {
        value.value = null
      } else {
        value.value = val
      }
    }

    return {
      value,
      validateInfos,
      handleChange,
    }
  },
}
</script>

<style scoped></style>
