<template>
  <content-items-editor
    v-if="contentItems"
    :content-items="contentItems"
    @sort="onSort"
    @create="onCreate"
    @delete="onDelete"
    @update="onUpdate"
  />
</template>

<script>
import { defineComponent } from 'vue'
import ContentItemsEditor from '../ContentItems/ContentItemsEditor'

export default defineComponent({
  name: 'AboutForm',
  components: {
    ContentItemsEditor
  },
  props: {
    contentItems: Array
  },
  methods: {
    onSort(order) {
      this.$inertia.put(this.route('settings.content-items.sort'), {
        order,
      })
    },
    onDelete(id) {
      this.$inertia.delete(this.route('settings.content-items.destroy', [id]))
    },
    onCreate(type, data) {
      const route = this.route('settings.content-items.store', [type])
      this.$inertia.post(route, data)
    },
    onUpdate(id, data) {
      const route = this.route('settings.content-items.update', [id])
      this.$inertia.put(route, data)
    },
  },
})
</script>
