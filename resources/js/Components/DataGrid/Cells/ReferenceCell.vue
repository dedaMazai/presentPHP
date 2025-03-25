<template>
  <a-typography-text
    v-if="ellipsis"
    :ellipsis="{ rows, tooltip: value }"
    :style="{ width: ellipsisWidth }"
    :content="value"
  ></a-typography-text>
  <span v-else>{{ value }}</span>
</template>

<script>
import { computed, defineComponent } from 'vue'
import { CellProps } from './types'
import { usePage } from '@inertiajs/inertia-vue3'

export default defineComponent({
  name: 'ReferenceCell',
  props: {
    ...CellProps,
    resource: {
      type: String,
      required: true,
    },
    ellipsis: {
      type: Boolean,
      default: false,
    },
    rows: {
      type: Number,
      default: 1,
    },
  },
  setup(props) {
    const value = computed(
      () => usePage().props.value[props.resource][props.value]
    )
    const ellipsisWidth = computed(() => {
      return parseInt(props.width) - 30 + 'px'
    })

    return {
      value,
      ellipsisWidth,
    }
  },
})
</script>

<style scoped></style>
