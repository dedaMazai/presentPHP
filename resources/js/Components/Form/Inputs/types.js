export const InputProps = {
  name: {
    type: String,
    required: true
  },
  label: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  is_published: {
    type: Boolean,
    default: false,
  },
}

export const SelectProps = {
  ...InputProps,
  resource: {
    type: String,
  },
  options: {
    type: Object,
  },
  valueFieldId: {
    type: String,
    default: 'key',
  },
  labelFieldId: {
    type: String,
    default: 'label',
  },
  mode: {
    type: String,
    default: 'default'
  },
}
