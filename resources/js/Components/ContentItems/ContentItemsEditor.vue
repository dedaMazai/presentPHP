<template>
  <page-wrapper :title="title">
    <a-card :bordered="false">
      <a-row justify="center" type="flex">
        <a-col :span="14">
          <draggable
            :list="items"
            class="list-group"
            handle=".drag"
            item-key="id"
            @sort="onSort"
          >
            <template #item="{ element: item, index }">
              <a-row class="content-item">
                <a-col :class="{ drag: !isNewItem(item) }" :span="1">
                  <drag-outlined v-if="!isNewItem(item)" />
                </a-col>
                <a-col :span="22">
                  <text-item
                    v-if="item.type === 'text'"
                    :value="item.text"
                    @save="(data) => onItemSaved(item.id, item.type, data)"
                  />
                  <title-item
                    v-else-if="item.type === 'title1lvl'"
                    :level="1"
                    :value="item.text"
                    @save="(data) => onItemSaved(item.id, item.type, data)"
                  />
                  <title-item
                    v-else-if="item.type === 'title2lvl'"
                    :level="2"
                    :value="item.text"
                    @save="(data) => onItemSaved(item.id, item.type, data)"
                  />
                  <title-item
                    v-else-if="item.type === 'title3lvl'"
                    :level="3"
                    :value="item.text"
                    @save="(data) => onItemSaved(item.id, item.type, data)"
                  />
                  <video-item
                    v-else-if="item.type === 'video'"
                    :value="item.video_url"
                    @save="(data) => onItemSaved(item.id, item.type, data)"
                  />
                  <image-item
                    v-else-if="item.type === 'image'"
                    :image="item.image"
                    @save="(data) => onItemSaved(item.id, item.type, data)"
                  />
                  <number-list-item
                    v-else-if="item.type === 'numbered_list'"
                    :value="item.content"
                    @save="(data) => onItemSaved(item.id, item.type, data)"
                  />
                  <un-number-list-item
                    v-else-if="item.type === 'unnumbered_list'"
                    :value="item.content"
                    @save="(data) => onItemSaved(item.id, item.type, data)"
                  />
                  <factoids-list-item
                    v-else-if="item.type === 'factoids'"
                    :value="item.content"
                    @save="(data) => onItemSaved(item.id, item.type, data)"
                  />
                  <document-item
                    v-else-if="item.type === 'document'"
                    :document="item.document"
                    @save="(data) => onItemSaved(item.id, item.type, data)"
                  />
                </a-col>
                <a-col :span="1" :style="{ 'text-align': 'right' }">
                  <a-button class="drag" type="link" @click="remove(index)">
                    <template #icon>
                      <close-outlined
                        :style="{ color: 'rgba(0, 0, 0, 0.85)' }"
                      />
                    </template>
                  </a-button>
                </a-col>
              </a-row>
            </template>
          </draggable>
        </a-col>
      </a-row>
      <a-row v-if="!hasNew" justify="center" type="flex">
        <a-col :span="4">
          <a-dropdown style="margin-top: 20px">
            <template #overlay>
              <a-menu @click="({ key }) => createNewItem(key)">
                <a-menu-item
                  v-for="itemBlock in itemBlocks"
                  :key="itemBlock.type"
                >
                  {{ itemBlock.name }}
                </a-menu-item>
              </a-menu>
            </template>
            <a-button :block="true" type="dashed">
              <plus-outlined />
              Добавить блок
            </a-button>
          </a-dropdown>
        </a-col>
      </a-row>
    </a-card>
  </page-wrapper>
</template>

<script>
import draggable from 'vuedraggable'
import {
  CloseOutlined,
  DragOutlined,
  PlusOutlined,
} from '@ant-design/icons-vue'
import TextItem from './Items/TextItem.vue'
import TitleItem from './Items/TitleItem'
import NumberListItem from './Items/NumberListItem'
import UnNumberListItem from './Items/UnNumberListItem'
import VideoItem from './Items/VideoItem.vue'
import ImageItem from './Items/ImageItem'
import FactoidsListItem from './Items/FactoidsListItem'
import DocumentItem from './Items/DocumentItem'

export default {
  components: {
    TextItem,
    TitleItem,
    NumberListItem,
    UnNumberListItem,
    VideoItem,
    ImageItem,
    FactoidsListItem,
    DocumentItem,
    DragOutlined,
    CloseOutlined,
    PlusOutlined,
    draggable,
  },
  props: {
    contentItems: Array,
  },
  emits: ['sort', 'delete', 'update', 'create'],
  data(props) {
    const title = props.title || `Редактирование контента`

    return {
      items: this.contentItems,
      itemBlocks: [
        {
          name: 'Текст',
          type: 'text',
        },
        {
          name: 'Заголовок 1-го уровня',
          type: 'title1lvl',
        },
        {
          name: 'Заголовок 2-го уровня',
          type: 'title2lvl',
        },
        {
          name: 'Заголовок 3-го уровня',
          type: 'title3lvl',
        },
        {
          name: 'Нумерованный список',
          type: 'numbered_list',
        },
        {
          name: 'Ненумерованный список',
          type: 'unnumbered_list',
        },
        {
          name: 'Видео',
          type: 'video',
        },
        {
          name: 'Изображение',
          type: 'image',
        },
        {
          name: 'Фактойды',
          type: 'factoids',
        },
        {
          name: 'Файл',
          type: 'document',
        },
      ],
      title,
    }
  },
  computed: {
    hasNew() {
      return this.items.find((item) => this.isNewItem(item))
    },
  },
  watch: {
    contentItems() {
      this.items = this.contentItems
    },
  },
  methods: {
    isNewItem(item) {
      return !item.id
    },
    onSort() {
      this.$emit(
        'sort',
        this.items.map((item) => item.id)
      )
    },
    remove(index) {
      if (this.isNewItem(this.items[index])) {
        this.items.splice(index, 1)
        return
      }

      const { id } = this.items[index]
      this.$emit('delete', id)
    },
    onItemSaved(id, type, data) {
      if (id) {
        this.$emit('update', id, this.prepareItemSaveRequestBody(type, data))
      } else {
        this.$emit('create', type, this.prepareItemSaveRequestBody(type, data))
      }
    },
    prepareItemSaveRequestBody(type, data) {
      switch (type) {
        case 'text':
          return {
            text: data,
          }
        case 'video':
          return {
            video_url: data,
          }
        case 'image':
          return {
            image_id: data,
          }
        case 'title1lvl':
          return {
            text: data,
          }
        case 'title2lvl':
          return {
            text: data,
          }
        case 'title3lvl':
          return {
            text: data,
          }
        case 'numbered_list':
          return {
            content: data,
          }
        case 'unnumbered_list':
          return {
            content: data,
          }
        case 'factoids':
          return {
            content: data,
          }
        case 'document':
          return {
            document_id: data,
          }
      }
    },
    createNewItem(type) {
      this.items.push({ type })
    },
  },
}
</script>
