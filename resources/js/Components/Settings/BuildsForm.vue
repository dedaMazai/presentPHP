<template>
  <simple-form :initial-values="values" :rules="rules" @on-submit="onSubmit">
    <build-upload-input
      label="Android"
      name="build_android_url"
      link-name="Скачать сборку Android"
      type="android"
    />
    <build-upload-input
      label="iOS"
      name="build_ios_url"
      link-name="Скачать сборку iOS"
      type="ios"
    />
  </simple-form>
</template>

<script>
import {defineComponent} from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import BuildUploadInput from '~/Components/Form/Inputs/BuildUploadInput'
import {message} from 'ant-design-vue'

const defaultValues = {
  build_android_url: null,
  build_ios_url: null,
}

export default defineComponent({
  name: 'BuildsForm',
  components: {
    SimpleForm,
    TextInput,
    BuildUploadInput,
  },
  props: {
    settings: Object,
  },
  data() {
    return {
      values: {...defaultValues, ...this.settings}
    }
  },
  methods: {
    onSubmit(data) {
      this.$inertia.put(this.route('settings.builds.update'), data, {
        onFinish: () => message.success('Сборки сохранены.'),
      })
    },
  },
})
</script>
