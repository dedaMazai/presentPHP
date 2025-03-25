<template>
  <a-row justify="space-between" align="middle">
    <a-col>
      <a-page-header title="Список контактов"></a-page-header>
    </a-col>
    <a-col>
      <a-button type="primary" @click="showModal">
        <plus-outlined />
        Добавить
      </a-button>
    </a-col>
  </a-row>
  <a-empty v-if="!contacts.length" />
  <draggable
    :componentData="{ gutter: [16, 16] }"
    :list="contacts"
    item-key="id"
    tag="a-row"
    v-bind="dragOptions"
    @end="drag = false"
    @sort="handleSort"
    @start="drag = true"
  >
    <template #item="{ element }">
      <a-col :span="6">
        <a-card hoverable size="small">
          <template #actions class="ant-card-actions">
            <a-tooltip
              key="edit"
              title="Редактировать"
              @click="editContact(element)"
            >
              <edit-outlined />
            </a-tooltip>
            <a-tooltip key="delete" title="Удалить">
              <a-popconfirm
                cancel-text="Нет"
                ok-text="Да"
                title="Вы уверены, что хотите удалить запись?"
                @confirm="onDelete(element.id)"
              >
                <delete-outlined />
              </a-popconfirm>
            </a-tooltip>
          </template>
          <a-card-meta :title="element.title">
            <template #description>
              <a-descriptions :column="1" layout="vertical" size="small">
                <a-descriptions-item label="Тип">
                  <a-typography-text strong>
                    {{ typeLabel(element.type) }}
                  </a-typography-text>
                </a-descriptions-item>
                <a-descriptions-item label="Город">
                  <a-typography-text strong>
                    {{ cityName(element.city_id) }}
                  </a-typography-text>
                </a-descriptions-item>
                <a-descriptions-item label="Дата создание">
                  <a-typography-text strong>
                    {{ formatDateTime(element.created_at) }}
                  </a-typography-text>
                </a-descriptions-item>
                <a-descriptions-item label="Дата обновления">
                  <a-typography-text strong>
                    {{ formatDateTime(element.updated_at) }}
                  </a-typography-text>
                </a-descriptions-item>
              </a-descriptions>
            </template>
          </a-card-meta>
        </a-card>
      </a-col>
    </template>
  </draggable>
  <a-modal
    v-model:visible="visible"
    :title="edit ? 'Редактирование контакта' : 'Создание контакта'"
    @cancel="resetFields"
  >
    <a-form layout="vertical" :model="formState" :rules="rules" ref="formRef">
      <a-form-item label="Название" name="title">
        <a-input v-model:value="formState.title" />
      </a-form-item>
      <a-form-item label="Изображение" name="icon_image_id" v-if="visible">
        <single-image-uploader
          v-model="formState.icon_image_id"
          :image-url="imageUrl"
        />
      </a-form-item>
      <a-form-item label="Город" name="city_id">
        <a-select v-model:value="formState.city_id">
          <a-select-option
            v-for="item in cities"
            :key="item.id"
            :value="item.id"
          >
            {{ item.name }}
          </a-select-option>
        </a-select>
      </a-form-item>
      <a-form-item label="Тип" name="type">
        <a-select :options="types" v-model:value="formState.type" />
      </a-form-item>
      <a-form-item v-if="formState.type === 'map'" label="Широта" name="lat">
        <a-input v-model:value="formState.lat" />
      </a-form-item>
      <a-form-item v-if="formState.type === 'map'" label="Долгота" name="long">
        <a-input v-model:value="formState.long" />
      </a-form-item>
      <a-form-item v-if="formState.type === 'email'" label="Email" name="email">
        <a-input v-model:value="formState.email" />
      </a-form-item>
      <a-form-item
        v-if="formState.type === 'phone'"
        label="Телефон"
        name="phone"
      >
        <a-input v-model:value="formState.phone" />
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
import Draggable from 'vuedraggable'
import toNumber from 'lodash/toNumber'
import find from 'lodash/find'
import { message } from 'ant-design-vue'
import BasicLayout from '~/Layouts/BasicLayout'
import { formatDateTime } from '~/utils'
import SingleImageUploader from '~/Components/SingleImageUploader'
import {
  PlusOutlined,
  EditOutlined,
  DeleteOutlined,
} from '@ant-design/icons-vue'

