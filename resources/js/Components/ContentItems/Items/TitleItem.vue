<template>
  <a-form ref="form" :model="form" :rules="rules" layout="vertical">
    <a-space direction="vertical" :style="{ width: '100%' }">
      <a-form-item name="title">
        <a-input v-model:value="form.title" :class="textClass" />
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
    level: Number,
    value: String,
  },
  emits: ['save'],
  data() {
    return {
      form: {
        title: this.value,
      },
      rules: {
        title: [
          { required: true, message: 'Введите заголовок', trigger: 'blur' },
        ],
      },
    }
  },
  computed: {
    changed() {
      return this.form.title !== this.value
    },
    textClass() {
      switch (this.level) {
        case 1:
          return 'header_one'
        case 2:
          return 'header_two'
        case 3:
          return 'header_three'
      }
    },
  },
  methods: {
    onCancel() {
      this.form.title = this.title
      this.$refs.form.resetFields()
    },
    onSubmit() {
      this.$refs.form
        .validate()
        .then(() => {
          this.$emit('save', this.form.title)
        })
        .catch((error) => {
          console.log('error', error)
        })
    },
  },
}
</script>

<style scoped>
.header_one {
  font-size: 24px;
  font-weight: bold;
}
.header_two {
  font-size: 20px;
  font-weight: bold;
}
.header_three {
  font-size: 16px;
  font-weight: bold;
}
</style>
