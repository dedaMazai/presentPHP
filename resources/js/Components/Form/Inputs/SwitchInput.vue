<template>
  <a-form-item :label="label">
    <a-switch v-model:checked="value"/>
  </a-form-item>
</template>

<script>
import useField from '~/composables/useField'
import {InputProps} from './types'

export default {
  name: 'SwitchInput',
  props: {
    ...InputProps,
  },
  setup(props) {
    const {value} = useField(props.name)
    return {
      value,
    }
  },
}
</script>

<style scoped></style>
