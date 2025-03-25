<template>
  <a-form :layout="layout" v-bind="formItemLayout" @submit="handleSubmit">
    <slot :model="model"></slot>
    <template v-if="!readonly">
      <slot :reset-fields="handleReset" name="actions">
        <a-form-item
          :wrapper-col="{ span: wrapperCol, offset: labelCol }"
          class="simple-form-default-actions"
        >
          <a-button
            v-if="userBanReset"
            @click="handleBan"
            type="primary"
          >Разблокировать
          </a-button>
          <a-button htmlType="submit" type="primary">Сохранить</a-button>

<!--          <a-button-->
<!--            v-if="model.category === 'news' && model.category !== 'news.create'"-->
<!--            style="background: white; color: black; border: 1px solid black; margin-left: 20px;"-->
<!--            @click.prevent="handleSubmitAsNew"-->
<!--          >-->
<!--            Сохранить как новую-->
<!--          </a-button>-->

          <a-button
            v-if="hasReset"
            style="margin-left: 10px"
            @click="handleReset"
            >Сбросить
          </a-button>
        </a-form-item>
      </slot>
    </template>
    <template v-if="banReset">
      <slot :reset-fields="handleReset" name="actions">
        <a-form-item
          :wrapper-col="{ span: wrapperCol, offset: labelCol }"
          class="simple-form-default-actions"
        >
          <a-button
            v-if="banReset"
            @click="handleBan"
            type="primary"
          >Разблокировать
          </a-button>
        </a-form-item>
      </slot>
    </template>
  </a-form>
</template>

<script>
import {
  computed,
  defineComponent, getCurrentInstance,
  nextTick,
  provide,
  reactive,
  toRef,
  unref,
  watch,
} from 'vue'
import { Form, message } from 'ant-design-vue'
import mapValues from 'lodash/mapValues'
import {errorMessages} from "@vue/compiler-sfc";
import { router } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'

const useForm = Form.useForm

export default defineComponent({
  name: 'SimpleForm',
  props: {
    initialValues: {
      type: Object,
      default: {},
    },
    rules: {
      type: Object,
    },
    layout: {
      type: String,
      default: 'vertical',
    },
    labelCol: {
      type: Number,
      default: 4,
    },
    wrapperCol: {
      type: Number,
      default: 8,
    },
    hasReset: {
      type: Boolean,
      default: false,
    },
    filters: {
      type: Boolean,
      default: false,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    banReset: {
      type: Boolean,
      default: false,
    },
    userBanReset: {
      type: Boolean,
      default: false,
    }
  },
  emits: ['onSubmit', 'onFieldChange'],
  methods: {
    handleBan() {
      this.$inertia.post('unlock', {
        onFinish: () => {

        }
      })
    },
  },
  setup(props, { emit }) {
    const model = reactive({ ...props.initialValues })
    let rules
    if (props.rules) {
      rules = reactive(toRef(props, 'rules'))
    } else {
      rules = props.rules
    }
    const form = useForm(model, rules)

    provide('$form', form)
    provide('$formModel', model)
    provide('$formRules', rules)

    watch(model, (newValue) => {
      emit('onFieldChange', model)
    })

    const handleSubmit = () => {
      form
        .validate()
        .then(() => {
          emit('onSubmit', model)
        })
        .catch((err) => {
          message.error(
            'Ошибка валидаций. Проверьте правильность ввода данных и повторите попытку.'
          )
          window.scrollTo(0, 0)
        })
    }


    // function handleSubmitAsNew() {
    //   form.validate()
    //     .then(() => {
    //       Inertia.post(window.route('news.store-from-edit', [model.id]), model)
    //     })
    //     .catch((e) => {
    //       message.error('Ошибка валидаций.')
    //     })
    // }

    const handleReset = (isHard) => {
      if (isHard) {
        form.resetFields(mapValues(unref(model), () => null))
        nextTick(function () {
          handleSubmit()
        })
      } else {
        form.resetFields()
      }
    }

    const formItemLayout = computed(() => {
      const { layout, filters } = props
      if (layout === 'horizontal') {
        return {
          labelCol: {
            span: props.labelCol,
          },
          wrapperCol: {
            span: props.wrapperCol,
          },
        }
      } else if (layout === 'vertical' && filters) {
        return {}
      } else {
        return {
          wrapperCol: {
            sm: 21,
            md: 14,
            lg: 10,
            xl: 8,
            xxl: 6,
          },
        }
      }
    })

    return {
      formItemLayout,
      model,
      handleSubmit,
      handleReset,
    }
  },
})
</script>

<style scoped></style>
