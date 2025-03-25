<template>
  <create-page
    :breadcrumb="breadcrumb"
    :extra-params="[ukProject.id]"
    :title="title"
    resource="uk-projects.articles"
  >
    <template #default>
      <uk-articles-form></uk-articles-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import UkArticlesForm from '~/Components/UkProjects/UkArticlesForm'

export default {
  name: "UkProjectArticleCreate",
  layout: BasicLayout,
  components: {
    CreatePage,
    UkArticlesForm,
  },
  props: {
    ukProject: Object
  },
  data(props) {
    const title = "Создание статьи";
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
          path: this.route("uk-projects.articles", [props.ukProject?.id]),
          breadcrumbName: "Список статей"
        },
        {breadcrumbName: title}
      ]
    };
  }
};
</script>

<style scoped></style>
