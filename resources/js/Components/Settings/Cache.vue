<template>
  <a-button type="primary" @click="reloadCache"
    >Перезагрузить кэш каталога</a-button
  >
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'Cache',
  methods: {
    reloadCache() {
      this.$inertia.post(this.route('settings.cache.reload-catalogue-tree'))
    },
  },
})
</script>
