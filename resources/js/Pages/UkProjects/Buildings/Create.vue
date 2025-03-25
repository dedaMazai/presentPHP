<template>
  <create-page
    :breadcrumb="breadcrumb"
    :extra-params="[ukProject.id]"
    :title="title"
    resource="uk-projects.buildings"
  >
    <template #default>
      <uk-buildings-form></uk-buildings-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import UkBuildingsForm from '~/Components/UkProjects/UkBuildingsForm'

export default {
  name: "UkProjectBuildingCreate",
  layout: BasicLayout,
  components: {
    CreatePage,
    UkBuildingsForm,
  },
  props: {
    ukProject: Object
  },
  data(props) {
    const title = "Создание корпуса";
    return {
      title,
      breadcrumb: [
        {
          path: this.route("uk-projects"),
          breadcrumbName: "Список проектов УК"
        },
        {
          path: this.route("uk-projects.edit", [props.ukProject?.id]),
          breadcrumbName: props.ukProject?.name
        },
        {
          path: this.route("uk-projects.buildings", [props.ukProject?.id]),
          breadcrumbName: "Список корпусов"
        },
        {breadcrumbName: title}
      ]
    };
  }
};
</script>

<style scoped></style>
