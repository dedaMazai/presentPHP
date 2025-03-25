<template>
  <create-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="sign-documents"
  >
    <template #default>
      <sign-documents-form></sign-documents-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import SignDocumentsForm from '~/Components/SignDocuments/SignDocumentsForm'

export default {
  name: 'SignDocumentsCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    SignDocumentsForm,
  },
  data() {
    const title = 'Создание Инструкции по приложению ЭП'
    return {
      title,
      breadcrumb: [
        {
          path: this.route('sign-documents'),
          breadcrumbName: 'Инструкции по приложению ЭП',
        },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
