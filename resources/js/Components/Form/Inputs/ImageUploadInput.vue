<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <multi-image-uploader
      v-if="multi"
      v-model="value"
      :default-values="readValue"
    />
    <single-image-uploader v-else v-model="value" :image-url="readValue"/>
  </a-form-item>
</template>

<script>
import map from 'lodash/map'
import SingleImageUploader from '~/Components/SingleImageUploader'
import MultiImageUploader from '~/Components/MultiImageUploader'
import useField from '~/composables/useField'
import { InputProps } from './types'

export default {
  name: 'ImageUploadInput',
  props: {
    ...InputProps,
    multi: {
      type: Boolean,
      default: false,
    },
    previewName: {
      type: String,
    },
  },
  components: {
    SingleImageUploader,
    MultiImageUploader,
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)
    const { value: readValue } = useField(
      props.previewName || props.name.replace('_id', '')
    )
    let defaultValue
    if (props.multi) {
      defaultValue = map(readValue.value, (item) => ({
        uid: item.id,
        url: item.url,
      }))
    } else {
      defaultValue = readValue.value?.url
    }

    return {
      value,
      validateInfos,
      readValue: defaultValue,
    }
  },
}
</script>

<style scoped></style>
