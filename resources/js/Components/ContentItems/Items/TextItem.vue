<template>
  <a-form ref="form" :model="form" :rules="rules" layout="vertical">
    <a-space direction="vertical" :style="{ width: '100%' }">
      <a-form-item ref="textInput" name="text" style="margin-bottom: 10px">
        <a-textarea
          v-model:value="form.text"
          placeholder="Basic usage"
          :rows="4"
        />
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
        text: this.value,
      },
      rules: {
        text: [{ required: true, message: 'Введите текст', trigger: 'blur' }],
      },
    }
  },
  computed: {
    changed() {
      return this.form.text !== this.value
    },
  },
  methods: {
    onCancel() {
      this.form.text = this.value
      this.$refs.form.resetFields()
    },
    onSubmit() {
      this.$refs.form
        .validate()
        .then(() => {
          this.$emit('save', this.form.text)
        })
        .catch((error) => {
          console.log('error', error)
        })
    },
  },
}
</script>
