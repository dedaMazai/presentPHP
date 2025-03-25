import { inject, toRef } from 'vue'

export default function useField(fieldName) {
  if (!fieldName) {
    throw `useField - fieldName: ${fieldName} не найден!`
  }

  const form = inject('$form')
  const formModel = inject('$formModel')
  const value = toRef(formModel, fieldName)
  const validateInfos = toRef(form.validateInfos, fieldName)

  return {
    value,
    validateInfos,
  }
}
