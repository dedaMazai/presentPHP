<template>
  <a-form ref="form" :model="form" :rules="rules" layout="vertical">
    <a-form-item name="image_id">
      <single-image-uploader
        :key="uploaderKey"
        v-model="form.image_id"
        :image-url="image?.url"
      />
    </a-form-item>
    <a-space>
      <a-button v-if="!image || changed" type="primary" @click="onSubmit">
        Сохранить
      </a-button>
      <a-button v-if="image && changed" type="default" @click="onCancel">
        Отменить
      </a-button>
    </a-space>
  </a-form>
</template>

<script>
import SingleImageUploader from '~/Components/SingleImageUploader'

export default {
  components: {
    SingleImageUploader,
  },
  props: {
    image: Object,
  },
  emits: ['save'],
  data() {
    return {
      uploaderKey: 0,
      form: {
        image_id: this.image?.id,
      },
      rules: {
        image_id: [
          {
            required: true,
            type: 'integer',
            message: 'Загрузите изображение',
            trigger: 'blur',
          },
        ],
      },
    }
  },
  computed: {
    changed() {
      return this.form.image_id !== this.image?.id
    },
  },
  methods: {
    onSubmit() {
      this.$refs.form
        .validate()
        .then(() => {
          this.$emit('save', this.form.image_id)
        })
        .catch((error) => {
          console.log('error', error)
        })
    },
    onCancel() {
      this.uploaderKey += 1
      this.form.image_id = this.image?.id
    },
  },
}
</script>
