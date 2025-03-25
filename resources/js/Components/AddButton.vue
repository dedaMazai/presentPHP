<template>
  <a-button
    type="primary"
    @click="$inertia.get(route(`${source}.create`, extraParams))"
  >
    <plus-outlined />
    Добавить
  </a-button>
</template>

<script>
import { inject } from 'vue'
import { PlusOutlined } from '@ant-design/icons-vue'

export default {
  name: 'AddButton',
  components: {
    PlusOutlined,
  },
  props: {
    extraParams: {
      type: Array,
      default: [],
    },
  },
  setup() {
    const source = inject('$sourceName')
    return { source }
  },
}
</script>

<style scoped></style>
