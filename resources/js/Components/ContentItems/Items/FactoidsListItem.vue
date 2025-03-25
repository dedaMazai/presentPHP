<template>
  <a-form ref="form" :model="form" layout="vertical">
    <a-space direction="vertical" :style="{ width: '100%' }">
      <a-form-item name="factoids">
        <div class="ptp-input ant-table" style="margin-bottom: 1rem">
          <table>
            <thead class="ant-table-thead">
              <tr>
                <th>Число</th>
                <th>Измерение</th>
                <th>Описание</th>
                <th></th>
              </tr>
            </thead>
            <tbody class="ant-table-tbody">
              <tr v-for="(item, index) in form.factoids" class="ant-table-row">
                <td>
                  <a-input-number v-model:value="item.number" />
                </td>
                <td>
                  <a-input v-model:value="item.unit" />
                </td>
                <td>
                  <a-input v-model:value="item.description" />
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
import cloneDeep from 'lodash/cloneDeep'
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
        factoids: cloneDeep(this.value),
      },
    }
  },
  computed: {
    changed() {
      return !isEqual(this.form.factoids, this.value)
    },
  },
  methods: {
    add() {
      this.form.factoids.push({
        number: null,
        unit: null,
        description: null,
      })
    },
    remove(index) {
      this.form.factoids.splice(index, 1)
    },
    onCancel() {
      this.form.factoids = cloneDeep(this.value)
    },
    onSubmit() {
      this.$emit('save', this.form.factoids)
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
</style>
