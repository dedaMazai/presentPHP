<template>
  <simple-form
    :initial-values="initialValues"
    class="filter-container"
    layout="vertical"
    filters
    @on-submit="$emit('onSubmit', $event)"
  >
    <template #default>
      <slot></slot>
    </template>
    <template #actions="{ resetFields }">
      <a-affix :offset-bottom="10">
        <a-form-item>
          <a-row :gutter="10">
            <a-col :span="12">
              <a-button block htmlType="submit" type="primary">Найти</a-button>
            </a-col>
            <a-col :span="12">
              <a-button block @click="resetFields(true)">Сбросить</a-button>
            </a-col>
          </a-row>
        </a-form-item>
      </a-affix>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from './Form/SimpleForm'

export default defineComponent({
  name: 'Filters',
  components: { SimpleForm },
  props: {
    initialValues: {
      type: Object,
    },
  },
  emits: ['onSubmit'],
  setup() {
    const handleReset = () => {}

    return {
      handleReset,
    }
  },
})
</script>

<style scoped>
.filter-container {
  margin-bottom: 1rem;
}

.filter-block {
  margin-bottom: 1rem;
}

.filter-cancel-btn {
  margin-left: 10px;
}
</style>
