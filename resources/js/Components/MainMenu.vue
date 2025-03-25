<template>
  <a-menu v-model:selectedKeys="selectedKeys" mode="inline" theme="dark">
    <a-menu-item key="users" v-if="hasAnyRole(['admin'])">
      <inertia-link :href="route('users')">
        <team-outlined />
        <span>Клиенты</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="payments" v-if="hasAnyRole(['admin'])">
      <inertia-link :href="route('payments')">
        <team-outlined />
        <span>Оплаты</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="accounts" v-if="hasAnyRole(['admin'])">
      <inertia-link :href="route('accounts')">
        <team-outlined />
        <span>Лицевые счета</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="news" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('news')">
        <read-outlined />
        <span>Новости</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="offers" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('banners.places')">
        <picture-outlined />
        <span>Баннеры</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="announcements" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('ads')">
        <notification-outlined />
        <span>Объявления</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="instructions" v-if="hasAnyRole(['admin', 'marketing'])">
      <inertia-link :href="route('instructions')">
        <notification-outlined />
        <span>Инструкция к покупке</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="notifications" v-if="hasAnyRole(['admin', 'marketing'])">
      <inertia-link :href="route('notifications')">
        <notification-outlined />
        <span>Уведомления</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="project-types" v-if="hasAnyRole(['admin', 'marketing'])">
      <inertia-link :href="route('project-types')">
        <project-outlined />
        <span>Проекты</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="bank-info" v-if="hasAnyRole(['admin', 'marketing'])">
      <inertia-link :href="route('bank-info')">
        <project-outlined />
        <span>Информация о банках</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="uk-projects" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('uk-projects')">
        <project-outlined />
        <span>Проекты УК</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="reality-types" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('reality-types')">
        <project-outlined />
        <span>Типы недвижимости</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="grouping-reality-types" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('grouping-reality-types')">
        <project-outlined />
        <span>Группы типов недвижимости</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="client-role-types" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('client-role-types')">
        <project-outlined />
        <span>Роли клиентов</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="finishings" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('finishings')">
        <project-outlined />
        <span>Варианты отделки</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="banks" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('banks')">
        <project-outlined />
        <span>Справочник банков</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="escrow-banks" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('escrow-banks')">
        <project-outlined />
        <span>Банки эскроу</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="documents-name" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('documents-name')">
        <project-outlined />
        <span>Наименования документов сделки</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="sign-documents" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('sign-documents')">
        <project-outlined />
        <span>Инструкции по приложению ЭП</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="blocking" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('blocking')">
        <project-outlined />
        <span>Блокировки</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="support-topics" v-if="hasAnyRole(['admin', 'uk'])">
      <inertia-link :href="route('support-topics')">
        <project-outlined />
        <span>Тематики обращений</span>
      </inertia-link>
    </a-menu-item>
    <a-menu-item key="settings" v-if="hasAnyRole(['admin'])">
      <inertia-link :href="route('settings.edit')">
        <setting-outlined />
        <span>Настройки</span>
      </inertia-link>
    </a-menu-item>
  </a-menu>
</template>

<script>
import { computed, defineComponent } from 'vue'
import {
  NotificationOutlined,
  PictureOutlined,
  ProjectOutlined,
  ReadOutlined,
  TeamOutlined,
  SettingOutlined,
} from '@ant-design/icons-vue'
import { usePage } from '@inertiajs/inertia-vue3'

export default defineComponent({
  name: 'MainMenu',
  components: {
    ReadOutlined,
    PictureOutlined,
    NotificationOutlined,
    TeamOutlined,
    ProjectOutlined,
    SettingOutlined,
  },
  setup() {
    const selectedKeys = computed(() => [usePage().url.value.split('/')[1]])

    return {
      selectedKeys,
    }
  },
})
</script>

<style scoped></style>
