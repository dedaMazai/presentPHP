<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="uk-project"
    resource-url="uk-projects"
  >
    <template #actions="{ data }">
      <a-button @click="$inertia.get(route('uk-projects.buildings', [data.id]))">
        Просмотр списка корпусов
      </a-button>
      <a-button @click="$inertia.get(route('uk-projects.articles', [data.id]))">
        Просмотр списка статей
      </a-button>
      <a-button @click="$inertia.get(route('uk-projects.contacts', [data.id]))">
        Просмотр списка контактов
      </a-button>
    </template>
    <template #default>
      <uk-projects-form></uk-projects-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import UkProjectsForm from '~/Components/UkProjects/UkProjectsForm'

export default {
  name: 'UkProjectEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    UkProjectsForm,
  },
  data() {
    const title = 'Редактирование проекта: {{ name }}'
    return {
      title,
      breadcrumb: [
        {
          path: this.route('uk-projects'),
          breadcrumbName: 'Список проектов УК',
        },
        {breadcrumbName: title},
      ],
    }
  },
}
</script>

<style scoped></style>
