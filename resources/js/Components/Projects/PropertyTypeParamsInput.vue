<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <div class="ptp-input ant-table" style="margin-bottom: 1rem">
      <table>
        <thead class="ant-table-thead">
          <tr>
            <th>Тип</th>
            <th>URL</th>
            <th></th>
          </tr>
        </thead>
        <tbody class="ant-table-tbody">
          <tr v-for="(item, index) in value" class="ant-table-row">
            <td>
              <a-select
                v-model:value="item.type"
                :options="types"
                style="width: 100px"
              >
              </a-select>
            </td>
            <td>
              <a-input v-model:value="item.url" />
            </td>
            <td>
              <delete-two-tone twoToneColor="#f5222d" @click="remove(index)" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <a-button type="dashed" @click="add">Добавить</a-button>
  </a-form-item>
</template>

<script>
import { ref, watch } from 'vue'
import every from 'lodash/every'
import { DeleteTwoTone } from '@ant-design/icons-vue'
import useField from '~/composables/useField'
import { InputProps } from '~/Components/Form/Inputs/types'

export default {
  name: 'PropertyTypeParamsInput',
  components: {
    DeleteTwoTone,
  },
  props: {
    ...InputProps,
    propertyTypes: {
      type: Object,
      default: {},
    },
  },
  setup(props) {
    const isValid = ref(true)
    const { value, validateInfos } = useField(props.name)

    const add = () => {
      value.value.push({
        type: null,
        url: null,
      })
    }

    const remove = (index) => {
      value.value.splice(index, 1)
    }

    watch(value.value, (newValue) => {
      if (!every(newValue, 'url')) {
        isValid.value = false
      } else {
        isValid.value = true
      }
      for (let item of newValue) {
        if (!item.url && item.url !== null) {
          item.url = null
        }
      }
    })

    return {
      value,
      validateInfos,
      isValid,
      add,
      remove,
      types: Object.keys(props.propertyTypes).map((key) => ({
        value: key,
        label: props.propertyTypes[key],
      })),
    }
  },
}
</script>

<style scoped>
.ptp-input >>> th {
  padding: 6px;
  font-size: 12px;
}
.ptp-input >>> td {
  padding: 6px;
  text-align: center;
  vertical-align: baseline;
}
.ppt-cell-invalid >>> .ptp-cell-help {
  display: block;
}
.ppt-cell-invalid >>> input {
  border-color: #ff4d4f;
}
.ptp-cell-help {
  display: none;
  font-size: 9px;
  margin-top: 2px;
}
</style>
