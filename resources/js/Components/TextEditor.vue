<template>
  <div>
    <ckeditor
      v-model="content"
      :config="editorConfig"
      :editor="editor"
      @blur="$emit('blur')"
    />
  </div>
</template>

<script>
import InlineEditor from '@ckeditor/ckeditor5-build-inline'
import '@ckeditor/ckeditor5-build-inline/build/translations/ru'

const editorConfig = {
  language: 'ru',
  toolbar: ['heading', '|', 'bold', 'italic', 'link', 'numberedList', 'bulletedList']
}

export default {
  props: {
    modelValue: {
      type: String,
      default: '',
      required: true
    }
  },
  emits: ['update:modelValue', 'blur'],
  data() {
    return {
      editor: InlineEditor,
      content: this.modelValue,
      editorConfig
    }
  },
  watch: {
    modelValue(value) {
      this.content = value
    },
    content() {
      this.$emit('update:modelValue', this.content)
    }
  }
}
</script>

<style scoped>
.ck-content {
  transition: all 0.3s;
  line-height: 1.5715;
}

.ck-content:not(.ck-focused) {
  border: 1px solid hsla(0, 0%, 0%, 0.15);
}

.ck-content:hover {
  border-color: #40a9ff;
  border-right-width: 1px;
}

.ck.ck-editor__editable:not(.ck-editor__nested-editable).ck-focused {
  border-color: #40a9ff;
  border-right-width: 1px;
  outline: 0;
  box-shadow: 0 0 0 2px rgb(24 144 255 / 20%);
}

.has-error .ck-content,
.has-error .ck-content:hover {
  border-color: #ff4d4f;
}

.has-error .ck.ck-editor__editable:not(.ck-editor__nested-editable).ck-focused {
  border-color: #ff7875;
  border-right-width: 1px !important;
  outline: 0;
  box-shadow: 0 0 0 2px rgb(255 77 79 / 20%);
}
</style>
