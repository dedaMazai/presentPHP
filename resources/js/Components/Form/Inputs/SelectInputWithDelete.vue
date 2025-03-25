<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <span v-if="readonly">{{ readonlyValue }}</span>
    <a-select v-else v-model:value="value" :show-search="search" :mode="mode" :style="{width: '80%'}">
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
    <span @click="clearForm" v-if="value">
      <svg width="34px" height="34px" viewBox="0 0 1024 1024" fill="#000000" class="icon"  version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M512 897.6c-108 0-209.6-42.4-285.6-118.4-76-76-118.4-177.6-118.4-285.6 0-108 42.4-209.6 118.4-285.6 76-76 177.6-118.4 285.6-118.4 108 0 209.6 42.4 285.6 118.4 157.6 157.6 157.6 413.6 0 571.2-76 76-177.6 118.4-285.6 118.4z m0-760c-95.2 0-184.8 36.8-252 104-67.2 67.2-104 156.8-104 252s36.8 184.8 104 252c67.2 67.2 156.8 104 252 104 95.2 0 184.8-36.8 252-104 139.2-139.2 139.2-364.8 0-504-67.2-67.2-156.8-104-252-104z" fill="" /><path d="M707.872 329.392L348.096 689.16l-31.68-31.68 359.776-359.768z" fill="" /><path d="M328 340.8l32-31.2 348 348-32 32z" fill="" />
      </svg>
    </span>
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
  methods: {
    clearForm() {
      this.value = null
    }
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

<style scoped>
</style>
