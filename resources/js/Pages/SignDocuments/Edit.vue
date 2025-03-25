<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="sign-documents"
    resource-url="sign-documents"
  >
    <template #default>
      <sign-documents-form></sign-documents-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import SignDocumentsForm from '~/Components/SignDocuments/SignDocumentsForm'

export default {
  name: 'SignDocumentsEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    SignDocumentsForm,
  },
  data() {
    const title = 'Редактирование Инструкции по приложению ЭП: {{ name }}'
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
