<template>
  <simple-form
    :initial-values="values"
    :rules="rules"
    @on-field-change="changeFormValues"
    @on-submit="onSubmit"
    :readonly="show"
  >
    <template #default="{ model }">
      <text-input
        label="Заголовок"
        name="title"
        :readonly="show"
      ></text-input>
      <text-area-input
        label="Текст"
        name="text"
        :readonly="show"
      ></text-area-input>
      <select-input
        label="Тип"
        name="type"
        resource="types"
        :readonly="show"
      ></select-input>
      <select-input
        label="Аудитория"
        name="destination_type"
        :options="destinationTypes"
        value-field-id="name"
        :readonly="show"
      ></select-input>
      <a-form-item label="Детализация" v-if="model.destination_type === 'owners_by_uk_projects'">
        <p v-if="show">{{ audienceSpecificationLabel(model.destination_type_payload["audience_specification"]) }}</p>
        <radio-group-input
          v-else
          :options="NOTIFICATION_AUDIENCE_SPECIFICATION"
          name="audience_specification"
        ></radio-group-input>
      </a-form-item>
      <template v-if="typeHasPayload">
        <div v-for="item in destinationPayloadArray" :key="item.name">
          <div v-if="item.optionsMapper">
            <a-form-item :label="item.label">
              <p v-if="show">{{ destinationPayloadLabel(item) }}</p>
              <a-select
                v-else
                :name="item.name"
                v-model:value="item.value"
                :mode="item.type === 'array' ? item.name === 'uk_project_ids' && model.audience_specification === 'uk_and_building' ? '' : 'multiple' : ''"
              ><a-select-option
                v-for="item in destinationTypesPayloadMap[
                    model.destination_type
                  ][item.name]"
                :key="item.key"
                :value="item.key"
              >
                {{ item.label }}
              </a-select-option>
              </a-select>
            </a-form-item>
            <a-form-item v-if="(isArray(destinationPayloadArray[0].value) &&
                  Object.keys(destinationPayloadArray[0].value).length ||
                  !isArray(destinationPayloadArray[0].value) && destinationPayloadArray[0].value) &&
                  model.destination_type === 'owners_by_uk_projects' &&
                  (model.audience_specification === 'uk_and_building' ||
                   model.destination_type_payload['audience_specification'] === 'uk_and_building')">
              <p>{{buildingLabel()}}</p>
              <p v-if="show">{{ buildLabel(model.destination_type_payload['buildings_id']) }}</p>
              <select-input
                v-else
                name="buildings_id"
                mode="multiple"
                :options="availableBuildings"
                label-field-id="build_name"
                value-field-id="id"
              ></select-input>
            </a-form-item>
          </div>
          <a-form-item label="Роль" v-if="model.destination_type === 'owners_by_uk_projects' ||
            model.destination_type === 'owners_by_account_realty_types'">
              <p v-if="show">{{ roleLabel(model.destination_type_payload["client_role_types"]) }}</p>
              <select-input
                v-else
                name="client_role_types"
                mode="multiple"
                :options="availableRoles"
                label-field-id="role_name"
                value-field-id="role_code"
              ></select-input>
          </a-form-item>
          <a-form-item v-else-if="item.type === 'integer'" :label="item.label">
            <p v-if="show">{{ item.value }}</p>
            <a-input-number v-else v-model:value="item.value"></a-input-number>
          </a-form-item>
          <a-form-item v-else-if="item.name === 'phone'" :label="item.label">
            <p v-if="show">{{ item.value }}</p>
            <a-input v-else v-model:value="item.value" v-mask="'+7 (###) ###-##-##'"></a-input>
          </a-form-item>
          <a-form-item v-else-if="item['name'] !== 'project_ids'" :label="item.label">
            <p v-if="show">{{ item.value }}</p>
            <a-input v-else v-model:value="item.value"></a-input>
          </a-form-item>
        </div>
      </template>
      <action-input
        v-if="
          model.destination_type &&
          !isEmpty(availableActionTypes) &&
          model.action &&
          !(show && formValues.action.type === null)
        "
        label="Тип перехода"
        name="action"
        :action-types="availableActionTypes"
        :actions-payload-map="actionTypesPayloadMap"
        :readonly="show"
        :destination-type="model.destination_type"
        :notification-type="model.type"
      >
      </action-input>
    </template>
  </simple-form>
