<template>
  <create-page :breadcrumb="breadcrumb" :title="title" resource="uk-projects">
    <template #default>
      <uk-projects-form></uk-projects-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import UkProjectsForm from '~/Components/UkProjects/UkProjectsForm'

export default {
  name: 'UkProjectsCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    UkProjectsForm,
  },
  props: {},
  data() {
    const title = `Создание проекта УК`
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
