<template>
  <a-row type="flex" justify="center">
    <a-col :span="12">
      <a-card
        class="card"
        :bordered="false"
      >
        <a-form
          ref="form"
          :model="form"
          :rules="validationRules"
          :label-col="labelCol"
          :wrapper-col="wrapperCol"
          @submit="onSubmit"
        >
          <a-alert
            v-if="errors.email"
            :message="errors.email === 'auth.failed' ? 'Неверный логин или пароль' : errors.email === 'auth.throttle' ? 'Превышено кол-во попыток входа. Повторите попозже' : errors.email"
            type="error"
          />

          <a-form-item
            label="Email"
            name="email"
          >
            <a-input
              v-model:value="form.email"
              placeholder="Email"
            />
          </a-form-item>

          <a-form-item
            label="Пароль"
            name="password"
          >
            <a-input
              v-model:value="form.password"
              type="password"
              placeholder="Пароль"
            />
          </a-form-item>

          <a-form-item
            label="Запомнить меня"
            name="remember"
          >
            <a-checkbox
              v-model:checked="form.remember"
            />
          </a-form-item>

          <a-form-item :wrapper-col="{ span: 14, offset: 5 }">
            <a-button
              type="primary"
              html-type="submit"
              @click="onSubmit"
            >
              Войти <login-outlined />
            </a-button>
          </a-form-item>
        </a-form>
      </a-card>
    </a-col>
  </a-row>
</template>

<script>
import { LoginOutlined } from '@ant-design/icons-vue'
import OneColumnLayout from '~/Layouts/OneColumnLayout'

export default {
  components: {
    LoginOutlined
  },
  layout: OneColumnLayout,
  props: {
    errors: Object
  },
  data () {
    return {
      labelCol: { span: 5 },
      wrapperCol: { span: 14 },
      form: {
        email: null,
        password: null,
        remember: true
      },
      validationRules: {
        email: [
          { required: true, message: 'Заполните email', trigger: 'blur' },
          { type: 'email', message: 'Поле должно быть валидным email', trigger: 'blur' }
        ],
        password: [
          { required: true, message: 'Заполните пароль', trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    onSubmit () {
      const { $inertia, route, form } = this
      this.$refs.form
        .validate()
        .then(() => {
          $inertia.post(route('login'), form)
        })
        .catch(error => {
          console.log('error', error)
        })
    }
  }
}
</script>
