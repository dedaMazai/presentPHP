<template>
  <a-form ref="form" :model="form" :rules="rules" layout="vertical">
    <a-form-item name="document_id">
      <single-document-uploader
        :key="uploaderKey"
        v-model="form.document_id"
        :document-url="document?.url"
        :document-name="document?.name"
      />
    </a-form-item>
    <br>
    <a-space>
      <a-button v-if="!document || changed" type="primary" @click="onSubmit">
        Сохранить
      </a-button>
      <a-button v-if="document && changed" type="default" @click="onCancel">
        Отменить
      </a-button>
    </a-space>
  </a-form>
</template>

<script>
import SingleDocumentUploader from '~/Components/SingleDocumentUploader'

export default {
  components: {
    SingleDocumentUploader,
  },
  props: {
    document: Object,
  },
  emits: ['save'],
  data() {
    return {
      uploaderKey: 0,
      form: {
        document_id: this.document?.id,
      },
      rules: {
        document_id: [
          {
            required: true,
            type: 'integer',
            message: 'Загрузите документ',
            trigger: 'blur',
          },
        ],
      },
    }
  },
  computed: {
    changed() {
      return this.form.document_id !== this.document.id
    },
  },
  methods: {
    onSubmit() {
      this.$refs.form
        .validate()
        .then(() => {
          this.$emit('save', this.form.document_id)
        })
        .catch((error) => {
          console.log('error', error)
        })
    },
    onCancel() {
      this.uploaderKey += 1
      this.form.document_id = this.document?.id
    },
  },
}
</script>
