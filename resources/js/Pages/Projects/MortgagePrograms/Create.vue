<template>
  <create-page
    :breadcrumb="breadcrumb"
    :extra-params="[type.id, project.id]"
    :title="title"
    resource="projects.mortgage-programs"
  >
    <template #default>
      <mortgage-programs-form :banks="banks"></mortgage-programs-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import MortgageProgramsForm from '~/Components/Projects/MortgageProgramsForm'

export default {
  name: 'ProjectMortgageProgramCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    MortgageProgramsForm,
  },
  props: {
    type: Object,
    project: Object,
    banks: Array,
  },
  data(props) {
    const title = 'Создание предложения банка'
    return {
      title,
      breadcrumb: [
        {
          path: this.route('project-types'),
          breadcrumbName: 'Типы проектов',
        },
        {
          path: this.route('project-types.edit', [props.type?.id]),
          breadcrumbName: props.type?.name,
        },
        {
          path: this.route('projects', [props.type?.id]),
          breadcrumbName: 'Список проектов',
        },
        {
          path: this.route('projects.edit', [
            props.type?.id,
            props.project?.id,
          ]),
          breadcrumbName: props.project?.name,
        },
        {
          path: this.route('projects.mortgage-programs', [
            props.type?.id,
            props.project?.id,
          ]),
          breadcrumbName: 'Список предложений банков',
        },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