const defaultFormState = {
  title: null,
  icon_image_id: null,
  icon_image: null,
  city_id: null,
  type: null,
  lat: null,
  long: null,
  email: null,
  phone: null,
}

const defaultRules = {
  title: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
  icon_image_id: [
    {
      required: true,
      message: 'Обязательное поле',
      trigger: 'blur',
      type: 'number',
    },
  ],
  city_id: [
    {
      required: true,
      message: 'Обязательное поле',
      trigger: 'blur',
      type: 'number',
    },
  ],
  type: [{ required: true, message: 'Обязательное поле', trigger: 'blur' }],
}

export default {
  name: 'Contacts',
  layout: BasicLayout,
  components: {
    SingleImageUploader,
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    Draggable,
  },
  props: {
    contacts: Array,
    types: Array,
    cities: Array,
  },
  data() {
    return {
      formatDateTime,
      drag: false,
      dragOptions: {
        animation: 200,
        group: 'description',
        disabled: false,
        ghostClass: 'ghost',
      },
      id: null,
      edit: false,
      loading: false,
      visible: false,
      formState: {
        ...defaultFormState,
      },
      columns: [
        {
          title: 'Название',
          dataIndex: 'title',
          key: 'title',
          sorter: (a, b) => a.title.localeCompare(b.title),
        },
        {
          title: 'Дата создания',
          dataIndex: 'created_at',
          key: 'created_at',
          slots: { customRender: 'created' },
          sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
        },
        {
          title: 'Дата обновления',
          dataIndex: 'updated_at',
          key: 'updated_at',
          slots: { customRender: 'updated' },
          sorter: (a, b) => new Date(a.updated_at) - new Date(b.updated_at),
        },
        {
          title: 'Действия',
          dataIndex: 'actions',
          key: 'actions',
          slots: { customRender: 'actions' },
        },
      ],
    }
  },
  computed: {
    rules() {
      switch (this.formState.type) {
        case 'phone':
          return {
            ...defaultRules,
            phone: [
              { required: true, message: 'Обязательное поле', trigger: 'blur' },
            ],
          }
        case 'email':
          return {
            ...defaultRules,
            email: [
              { required: true, message: 'Обязательное поле', trigger: 'blur' },
            ],
          }
        case 'map':
          return {
            ...defaultRules,
            lat: [
              { required: true, message: 'Обязательное поле', trigger: 'blur' },
              {
                type: 'number',
                message: 'Значение должно быть числом',
                transform(value) {
                  return toNumber(value)
                },
              },
            ],
            long: [
              { required: true, message: 'Обязательное поле', trigger: 'blur' },
              {
                type: 'number',
                message: 'Значение должно быть числом',
                transform(value) {
                  return toNumber(value)
                },
              },
            ],
          }
        default:
          return { ...defaultRules }
      }
    },
    imageUrl() {
      if (this.formState.icon_image) {
        return this.formState.icon_image.url
      } else {
        return null
      }
    },
  },
  methods: {
    formatDate(date) {
      return formatDateTime(date)
    },
    showModal() {
      this.visible = true
    },
    validate() {
      this.$refs.formRef.validate()
    },
    handleSubmit() {
      this.$refs.formRef
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
    editContact(contact) {
      this.formState = { ...contact }
      this.edit = true
      this.id = contact.id
      this.visible = true
    },
    deleteContact(contact) {
      this.onDelete(contact.id)
    },
    onCreate(data) {
      const route = this.route('settings.contacts.store')
      this.$inertia.post(route, data)
    },
    onDelete(id) {
      this.$inertia.delete(this.route('settings.contacts.destroy', [id]))
    },
    onUpdate(data, id) {
      const route = this.route('settings.contacts.update', [id])
      this.$inertia.put(route, data)
    },
    handleSort() {
      const data = this.contacts.map((item) => item.id)
      const route = this.route('settings.contacts.sort')
      this.$inertia.put(route, { order: data })
    },
    typeLabel(type) {
      switch (type) {
        case 'map':
          return 'Карта'
        case 'email':
          return 'Email'
        case 'phone':
          return 'Телефон'
      }
    },
    cityName(city_id) {
      return find(this.cities, { id: city_id }).name
    },
  },
}
</script>

<style scoped></style>
