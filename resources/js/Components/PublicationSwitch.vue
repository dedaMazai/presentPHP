<template>
  <a-select
    v-model:value="currentStatus"
    @change="$emit('update:modelValue', currentStatus)"
  >
    <template
      v-if="showBadge"
      #suffixIcon
    >
      <a-badge :status="badgeStatus"/>
    </template>
    <a-select-option
      v-for="item in statuses"
      :key="item.key"
      :value="item.key"
    >
      {{ item.label }}
    </a-select-option>
  </a-select>
</template>

<script>
export default {
  props: {
    modelValue: Boolean,
    showBadge: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      currentStatus: this.modelValue,
      statuses: [
        {
          key: false,
          label: 'Не опубликовано'
        },
        {
          key: true,
          label: 'Опубликовано'
        }
      ]
    }
  },
  computed: {
    badgeStatus: function () {
      switch (this.currentStatus) {
        case true:
          return 'success'
        default:
          return 'default'
      }
    }
  }
}
</script>

<style scoped>
.ant-badge-status {
  margin-top: -3px;
}
</style>