</template>

<script>
import { defineComponent } from 'vue'
import filter from 'lodash/filter'
import find from 'lodash/find'
import { isEmpty } from 'lodash'
import cloneDeep from 'lodash/cloneDeep'
import forEach from 'lodash/forEach'
import isArray from 'lodash/isArray'
import SimpleForm from '~/Components/Form/SimpleForm'
import TextInput from '~/Components/Form/Inputs/TextInput'
import SelectInput from '~/Components/Form/Inputs/SelectInput'
import TextAreaInput from '~/Components/Form/Inputs/TextAreaInput'
import ActionInput from './ActionInput'
import RadioGroupInput from '~/Components/Form/Inputs/RadioGroupInput'
import { NOTIFICATION_AUDIENCE_SPECIFICATION } from '~/constants/notification_audience_specification'
import { mask } from 'vue-the-mask'

const defaultValues = {
  title: null,
  text: null,
  type: null,
  destination_type: null,
  destination_type_payload: {},
  audience_specification: 'uk_only',
  buildings_id: [],
  uk_project_ids: [],
  client_role_types: [],
  destination: {
    type: null,
    payload: {},
  },
  action: {
    type: null,
    payload: [],
  },
}

const defaultRules = {
  title: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
  text: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
  type: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
  destination_type: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
}

