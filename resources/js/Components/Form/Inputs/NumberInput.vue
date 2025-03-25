<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <a-input-number
      v-model:value="value"
      :min="min"
      :max="max"
      :step="step"
      :precision="precision"
    />
  </a-form-item>
</template>

<script>
import useField from '~/composables/useField'
import { InputProps } from './types'

export default {
  name: 'NumberInput',
  props: {
    ...InputProps,
    min: {
      type: Number,
      default: 1,
    },
    max: Number,
    step: {
      type: Number,
      default: 1,
    },
    precision: {
      type: Number,
      default: 2,
    },
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)
    return {
      value,
      validateInfos,
    }
  },
}
</script>

<style scoped></style>
