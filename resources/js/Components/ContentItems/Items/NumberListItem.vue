<template>
  <a-form ref="form" :model="form" layout="vertical">
    <a-space direction="vertical" :style="{ width: '100%' }">
      <a-form-item name="numberList">
        <div class="ptp-input ant-table" style="margin-bottom: 1rem">
          <table>
            <tbody class="ant-table-tbody">
              <tr
                v-for="(item, index) in form.numberList"
                class="ant-table-row"
              >
                <td
                  :class="[
                    { 'ppt-cell-invalid': !isValid && !form.numberList[index] },
                  ]"
                >
                  <a-input v-model:value="form.numberList[index]" />
                  <p class="ptp-cell-help">Введите текст</p>
                </td>
                <td>
                  <delete-two-tone
                    twoToneColor="#f5222d"
                    @click="remove(index)"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <a-button type="dashed" @click="add">Добавить</a-button>
      </a-form-item>
      <a-form-item>
        <a-space>
          <a-button
            v-if="value.length === 0 || changed"
            type="primary"
            @click="onSubmit"
            :disabled="!isValid"
          >
            Сохранить
          </a-button>
          <a-button v-if="changed" type="default" @click="onCancel">
            Отменить
          </a-button>
        </a-space>
      </a-form-item>
    </a-space>
  </a-form>
</template>

<script>
import isEqual from 'lodash/isEqual'
import { DeleteTwoTone } from '@ant-design/icons-vue'

export default {
  components: {
    DeleteTwoTone,
  },
  props: {
    value: { type: Array, default: [] },
  },
  emits: ['save'],
  data() {
    return {
      form: {
        numberList: [...this.value],
      },
      isValid: true,
    }
  },
  computed: {
    changed() {
      return !isEqual(this.form.numberList, this.value)
    },
  },
  watch: {
    'form.numberList': {
      deep: true,
      handler() {
        this.isValid = true
      },
    },
  },
  methods: {
    add() {
      this.form.numberList.push('')
    },
    remove(index) {
      this.form.numberList.splice(index, 1)
    },
    onCancel() {
      this.$refs.form.resetFields()
    },
    onSubmit() {
      this.validate()
      if (this.isValid) {
        this.$emit('save', this.form.numberList)
      }
    },
    validate() {
      for (let item of this.form.numberList) {
        if (!item) {
          this.isValid = false
          return
        }
      }
      this.isValid = true
    },
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
  font-size: 12px;
  margin-top: 2px;
  color: #ff4d4f;
  text-align: left;
}
</style>
