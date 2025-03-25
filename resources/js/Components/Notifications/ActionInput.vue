<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <p v-if="readonly">{{ selectedActionLabel }}</p>
    <a-select v-else v-model:value="value.type">
      <a-select-option
        v-for="item in actionTypes"
        :key="item.name"
        :value="item.name"
      >
        {{ item.label }}
      </a-select-option>
    </a-select>
    <template v-if="value.type && !isEmpty(value.payload)">
      <div v-for="item in value.payload" :key="item.name">
        <a-form-item v-if="item.optionsMapper" :label="item.label">
          <p v-if="readonly">{{ actionPayloadLabel(item) }}</p>
          <a-select
            v-else
            v-model:value="item.value"
            :mode="item.type === 'array' ? 'multiple' : ''"
            ><a-select-option
              v-for="item in actionsPayloadMap[value.type][item.name]"
              :key="item.key"
              :value="item.key"
            >
              {{ item.label }}
            </a-select-option>
          </a-select>
        </a-form-item>
        <a-form-item v-else-if="item.type === 'integer'" :label="item.label">
          <p v-if="readonly">{{ item.value }}</p>
          <a-input-number v-else v-model:value="item.value"></a-input-number>
        </a-form-item>
        <a-form-item v-else :label="item.label">
          <p v-if="readonly">{{ item.value }}</p>
          <a-input v-else v-model:value="item.value"></a-input>
        </a-form-item>
      </div>
    </template>
  </a-form-item>
</template>

<script>
import { computed, onMounted, toRefs, watch } from 'vue'
import find from 'lodash/find'
import cloneDeep from 'lodash/cloneDeep'
import forEach from 'lodash/forEach'
import isEmpty from 'lodash/isEmpty'
import isArray from 'lodash/isArray'
import isObject from 'lodash/isObject'
import useField from '~/composables/useField'
import { InputProps } from '~/Components/Form/Inputs/types'

export default {
  name: 'ActionInput',
  props: {
    ...InputProps,
    actionTypes: Array,
    actionsPayloadMap: Object,
    destinationType: String,
    readonly: {
      type: Boolean,
      default: false,
    },
    notificationType: String,
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)
    const { destinationType } = toRefs(props)

    const actionType = computed(() => {
      return value.value
        ? find(props.actionTypes, { name: value.value.type })
        : ''
    })
    const hasMapper = computed(() => {
      return !!(
        actionType.value && actionType.value.payloadParams[0].optionsMapper
      )
    })
    const hasPayload = computed(() => {
      return !!(actionType.value && !isEmpty(actionType.value.payloadParams))
    })

    const selectedActionLabel = computed(() => {
      return actionType.value ? actionType.value.label : ''
    })
    const actionParams = computed(() => {
      if (hasPayload) {
        const payloadArr = cloneDeep(actionType.value.payloadParams)
        if (props.readonly) {
          if (isArray(value.value.payload)) {
            return value.value.payload
          } else if (isObject(value.value.payload)) {
            forEach(payloadArr, (item) => {
              item.value = value.value.payload[item.name]
            })
          }
        } else {
          forEach(payloadArr, (item) => {
            item.value = item.type === 'array' ? [] : null
          })
        }
        return payloadArr
      } else return []
    })

    watch(destinationType, (newValue, oldValue) => {
      if (newValue !== oldValue) {
        value.value.type = null
        value.value.payload = null
      }
    })

    watch(actionType, (newValue, oldValue) => {
      if (
        value.value &&
        newValue &&
        (!oldValue || newValue.name !== oldValue.name)
      ) {
        value.value.payload = actionParams.value
      }
    })

    if (props.readonly) {
      value.value = { type: value.value.type, payload: actionParams.value }
    }

    onMounted(() => {
      if (!props.readonly) {
        value.value.type = null
        value.value.payload = []
      }
    })

    const actionPayloadLabel = (payload) => {
      if (isArray(payload.value)) {
        const labelArr = payload.value.map((item) => {
          const payloadObj = find(
            props.actionsPayloadMap[value.value.type][payload.name],
            { key: item }
          )
          return payloadObj ? payloadObj.label : ''
        })
        return labelArr.join(', ')
      } else {
        const payloadObj = find(
          props.actionsPayloadMap[value.value.type][payload.name],
          { key: payload.value }
        )
        return payloadObj ? payloadObj.label : ''
      }
    }

    return {
      value,
      validateInfos,
      selectedActionLabel,
      hasMapper,
      actionParams,
      actionPayloadLabel,
      isEmpty,
    }
  },
}
</script>

<style scoped></style>
