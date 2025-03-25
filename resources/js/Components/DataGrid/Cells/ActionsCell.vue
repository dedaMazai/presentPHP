<template>
  <span>
    <inertia-link :href="showUrl" v-if="show">
      Смотреть
    </inertia-link>

    <inertia-link :href="editUrl" v-if="!showOnly">
      Редактировать
    </inertia-link>

    <template v-if="!editOnly && !showOnly">
      <a-divider type="vertical" />

      <inertia-link method="post" :href="copyUrl" v-if="copyUrl">
      Копировать
      </inertia-link>


      <a-divider type="vertical" />
      <a-popconfirm
        cancel-text="Нет"
        ok-text="Да"
        placement="bottomRight"
        title="Вы уверены, что хотите удалить запись?"
        @confirm="$inertia.delete(deleteUrl)"
      >
        <a>Удалить</a>
      </a-popconfirm>
    </template>
  </span>
</template>

<script>
import { defineComponent, inject } from 'vue'
import { CellProps } from './types'

const route = window.route

export default defineComponent({
  name: 'ActionsCell',
  props: {
    ...CellProps,
    extraParams: {
      type: Array,
      default: [],
    },
    show: {
      type: Boolean,
      default: false,
    },
    editOnly: {
      type: Boolean,
      default: false,
    },
    showOnly: {
      type: Boolean,
      default: false,
    },
  },
  setup(props) {
    const source = inject('$sourceName')
    return {
      source,
      showUrl: props.show && !props.editOnly
        ? route(`${source}.show`, [...props.extraParams, props.record.id])
        : '',

      editUrl: props.showOnly
        ? ''
        : route(`${source}.edit`, [...props.extraParams, props.record.id]),

      copyUrl: source === 'news'
        ? route(`${source}.copy`, [...props.extraParams, props.record.id])
        : '',

      deleteUrl: props.editOnly || props.showOnly
        ? ''
        : route(`${source}.destroy`, [...props.extraParams, props.record.id]),
    }
  },
})
</script>
