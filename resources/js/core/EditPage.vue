<template>
  <page-wrapper :breadcrumb="breadcrumb" :title="title">
    <template #extra>
      <slot :data="data" name="actions"></slot>
      <a-button v-if="!editOnly" danger @click="handleDelete">Удалить</a-button>
    </template>
    <template #default>
      <a-card size="small">
        <component
          :is="child"
          v-for="(child, index) in $slots.default()"
          :key="child.name"
          v-bind="child.props"
          :initial-values="data"
          @on-submit="putResource"
        ></component>
      </a-card>
    </template>
  </page-wrapper>
</template>

<script>
import { createVNode, defineComponent } from 'vue'
import { Modal } from 'ant-design-vue'
import { ExclamationCircleOutlined } from '@ant-design/icons-vue'
import map from 'lodash/map'
import PageWrapper from '~/Components/PageWrapper'
import AddButton from '~/Components/AddButton'
import useResource from '~/composables/useResource'
import { formatString } from '~/utils'
import { PageProps } from './types'

export default defineComponent({
  name: 'CreatePage',
  components: {
    AddButton,
    PageWrapper,
  },
  props: {
    ...PageProps,
    editOnly: {
      type: Boolean,
      default: false,
    },
  },
  setup(props) {
    const { data, putResource, deleteResource } = useResource(
      props.resource,
      true,
      {
        extraPutParams: props.extraParams,
        extraDeleteParams: props.extraParams,
        resourceUrl: props.resourceUrl,
      }
    )

    const handleDelete = () => {
      Modal.confirm({
        title: () => 'Вы уверены, что хотите удалить эту запись?',
        icon: () => createVNode(ExclamationCircleOutlined),
        centered: true,
        okText: () => 'Да, удалить',
        okType: 'danger',
        cancelText: () => 'Нет',
        onOk() {
          deleteResource()
        },
        onCancel() {},
      })
    }

    return {
      title: formatString(props.title, data.value),
      breadcrumb: map(props.breadcrumb, (item) => ({
        ...item,
        breadcrumbName: formatString(item.breadcrumbName, data.value),
      })),
      data,
      putResource,
      handleDelete,
    }
  },
})
</script>

<style scoped></style>
