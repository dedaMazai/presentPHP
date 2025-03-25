<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <span v-if="readonly">{{ formatDateTime(value, dateFormat) }}</span>
    <a-date-picker
      v-else
      :format="dateFormat"
      :showTime="false"
      :value="value"
      :defaultValue="formatDateTime(new Date(), dateFormat)"
      :allowClear="false"
      style="width: 100%"
      @change="handleChange"
    />
  </a-form-item>
</template>

<script>
import { computed, defineComponent } from 'vue'
import useField from '~/composables/useField'
import { globalDateFormat, globalDateTimeFormat, formatDateTime } from '~/utils'
import moment from "moment";
import { InputProps } from './types'

export default defineComponent({
  name: 'CurrentDateInput',
  props: {
    ...InputProps,
    readonly: {
      type: Boolean,
      default: false,
    },
    time: {
      type: Boolean,
      default: false,
    }
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)
    const handleChange = (val, date) => {
      value.value = date
    }

    const dateFormat = computed(() =>
      props.time ? globalDateTimeFormat : globalDateFormat
    )

    return {
      value,
      handleChange,
      formatDateTime,
      dateFormat,
      validateInfos,
    }
  },
})
</script>

<style scoped></style>
