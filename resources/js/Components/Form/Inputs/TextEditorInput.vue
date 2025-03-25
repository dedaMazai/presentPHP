<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <text-editor v-model="value" />
  </a-form-item>
</template>

<script>
import { inject, watch } from 'vue'
import useField from '~/composables/useField'
import TextEditor from '~/Components/TextEditor.vue'
import { InputProps } from './types'

export default {
  name: 'TextEditorInput',
  components: {
    TextEditor,
  },
  props: {
    ...InputProps,
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)
    const formModel = inject('$formModel')
    watch(value, (newValue) => {
      if (newValue === '') {
        formModel[props.name] = null
      }
    })
    return {
      value,
      validateInfos,
    }
  },
}
</script>
