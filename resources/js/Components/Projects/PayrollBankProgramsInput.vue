<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <a-row
      v-for="(item, index) in value"
      type="flex"
      justify="start"
      align="middle"
      :gutter="16"
      style="margin-bottom: 1rem"
    >
      <a-col :span="10">
        <a-input-number v-model:value="item.id" placeholder="ID" />
      </a-col>
      <a-col :span="10">
        <a-input v-model:value="item.name" placeholder="Название" />
      </a-col>
      <a-col :span="4"
      ><delete-two-tone twoToneColor="#f5222d" @click="remove(index)"
      /></a-col>
    </a-row>
    <a-button type="dashed" @click="add">Добавить</a-button>
  </a-form-item>
</template>

<script>
import { DeleteTwoTone } from '@ant-design/icons-vue'
import useField from '~/composables/useField'
import { InputProps } from '~/Components/Form/Inputs/types'

export default {
  name: 'PayrollBankProgramsInput',
  components: {
    DeleteTwoTone,
  },
  props: {
    ...InputProps,
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)

    const add = () => {
      value.value.push({
        id: '',
        name: '',
      })
    }

    const remove = (index) => {
      value.value.splice(index, 1)
    }

    return {
      value,
      validateInfos,
      add,
      remove,
    }
  },
}
</script>

<style scoped>

</style>
