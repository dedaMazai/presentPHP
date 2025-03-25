<template>
  <page-wrapper :title="title">
    <a-card>
      <a-tabs tab-position="left">
        <a-tab-pane key="basic" tab="Общие">
          <settings-form :settings="settings" />
        </a-tab-pane>
        <a-tab-pane key="about" tab="О группе компаний">
          <about-form :settings="settings" :content-items="contentItems" />
        </a-tab-pane>
        <a-tab-pane key="contacts" tab="Контакты">
          <contacts
            :settings="settings"
            :contacts="contacts"
            :types="mappedTypes"
            :cities="cities"
          />
        </a-tab-pane>
        <a-tab-pane key="cache" tab="Кэш">
          <cache></cache>
        </a-tab-pane>
        <a-tab-pane key="documents" tab="Документы">
          <documents-form
            :settings="settings"
          />
        </a-tab-pane>
        <a-tab-pane key="services" tab="Услуги">
          <services-form :settings="settings"/>
        </a-tab-pane>
        <a-tab-pane key="account-deleting-reasons" tab="Причины удаления аккаунта">
          <deleting-reasons-form :deleting-reasons="deletingReasons"/>
        </a-tab-pane>
        <a-tab-pane key="builds" tab="Сборки">
          <builds-form
            :settings="settings"
          />
        </a-tab-pane>
      </a-tabs>
    </a-card>
  </page-wrapper>
</template>

<script>
import toPairs from 'lodash/toPairs'
import BasicLayout from '~/Layouts/BasicLayout'
import SettingsForm from '~/Components/Settings/SettingsForm'
import AboutForm from '~/Components/Settings/AboutForm'
import DocumentsForm from '~/Components/Settings/DocumentsForm'
import ServicesForm from '~/Components/Settings/ServicesForm'
import Contacts from '~/Components/Settings/Contacts/Contacts'
import Cache from '~/Components/Settings/Cache'
import DeletingReasonsForm from '~/Components/Settings/DeletingReasonsForm'
import BuildsForm from '~/Components/Settings/BuildsForm'

export default {
  name: 'SettingsEdit',
  layout: BasicLayout,
  components: {
    SettingsForm,
    AboutForm,
    Contacts,
    Cache,
    DocumentsForm,
    ServicesForm,
    DeletingReasonsForm,
    BuildsForm,
  },
  props: {
    settings: Object,
    contentItems: Array,
    contacts: Array,
    contactTypes: Array,
    cities: Array,
    deletingReasons: Array,
  },
  data() {
    return {
      title: 'Настройки',
    }
  },
  computed: {
    mappedTypes() {
      if (this.contactTypes) {
        return toPairs(this.contactTypes).map((pair) => {
          return {label: pair[1], value: pair[0]}
        })
      }
    },
  },
}
</script>
