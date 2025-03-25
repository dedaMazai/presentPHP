<template>
  <a-form ref="form" :model="form" :rules="rules" layout="vertical">
    <a-space direction="vertical" :style="{ width: '100%' }">
      <a-form-item name="url">
        <a-input v-model:value="form.url" placeholder="Введите URL" />
      </a-form-item>
      <a-form-item>
        <a-space>
          <a-button v-if="!value || changed" type="primary" @click="onSubmit">
            Сохранить
          </a-button>
          <a-button v-if="value && changed" type="default" @click="onCancel">
            Отменить
          </a-button>
        </a-space>
      </a-form-item>
    </a-space>
  </a-form>
</template>

<script>
export default {
  props: {
    value: String,
  },
  emits: ['save'],
  data() {
    return {
      form: {
        url: this.value,
      },
      rules: {
        url: [{ required: true, message: 'Введите URL', trigger: 'blur' }],
      },
    }
  },
  computed: {
    changed() {
      return this.form.url !== this.value
    },
  },
  methods: {
    onCancel() {
      this.form.url = this.url
      this.$refs.form.resetFields()
    },
    onSubmit() {
      this.$refs.form
        .validate()
        .then(() => {
          this.$emit('save', this.form.url)
        })
        .catch((error) => {
          console.log('error', error)
        })
    },
  },
}
</script>
