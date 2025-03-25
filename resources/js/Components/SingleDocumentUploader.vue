<template>
  <div>
    <a-upload
      v-model:file-list="fileList"
      :action="route('admin-documents.upload')"
      :before-upload="beforeUpload"
      :data="uploadData"
      list-type="text"
      name="document"
      @change="handleChange"
    >
      <template v-if="!fileList.length" style="border: 1px solid">
        <a-button type="dashed">Добавить</a-button>
      </template>
    </a-upload>
    <a-modal
      :footer="null"
      :visible="previewVisible"
      @cancel="handlePreviewCancel"
    >
      <img :src="previewUrl" alt="Preview" style="width: 100%" />
    </a-modal>
  </div>
</template>
<script>
import { PlusOutlined } from '@ant-design/icons-vue'

export default {
  components: {
    PlusOutlined,
  },
  props: {
    modelValue: Number,
    documentUrl: String,
    documentName: String,
    action: String
  },
  emits: ['update:modelValue', 'update:documentUrl'],
  data() {
    const fileList = []
    if (this.documentUrl) {
      fileList.push({
        uid: 1,
        url: this.documentUrl,
        name: this.documentName
      })
    }

    return {
      fileList,
      previewVisible: false,
      previewUrl: null,
    }
  },
  methods: {
    uploadData() {
      return {
        _token: this.$page.props.csrf_token
      }
    },
    handleChange({ fileList }) {
      this.fileList = fileList
      this.$emit('update:modelValue', fileList[0]?.response?.id || null)
      this.$emit('update:documentUrl', fileList[0]?.response?.url || null)
    },
    async handlePreview(file) {
      this.previewUrl = this.imageUrl
      this.previewVisible = true
    },
    handlePreviewCancel() {
      this.previewVisible = false
    },
    beforeUpload(file) {
      const isValidSize = file.size / 1024 / 1024 < 30
      const isValidTypes = [
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/vnd.ms-powerpoint",
        "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        "application/pdf"
      ]

      return new Promise((resolve, reject) => {
        if (isValidSize && isValidTypes.includes(file.type)) {
          resolve()
        } else {
          const errorMessage = 'Некорректный файл. Пожалуйста, загрузите новый.'
          this.$message.error(errorMessage)
          reject(new Error(errorMessage))
        }
      })
    },
  },
}
</script>
