<template>
  <a-select v-model:value="modelValue" class="select-cell" @change="postAction">
    <a-select-option v-for="item in options" :key="item.key" :value="item.key">
      {{ item.label }}
    </a-select-option>
  </a-select>
</template>

<script>
import {defineComponent, ref, toRefs} from 'vue'
import {Inertia} from '@inertiajs/inertia'
import {CellProps} from './types'
import useDictionary from '~/composables/useDictionary'

const route = window.route

export default defineComponent({
  name: 'SelectCell',
  props: {
    ...CellProps,
    resource: {
      type: String,
    },
    options: {
      type: Array,
    },
    actionUrl: {
      type: String,
    },
    extraParams: {
      type: Array,
      default: [],
    },
  },
  setup(props) {
    const {name, value, record, resource, actionUrl} = toRefs(props)
    const modelValue = ref(value)
    const dictionary = useDictionary(resource.value)

    const postAction = (val) => {
      Inertia.post(
        route(actionUrl.value, [...props.extraParams, record.value.id]),
        {
          [name.value]: val,
        }
      )
    }

    return {
      modelValue,
      options: props.options || dictionary.data,
      postAction,
    }
  },
})
</script>

<style scoped>
.select-cell {
  width: 100%;
}
</style>
