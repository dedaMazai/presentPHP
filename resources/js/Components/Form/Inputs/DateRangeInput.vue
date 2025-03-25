<template>
  <a-form-item :label="label">
    <a-range-picker
      :format="globalDateFormat"
      :placeholder="['От', 'До']"
      :showTime="false"
      :value="value"
      :allowEmpty="[true, true]"
      style="width: 100%"
      @change="handleChange"
    />
  </a-form-item>
</template>

<script>
import {defineComponent} from 'vue'
import useField from '~/composables/useField'
import {globalDateFormat, globalDateTimeFormat} from '~/utils'
import {InputProps} from './types'

export default defineComponent({
  name: 'DateRangeInput',
  props: {
    ...InputProps,
  },
  data() {
    return {
      globalDateFormat,
      globalDateTimeFormat,
    }
  },
  setup(props) {
    let {value} = useField(props.name)

    const handleChange = (val, period) => {
      value.value = period
    }

    return {
      value,
      handleChange,
    }
  },
})
</script>

<style scoped></style>
