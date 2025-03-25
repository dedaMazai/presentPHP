<template>
  <span>{{ dateStr }}</span>
</template>

<script>
import {defineComponent} from 'vue'
import {formatDateTime, globalDateFormat} from '~/utils'
import {CellProps} from './types'

export default defineComponent({
  name: 'DateOnlyCell',
  props: {
    ...CellProps,
  },
  computed: {
    dateStr() {
      return formatDateTime(this.value, globalDateFormat)
    }
  }
})
</script>

<style scoped></style>
