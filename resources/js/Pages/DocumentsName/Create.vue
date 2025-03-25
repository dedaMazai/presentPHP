<template>
  <create-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="documents-name"
  >
    <template #default>
      <documents-name-form></documents-name-form>
    </template>
  </create-page>
</template>

<script>
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import DocumentsNameForm from '~/Components/DocumentsName/DocumentsNameForm'

export default {
  name: 'DocumentsNameCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    DocumentsNameForm,
  },
  data() {
    const title = `Создание Наименования документов сделки`
    return {
      title,
      breadcrumb: [
        {
          path: this.route('documents-name'),
          breadcrumbName: 'Наименования документов сделки',
        },
        { breadcrumbName: title },
      ],
    }
  },
}
</script>

<style scoped></style>
