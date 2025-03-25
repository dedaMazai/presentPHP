<template>
  <span>{{ value }}</span>
</template>

<script>
import {defineComponent} from 'vue'
import {CellProps} from './types'

export default defineComponent({
  name: 'TextCell',
  props: {
    ...CellProps,
  },
})
</script>

<style scoped></style>
