<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <span v-if="readonly">{{ readonlyValue }}</span>
    <a-select v-else v-model:value="value" :show-search="search" :mode="mode">
      <a-select-option
        v-for="item in options"
        :key="item[valueFieldId]"
        :value="item[valueFieldId]"
      >
        <a-typography-text
          v-if="ellipsis"
          :ellipsis="{ rows, tooltip: item[labelFieldId] }"
          :style="{ width: labelWidth }"
          :content="item[labelFieldId]"
        ></a-typography-text>
        <span v-else>{{ item[labelFieldId] }}</span>
      </a-select-option>
    </a-select>
  </a-form-item>
</template>

<script>
import { computed } from 'vue'
import isArray from 'lodash/isArray'
import find from 'lodash/find'
import isObject from 'lodash/isObject'
import useField from '~/composables/useField'
import useDictionary from '~/composables/useDictionary'
import { SelectProps } from './types'

export default {
  name: 'SelectInput',
  props: {
    ...SelectProps,
    search: {
      type: Boolean,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    ellipsis: {
      type: Boolean,
      default: false,
    },
    rows: {
      type: Number,
      default: 1,
    },
    labelWidth: String,
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)
    const dictionary = useDictionary(props.resource)
    const options = computed(() => {
      return props.options || dictionary.data
    })

    const readonlyValue = computed(() => {
      const key = props.valueFieldId || 'key'
      const label = props.labelFieldId || 'label'
      if (isArray(value.value)) {
        const valuesLabels = value.value.map(
          (item) => find(options.value, { [key]: item })[label]
        )
        return valuesLabels.join(', ')
      } else if (isObject(value.value)) {
        const valuesLabels = Object.values(value.value)[0].map(
          (item) => find(options.value, { [key]: item })[label]
        )
        return valuesLabels.join(', ')
      } else {
        const val = find(options.value, { [key]: value.value })
        return val ? val[label] : ''
      }
    })

    return {
      value,
      validateInfos,
      options,
      readonlyValue,
    }
  },
}
</script>

<style scoped></style>
