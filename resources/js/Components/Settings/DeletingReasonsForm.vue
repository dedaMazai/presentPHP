<template>
  <a-row justify="space-between" align="middle">
    <a-col>
      <a-page-header title="Список причин удаления"></a-page-header>
    </a-col>
    <a-col>
      <a-button type="primary" @click="showModal">
        <plus-outlined />
        Добавить
      </a-button>
    </a-col>
  </a-row>
  <a-empty v-if="!deletingReasons.length" />
  <a-row>
    <a-col :span="6" v-for="reason in deletingReasons">
      <a-card hoverable size="small">
        <template #actions class="ant-card-actions">
          <a-tooltip
            key="edit"
            title="Редактировать"
            @click="editReason(reason)"
          >
            <edit-outlined />
          </a-tooltip>
          <a-tooltip key="delete" title="Удалить">
            <a-popconfirm
              cancel-text="Нет"
              ok-text="Да"
              title="Вы уверены, что хотите удалить запись?"
              @confirm="onDelete(reason.id)"
            >
              <delete-outlined />
            </a-popconfirm>
          </a-tooltip>
        </template>
        <a-card-meta :title="reason.title">
          <template #description>
            <a-descriptions :column="1" layout="vertical" size="small">
              <a-descriptions-item label="Название">
                <a-typography-text strong>
                  {{ reason.title }}
                </a-typography-text>
              </a-descriptions-item>
              <a-descriptions-item label="Значение">
                <a-typography-text strong>
                  {{ reason.value }}
                </a-typography-text>
              </a-descriptions-item>
            </a-descriptions>
          </template>
        </a-card-meta>
      </a-card>
    </a-col>
  </a-row>
  <a-modal
    v-model:visible="visible"
    :title="edit ? 'Редактирование причины' : 'Создание причины'"
    @cancel="resetFields"
  >
    <a-form layout="vertical" :model="formState" :rules="rules" ref="formReasonsRef">
      <a-form-item label="Название" name="title">
        <a-input v-model:value="formState.title" />
      </a-form-item>
      <a-form-item label="Значение" name="value">
        <a-input v-model:value="formState.value" />
      </a-form-item>
    </a-form>
    <template #footer>
      <a-button key="back" @click="handleCancel">Вернуться</a-button>
      <a-button
        key="submit"
        type="primary"
        :loading="loading"
        @click="handleSubmit"
      >{{ edit ? 'Изменить' : 'Создать' }}
      </a-button>
    </template>
  </a-modal>
</template>

<script>
import { message } from 'ant-design-vue'
import {
  PlusOutlined,
  EditOutlined,
  DeleteOutlined,
} from '@ant-design/icons-vue'
import Draggable from 'vuedraggable'
import BasicLayout from '~/Layouts/BasicLayout'

const defaultFormState = {
  title: null,
  value: null,
}

export default {
  name: 'DeletingReasonsForm',
  layout: BasicLayout,
  components: {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    Draggable,
  },
  props: {
    deletingReasons: Array,
  },
  data() {
    return {
      id: null,
      edit: false,
      loading: false,
      visible: false,
      formState: {
        ...defaultFormState,
      },
      rules: {
        title: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
        value: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
      }
    }
  },
  methods: {
    showModal() {
      this.visible = true
    },
    validate() {
      this.$refs.formReasonsRef.validate()
    },
    handleSubmit() {
      this.$refs.formReasonsRef
        .validate()
        .then(() => {
          if (this.edit) {
            this.onUpdate(this.formState, this.id)
          } else {
            this.onCreate(this.formState)
          }
          this.handleCancel()
        })
        .catch((err) => {
          message.error(
            'Ошибка валидаций. Проверьте правильность ввода данных и повторите попытку.'
          )
        })
    },
    handleCancel() {
      this.visible = false
      this.resetFields()
    },
    resetFields() {
      this.edit = false
      this.id = null
      this.formState = { ...defaultFormState }
    },
    editReason(reason) {
      this.formState = { ...reason }
      this.edit = true
      this.id = reason.id
      this.visible = true
    },
    deleteReason(reason) {
      this.onDelete(reason.id)
    },
    onCreate(data) {
      const route = this.route('settings.deleting-reasons.store')
      this.$inertia.post(route, data)
    },
    onDelete(id) {
      this.$inertia.delete(this.route('settings.deleting-reasons.destroy', [id]))
    },
    onUpdate(data, id) {
      const route = this.route('settings.deleting-reasons.update', [id])
      this.$inertia.put(route, data)
    },
  },
}
</script>

<style scoped>

</style>
