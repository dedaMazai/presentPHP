<template>
  <div>
    <div>
      <draggable
        class="multi-image-uploader"
        :list="fileList"
        item-key="uid"
        v-bind="dragOptions"
        @start="drag = true"
        @end="drag = false"
        @sort="handleEmitChange"
      >
        <template #item="{ element }">
          <div
            class="multi-image-uploader__file"
            @click="handlePreview(element)"
          >
            <div
              class="multi-image-uploader__file-remove"
              @click.prevent.stop="handleRemove(element.uid)"
            >
              <close-outlined />
            </div>
            <img
              v-if="element.url || element.response"
              :src="element.url || element.response?.url"
              :alt="element.name"
            />
            <a-spin v-else />
          </div>
        </template>
      </draggable>
      <a-upload
        name="image"
        list-type="picture-card"
        v-model:file-list="fileList"
        :action="route('images.upload')"
        :data="uploadData"
        :multiple="true"
        :showUploadList="false"
        @change="handleChange"
      >
        <template v-if="true">
          <plus-outlined />
          <div class="ant-upload-text">Загрузить</div>
        </template>
      </a-upload>
    </div>
    <a-modal
      :footer="null"
      :visible="previewVisible"
      @cancel="handlePreviewCancel"
    >
        <img v-for="element in fileList"
             :src="element?.url || element?.response?.url"
             :alt="element.name"
             style="width: 100%"
        />
    </a-modal>
  </div>
</template>
<script>
import Draggable from 'vuedraggable'
import { PlusOutlined, CloseOutlined } from '@ant-design/icons-vue'
import map from 'lodash/map'
import filter from 'lodash/filter'

function getImageBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.readAsDataURL(file)

    reader.onload = () => resolve(reader.result)

    reader.onerror = (error) => reject(error)
  })
}

export default {
  components: {
    PlusOutlined,
    CloseOutlined,
    Draggable,
  },
  props: {
    modelValue: Number,
    imageUrl: String,
    defaultValues: {
      type: Array,
      default: []
    }
  },
  emits: ['update:modelValue', 'update:imageUrl'],
  data(props) {
    return {
      fileList: props.defaultValues,
      previewVisible: false,
      previewUrl: null,
      drag: false,
      dragOptions: {
        animation: 200,
        ghostClass: 'ghost',
      },
    }
  },
  methods: {
    uploadData() {
      return {
        _token: this.$page.props.csrf_token
      }
    },
    handleChange() {
      this.handleEmitChange()
    },
    handlePreviewCancel() {
      this.previewVisible = false
    },
    async handlePreview(file) {
      if (!file.url && !file.preview) {
        file.preview = await getImageBase64(file.originFileObj)
      }
      this.previewUrl = file.response?.url || file.preview
      this.previewVisible = true
    },
    beforeUpload(file) {
      const isValidSize = file.size / 1024 / 1024 < 2

      return new Promise((resolve, reject) => {
        if (isValidSize) {
          resolve()
        } else {
          const errorMessage = 'Изображение должно быть меньше 2Мб'
          this.$message.error(errorMessage)
          reject(new Error(errorMessage))
        }
      })
    },
    handleEmitChange() {
      const images = map(this.fileList, 'response.id')
      this.$emit('update:modelValue', images)
    },
    handleRemove(uid) {
      this.fileList = filter(this.fileList, (file) => file.uid !== uid)
      this.handleEmitChange()
    },
  },
}
</script>

<style scoped>
.multi-image-uploader {
  display: flex;
  justify-content: start;
  align-items: center;
  flex-wrap: wrap;
}
.multi-image-uploader >>> .ant-upload-picture-card-wrapper {
  width: auto;
}

.multi-image-uploader__file {
  position: relative;
  width: 104px;
  height: 104px;
  overflow: hidden;
  margin-right: 8px;
  margin-bottom: 8px;
  text-align: center;
  vertical-align: top;
  background-color: #fafafa;
  border: 1px solid #d9d9d9;
  border-radius: 2px;
  cursor: pointer;
  flex-shrink: 0;
  padding: 8px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.multi-image-uploader__file >>> img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.multi-image-uploader__file-remove {
  position: absolute;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #ffffff;
  color: #cf1322;
  border: 2px solid #cf1322;
  border-radius: 50%;
  width: 16px;
  height: 16px;
  top: 2px;
  right: 2px;
  font-size: 9px;
}
</style>
