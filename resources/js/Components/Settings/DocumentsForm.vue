<template>
  <simple-form :initial-values="values" :rules="rules" @on-submit="onSubmit">
    <file-upload-input
      label="Оферта"
      name="offer_url"
      link-name="Скачать документ офферты"
    />
    <file-upload-input
      label="Пользовательское соглашение"
      name="consent_url"
      link-name="Скачать документ согласия"
    />
    <file-upload-input
      label="Шаблон доверенности (для приемки)"
      name="confidant_url"
      link-name="Скачать документ шаблона доверенности"
    />
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import FileUploadInput from '~/Components/Form/Inputs/FileUploadInput'
import { message } from 'ant-design-vue'

const defaultValues = {
  offer_url: null,
  consent_url: null,
  confidant_url: null,
}

export default defineComponent({
  name: 'DocumentsForm',
  components: {
    SimpleForm,
    TextInput,
    FileUploadInput,
  },
  props: {
    settings: Object,
  },
  data() {
    return {
      values: { ...defaultValues, ...this.settings },
      rules: {
        offer_url: [{ required: true, message: 'Заполните поле' }],
        consent_url: [{ required: true, message: 'Заполните поле' }],
      },
    }
  },
  methods: {
    onSubmit(data) {
      this.$inertia.put(this.route('settings.documents.update'), data, {
        onFinish: () => message.success('Настройки сохранены.'),
      })
    },
  },
})
</script>