export default defineComponent({
  name: 'NotificationForm',
  components: {
    SimpleForm,
    TextInput,
    SelectInput,
    TextAreaInput,
    ActionInput,
    RadioGroupInput,
  },
  directives: { mask },
  props: {
    initialValues: Object,
    show: {
      type: Boolean,
      default: false,
    },
    destinationTypes: Array,
    destinationTypesPayloadMap: Object,
    ukBuildings: Array,
    ukClientRoleTypes: Array,
    actionTypes: Array,
    actionTypesPayloadMap: Object,
  },
  data() {
    return {
      formValues: {},
      isEmpty,
      isArray,
      destinationPayloadArray: [],
      NOTIFICATION_AUDIENCE_SPECIFICATION,
    }
  },
  computed: {
    values() {
      if (this.initialValues && this.initialValues.action === null) {
        return {
          ...defaultValues,
          ...this.initialValues,
          action: {
            type: null,
            payload: [],
          },
        }
      } else {
        return { ...defaultValues, ...this.initialValues }
      }
    },

    rules() {
      let tempRules = { ...defaultRules }

      if (this.show) {
        tempRules = {}
      }

      return tempRules
    },

    destinationPayload() {
      const type = this.formValues.destination_type

      if (this.typeHasPayload) {
        return Object.values(this.destinationTypesPayloadMap[type])[0]
      }

      return []
    },

    selectedDestinationType() {
      return find(this.destinationTypes, {
        name: this.formValues.destination_type,
      })
    },

    availableBuildings() {
      if (Object.keys(this.destinationPayloadArray).length) {
        return filter(this.ukBuildings, { project_id: this.destinationPayloadArray[0].value })
      }

      return []
    },

    availableRoles() {
      return this.ukClientRoleTypes
    },

    availableActionTypes() {
      const type = this.selectedDestinationType
      if (type) {
        return filter(this.actionTypes, (item) =>
          type.availableActions.includes(item.name)
        )
      }
      return []
    },

    typeHasPayload() {
      if (this.selectedDestinationType) {
        return !isEmpty(this.selectedDestinationType.payloadParams)
      }
      return false
    },
  },
  watch: {
    'formValues.destination_type': {
      handler() {
        if (this.typeHasPayload) {
          const payloadArr = cloneDeep(
            this.selectedDestinationType.payloadParams
          )
          if (this.show) {
            forEach(payloadArr, (item) => {
              item.value = this.formValues.destination_type_payload[item.name]
            })
          } else {
            forEach(payloadArr, (item) => {
              item.value = item.type === 'array' ? [] : null
            })
          }
          this.destinationPayloadArray = payloadArr
        } else this.destinationPayloadArray = []
      },
    },
  },
  methods: {
    changeFormValues(newValue) {
      this.formValues = newValue
    },

    destinationPayloadLabel(payload) {
      if (isArray(payload.value)) {
        if (isEmpty(payload.value) ) {
          return 'Не указан'
        } else {
          const labelArr = payload.value.map((item) => {
            const payloadObj = find(
              this.destinationTypesPayloadMap[this.formValues.destination_type][
                payload.name
                ],
              { key: item }
            )
            return payloadObj ? payloadObj.label : ''
          })
          return labelArr.join(', ')
        }
      } else {
        const payloadObj = find(
          this.destinationTypesPayloadMap[this.formValues.destination_type][
            payload.name
          ],
          { key: payload.value }
        )
        return payloadObj ? payloadObj.label : ''
      }
    },

    buildingLabel() {
      let building_name = ''

      if (!isArray(this.destinationPayloadArray[0].value)) {
        building_name = ' "' + find(
          this.destinationTypesPayloadMap[this.formValues.destination_type][this.destinationPayloadArray[0].name],
          { key: this.destinationPayloadArray[0].value }).label + '"'
      }

      return 'Корпус в Проекте УК' + building_name
    },

    buildLabel(current_buildings) {
      if (isArray(current_buildings)) {
        let labelArr = []

        forEach(current_buildings, (item) => {
          labelArr.push(find(this.ukBuildings, { id: item }).build_name)
        })

        return labelArr.join(', ')
      } else {
        return find(this.ukBuildings, { id: current_buildings }).build_name
      }
    },

    roleLabel(current_role) {
      if (isEmpty(current_role)) {
        return 'Не указана'
      } else {
        if (isArray(current_role)) {
          let labelArr = []

          forEach(current_role, (item) => {
            labelArr.push(find(this.ukClientRoleTypes, { role_code: item }).role_name)
          })

          return labelArr.join(', ')
        } else {
          return find(this.ukClientRoleTypes, { role_code: current_role }).role_name
        }
      }
    },

    audienceSpecificationLabel(current_audience_spec) {
        return current_audience_spec ? find(NOTIFICATION_AUDIENCE_SPECIFICATION, { key: current_audience_spec }).label : ''
    },

    onSubmit(values) {
      const destinationObject = {
        type: values.destination_type,
      }
      if (this.typeHasPayload) {
        destinationObject.payload = {}

        forEach(this.destinationPayloadArray, (item) => {
          destinationObject.payload[item.name] = item.value
        })

        destinationObject.payload['audience_specification'] = values.audience_specification
        destinationObject.payload['buildings_id'] = values.buildings_id
        destinationObject.payload['client_role_types'] = values.client_role_types
      }

      this.formValues.destination = destinationObject
      values.destination = destinationObject

      delete values.destination_type
      delete values.destination_type_payload
      delete values.audience_specification
      delete values.buildings_id
      delete values.client_role_types
      delete values.uk_project_ids

      if (values.action && values.action.type === null) {
        delete values.action
      } else {
        const actionObject = {
          type: values.action.type,
        }
        actionObject.payload = {}
        forEach(values.action.payload, (item) => {
          actionObject.payload[item.name] = item.value
        })
        values.action = actionObject
      }
    },
  },
  mounted() {
    this.formValues = this.values
  },
})
</script>

<style scoped></style>
