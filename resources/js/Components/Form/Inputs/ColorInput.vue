<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <input type="color" :value="value" @change="change($event)" />
  </a-form-item>
</template>

<script>
import useField from '~/composables/useField'
import { InputProps } from './types'

export default {
  name: 'ColorInput',
  props: {
    ...InputProps,
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)

    const change = (event) => {
      value.value = event.target.value
    }

    return {
      value,
      validateInfos,
      change,
    }
  },
}
</script>

<style scoped></style>
