<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <build-uploader v-model="value" :type="type"/>
    <p>
      <a v-if="value" :href="value">{{ linkName }}</a>
    </p>
  </a-form-item>
</template>

<script>
import BuildUploader from '~/Components/BuildUploader'
import TextInput from '~/Components/Form/Inputs/TextInput'
import useField from '~/composables/useField'
import {InputProps} from './types'

export default {
  name: 'BuildUploadInput',
  props: {
    ...InputProps,
    linkName: {
      type: String,
      default: 'Скачать файл',
    },
    type: String,
  },
  components: {
    BuildUploader,
    TextInput
  },
  setup(props) {
    const {value, validateInfos} = useField(props.name)
    return {
      value,
      validateInfos,
    }
  },
}
</script>

<style scoped></style>
