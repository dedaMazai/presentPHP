<template>
  <a-form-item :label="labelTooltip ? null : label" v-bind="validateInfos">
    <template #label>
      <a-space>
        <span>{{ label }}</span>
        <a-tooltip>
          <template #title>{{ labelTooltip }}</template>
          <QuestionCircleOutlined />
        </a-tooltip>
      </a-space>
    </template>
    <span v-if="readonly">{{ value }}</span>
    <a-textarea v-else v-model:value="value" :rows="rows" />
  </a-form-item>
</template>

<script>
import { inject, watch } from 'vue'
import { QuestionCircleOutlined } from '@ant-design/icons-vue'
import useField from '~/composables/useField'
import { InputProps } from './types'

export default {
  name: 'TextAreaInput',
  props: {
    ...InputProps,
    rows: {
      type: Number,
      default: 4,
    },
    labelTooltip: String,
    readonly: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    QuestionCircleOutlined,
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
