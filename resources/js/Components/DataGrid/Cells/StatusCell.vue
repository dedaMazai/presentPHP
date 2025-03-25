<template>
  <a-badge
    :status="value ? 'success' : 'error'"
    :text="value ? 'Active' : 'Inactive'"
  />
</template>

<script>
import {defineComponent} from 'vue'
import {CellProps} from './types'

export default defineComponent({
  name: 'StatusCell',
  props: {
    ...CellProps,
  },
})
</script>

<style scoped></style>
