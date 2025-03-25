<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <file-uploader v-model="value" />
    <p>
      <a v-if="value" :href="value">{{ linkName }}</a>
    </p>
  </a-form-item>
</template>

<script>
import FileUploader from '~/Components/FileUploader'
import useField from '~/composables/useField'
import { InputProps } from './types'

export default {
  name: 'FileUploadInput',
  props: {
    ...InputProps,
    linkName: {
      type: String,
      default: 'Скачать файл',
    },
  },
  components: { FileUploader },
  setup(props) {
    const { value, validateInfos } = useField(props.name)
    return {
      value,
      validateInfos,
    }
  },
}
</script>

<style scoped></style>
