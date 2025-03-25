<template>
  <div>
    <a-upload
      v-model:file-list="fileList"
      :action="route('images.upload')"
      :before-upload="beforeUpload"
      :data="uploadData"
      list-type="picture-card"
      name="image"
      @change="handleChange"
      @preview="handlePreview"
    >
      <template v-if="!fileList.length">
        <plus-outlined />
        <div class="ant-upload-text">Загрузить</div>
      </template>
    </a-upload>
    <a-modal
      :footer="null"
      :visible="previewVisible"
      @cancel="handlePreviewCancel"
    >
      <img :src="previewUrl" alt="Preview image" style="width: 100%" />
    </a-modal>
  </div>
</template>
<script>
import { PlusOutlined } from '@ant-design/icons-vue'

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
  },
  props: {
    modelValue: Number,
    imageUrl: String,
  },
  emits: ['update:modelValue', 'update:imageUrl'],
  data() {
    const fileList = []
    if (this.imageUrl) {
      fileList.push({
        uid: 1,
        url: this.imageUrl,
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
      this.$emit('update:imageUrl', fileList[0]?.response?.url || null)
    },
    handlePreviewCancel() {
      this.previewVisible = false
    },
    async handlePreview(file) {
      if (!file.url && !file.preview) {
        file.preview = await getImageBase64(file.originFileObj)
      }
      this.previewUrl = this.imageUrl || file.preview
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
  },
}
</script>
