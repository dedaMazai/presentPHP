<template>
  <page-wrapper title="Расположения баннеров">
    <a-row :gutter="16">
      <a-col v-for="(place, key) in places" :key="key" :span="6">
        <a-card hoverable @click="$inertia.get(route(`banners`, [key]))">
          <a-typography-link strong>{{ place }}</a-typography-link>
        </a-card>
      </a-col>
    </a-row>
  </page-wrapper>
</template>

<script>
import BasicLayout from '~/Layouts/BasicLayout'

export default {
  name: 'Places',
  layout: BasicLayout,
  props: {
    places: {
      type: Object,
      default: [],
    },
  },
}
</script>

<style scoped></style>
