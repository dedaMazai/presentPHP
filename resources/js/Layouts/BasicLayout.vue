<template>
  <a-config-provider :locale="locale">
    <a-layout class="basic-layout">
      <a-layout-sider v-model:collapsed="collapsed" :width="230" collapsible>
        <div class="basic-layout-logo">
          <a class="basic-layout-logo-link" href="/">
            <h2 class="basic-layout-logo-title">
              {{ collapsed ? 'P' : 'Pioneer' }}
            </h2>
          </a>
        </div>
        <main-menu></main-menu>
      </a-layout-sider>

      <a-layout class="basic-layout-content">
        <a-layout-header class="basic-layout-content-header">
          <span style="margin-right: 10px">{{ this.$page.props.auth.user.email }}</span>
          <span class="logout-form" @click="$inertia.post(route('logout'))">
            <a><logout-outlined /> Выйти</a>
          </span>
        </a-layout-header>
        <a-layout-content class="">
          <div class="basic-layout-content-wrapper">
            <slot />
          </div>
        </a-layout-content>
      </a-layout>
    </a-layout>
  </a-config-provider>
</template>

<script>
import { ref } from 'vue'
import { LogoutOutlined } from '@ant-design/icons-vue'
import locale from 'ant-design-vue/es/locale/ru_RU'
import MainMenu from '~/Components/MainMenu'

export default {
  components: {
    LogoutOutlined,
    MainMenu,
  },
  data() {
    return {
      locale,
      collapsed: ref(false),
    }
  },
}
</script>

<style scoped>
.basic-layout {
  min-height: 100vh;
}

.basic-layout-logo {
  padding: 20px 20px 0 20px;
}

.basic-layout-logo-title {
  color: #ffffff;
  text-align: center;
}

.basic-layout-content {
  min-height: 100vh;
  height: 100%;
}
.basic-layout-content-header {
  background: #fff;
  padding: 0;
  z-index: 1;
  box-shadow: 0 1px 4px rgb(0 21 41 / 8%);
  height: 40px;
  display: flex;
  justify-content: end;
  align-items: center;
}

.logout-form {
  margin-right: 24px;
}
</style>
