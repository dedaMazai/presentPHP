<template>
  <div>
    <a-upload
      v-model:file-list="fileList"
      :action="route('builds.upload')"
      :data="uploadData"
      name="file"
      @change="handleChange"
      :showUploadList="!fileUrl"
    >
      <a-button>
        <upload-outlined></upload-outlined>
        Загрузить
      </a-button>
    </a-upload>
  </div>
</template>

<script>
import {UploadOutlined} from '@ant-design/icons-vue'

export default {
  components: {
    UploadOutlined,
  },
  props: {
    modelValue: String,
    type: String,
  },
  emits: ['update:modelValue'],
  data() {
    return {
      fileList: [],
      fileUrl: this.modelValue,
    }
  },
  methods: {
    uploadData() {
      return {
        _token: this.$page.props.csrf_token,
        type: this.type
      }
    },
    handleChange({fileList}) {
      this.fileList = fileList
      const url = fileList[0]?.response?.url
      this.fileUrl = url
      this.$emit('update:modelValue', url || null)
    },
  },
  computed: {},
}
</script>
