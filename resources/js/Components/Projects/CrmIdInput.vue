<template>
  <a-form-item :label="label" v-bind="validateInfos">
    <div class="ptp-input ant-table" style="margin-bottom: 1rem">
      <table>
        <thead class="ant-table-thead">
        <tr>
          <th>CRM ID</th>
          <th>Стоимость платной брони</th>
          <th>Срок действия платной брони (в днях)</th>
          <th>Время оплаты платной брони (в секундах)</th>
          <th>Время оплаты трейд-ин брони (в секундах)</th>
          <th>Премиальный</th>
          <th></th>
        </tr>
        </thead>
        <tbody class="ant-table-tbody">
        <tr v-for="(item, index) in value" class="ant-table-row">
          <td>
            <a-input v-model:value="bookingProperty[index].crm_id" style="width: 150px;" required />
          </td>
          <td>
            <a-input-number v-model:value="bookingProperty[index].paid_booking_cost" required />
          </td>
          <td>
            <a-input-number v-model:value="bookingProperty[index].paid_booking_expiry_time" required />
          </td>
          <td>
            <a-input-number v-model:value="bookingProperty[index].paid_booking_payment_time" required />
          </td>
          <td>
            <a-input-number v-model:value="bookingProperty[index].tradein_booking_payment_time" required />
          </td>
          <td>
            <a-switch v-model:checked="bookingProperty[index].is_premium" />
          </td>
          <td>
            <delete-two-tone twoToneColor="#f5222d" @click="removeRow(index)" />
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <a-button type="dashed" @click="addRow">Добавить</a-button>
  </a-form-item>
</template>

<script>
import { DeleteTwoTone } from '@ant-design/icons-vue'
import useField from '~/composables/useField'
import { InputProps } from '~/Components/Form/Inputs/types'
import { reactive, watch } from "vue";

import forEach from "lodash/forEach";

export default {
  name: 'CrmIdInput',
  components: {
    DeleteTwoTone,
  },
  props: {
    ...InputProps,
    bookingProperty: Object,
  },
  setup(props) {
    const { value, validateInfos } = useField(props.name)

    watch(props.bookingProperty, (newBookingProperty) => {
      forEach(newBookingProperty, (item, index) => {
        value.value[index] = item.crm_id
      })
    })

    return {
      value,
      validateInfos,
    }
  },
  methods: {
    addRow() {
      let booking_prop = reactive({
          crm_id: "",
          paid_booking_cost: null,
          paid_booking_expiry_time: null,
          paid_booking_payment_time: null,
          tradein_booking_payment_time: null,
          is_premium: false
        })

      this.$emit('addRow', booking_prop);
    },

    removeRow(index) {
      this.$emit('removeRow', index);
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
