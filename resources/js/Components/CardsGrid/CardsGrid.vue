<template>
  <a-empty v-if="!dataSource.length" />
  <draggable
    :componentData="{ gutter: [16, 16] }"
    :list="dataSource"
    item-key="id"
    tag="a-row"
    v-bind="dragOptions"
    @end="drag = false"
    @sort="handleSort"
    @start="drag = true"
  >
    <template #item="{ element }">
      <a-col :span="6">
        <a-card hoverable size="small">
          <template v-if="$slots.cover" #cover>
            <div class="cards-grid-cover">
              <slot :item="element" name="cover"></slot>
            </div>
          </template>
          <template #actions class="ant-card-actions">
            <a-tooltip v-if="show" key="show" title="Просмотр">
              <inertia-link :href="showUrl(element.id)">
                <eye-outlined />
              </inertia-link>
            </a-tooltip>
            <a-tooltip key="edit" title="Редактировать">
              <inertia-link :href="editUrl(element.id)">
                <edit-outlined />
              </inertia-link>
            </a-tooltip>
            <a-tooltip key="delete" title="Удалить">
              <a-popconfirm
                cancel-text="Нет"
                ok-text="Да"
                title="Вы уверены, что хотите удалить запись?"
                @confirm="$inertia.delete(deleteUrl(element.id))"
              >
                <delete-outlined />
              </a-popconfirm>
            </a-tooltip>
          </template>
          <slot :item="element"></slot>
        </a-card>
      </a-col>
    </template>
  </draggable>
</template>

<script>
import { inject, ref, watch } from 'vue'
import Draggable from 'vuedraggable'
import { DeleteOutlined, EditOutlined, EyeOutlined } from '@ant-design/icons-vue'
import { Inertia } from '@inertiajs/inertia'
import isString from 'lodash/isString'
import DataGrid from '~/Components/DataGrid/DataGrid'

const route = window.route

export default {
  name: 'CardsGrid',
  components: {
    Draggable,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
    DataGrid
  },
  props: {
    extraParams: {
      type: Array,
      default: [],
    },
    show: {
      type: [String, Boolean],
    },
  },
  setup(props) {
    const resource = inject('$resource')
    const source = inject('$sourceName')
    const drag = ref(false)
    const dataSource = ref(resource.data)

    const showUrl = (id) => {
      return route(isString(props.show) ? props.show : source, [...props.extraParams, id])
    }

    const editUrl = (id) => {
      return route(`${source}.edit`, [...props.extraParams, id])
    }

    const deleteUrl = (id) => {
      return route(`${source}.destroy`, [...props.extraParams, id])
    }

    const handleSort = () => {
      resource.sortResource(dataSource.value.map((item) => item.id))
    }

    const updateDataSource = async () => {
      dataSource.value = resource.data
    }

    watch(resource.data, updateDataSource)

    return {
      dataSource,
      drag,
      dragOptions: {
        animation: 200,
        group: 'description',
        disabled: false,
        ghostClass: 'ghost',
      },
      showUrl,
      editUrl,
      deleteUrl,
      handleSort,
    }
  },
}
</script>

<style scoped>
.cards-grid-cover {
  width: 100%;
  height: 150px;
  overflow: hidden;
}

.cards-grid-cover >>> * {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>
