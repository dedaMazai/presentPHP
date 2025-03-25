<template>
  <span>{{ dateStr }}</span>
</template>

<script>
import {defineComponent} from 'vue'
import {formatDateTime} from '~/utils'
import {CellProps} from './types'

export default defineComponent({
  name: 'DateCell',
  props: {
    ...CellProps,
  },
  computed: {
    dateStr() {
      return formatDateTime(this.value)
    }
  }
})
</script>

<style scoped></style>
