<template>
  <edit-page
    :breadcrumb="breadcrumb"
    :extra-params="[ukProject.id]"
    :title="title"
    resource="contact"
    resource-url="uk-projects.contacts"
  >
    <template #default>
      <contacts-form :contact="contact" :types="mappedTypes" />
    </template>
  </edit-page>
</template>

<script>
import toPairs from 'lodash/toPairs'
import EditPage from '~/core/EditPage'
import BasicLayout from '~/Layouts/BasicLayout'
import ContactsForm from '~/Components/UkProjects/ContactsForm'

export default {
  name: 'UkProjectArticleEdit',
  layout: BasicLayout,
  components: {
    EditPage,
    ContactsForm,
  },
  props: {
    ukProject: Object,
    contact: Object,
    contactTypes: Array,
  },
  data(props) {
    const title = 'Редактирование контакта: {{ title }}'
    return {
      title,
      breadcrumb: [
        {
          path: this.route('uk-projects'),
          breadcrumbName: 'Список проектов УК',
        },
        {
          path: this.route('uk-projects.edit', [props.ukProject?.id]),
          breadcrumbName: props.ukProject?.name,
        },
        {
          path: this.route('uk-projects.contacts', [props.ukProject?.id]),
          breadcrumbName: 'Список контактов',
        },
        { breadcrumbName: title },
      ],
    }
  },
  computed: {
    mappedTypes() {
      if (this.contactTypes) {
        const coll = toPairs(this.contactTypes).map((pair) => {
          return { label: pair[1], value: pair[0] }
        })
        return coll
      }
    },
  },
}
</script>

<style scoped></style>
