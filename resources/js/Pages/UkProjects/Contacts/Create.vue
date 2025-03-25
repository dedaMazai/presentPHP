<template>
  <create-page
    :breadcrumb="breadcrumb"
    :extra-params="[ukProject.id]"
    :title="title"
    resource="uk-projects.contacts"
  >
    <template #default>
      <contacts-form :types="mappedTypes"></contacts-form>
    </template>
  </create-page>
</template>

<script>
import toPairs from 'lodash/toPairs'
import CreatePage from '~/core/CreatePage'
import BasicLayout from '~/Layouts/BasicLayout'
import ContactsForm from '~/Components/UkProjects/ContactsForm'

export default {
  name: 'UkProjectContactCreate',
  layout: BasicLayout,
  components: {
    CreatePage,
    ContactsForm,
  },
  props: {
    ukProject: Object,
    contactTypes: Array,
  },
  data(props) {
    const title = 'Создание контакта'
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
