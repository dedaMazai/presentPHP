<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :title="title"
    resource="documents-name"
    resource-url="documents-name"
  >
    <template #default>
      <documents-name-form></documents-name-form>
    </template>
  </edit-page>
</template>

<script>
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import DocumentsNameForm from '~/Components/DocumentsName/DocumentsNameForm'

export default {
  name: 'DocumentsNameEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    DocumentsNameForm,
  },
  data() {
    const title = 'Редактирование Наименования документов сделки: {{ name }}'
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
