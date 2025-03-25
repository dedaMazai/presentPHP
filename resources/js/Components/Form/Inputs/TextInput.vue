<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <span v-if="readonly">{{ value }}</span>
    <a-input v-else v-model:value="value" />
  </a-form-item>
</template>

<script>
import { inject, watch } from 'vue'
import useField from '~/composables/useField'
import { InputProps } from './types'

export default {
  name: 'TextInput',
  props: {
    ...InputProps,
    readonly: {
      type: Boolean,
      default: false,
    },
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)
    const formModel = inject('$formModel')
    watch(value, (newValue) => {
      if (newValue === '') {
        formModel[props.name] = null
      }
    })
    return {
      value,
      validateInfos,
    }
  },
}
</script>

<style scoped></style>
