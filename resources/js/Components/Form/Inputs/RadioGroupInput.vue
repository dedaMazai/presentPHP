<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <a-radio-group v-model:value="value">
      <a-radio
        v-for="item in options"
        :key="item[valueFieldId]"
        :value="item[valueFieldId]"
      >{{ item[labelFieldId] }}
      </a-radio
      >
    </a-radio-group>
  </a-form-item>
</template>

<script>
import useField from '~/composables/useField'
import {SelectProps} from './types'
import useDictionary from '~/composables/useDictionary'

export default {
  name: 'TextInput',
  props: {
    ...SelectProps,
  },
  setup(props) {
    const {value, validateInfos} = useField(props.name)
    const dictionary = useDictionary(props.resource)

    return {
      value,
      validateInfos,
      options: props.options || dictionary.data,
    }
  },
}
</script>

<style scoped></style>
