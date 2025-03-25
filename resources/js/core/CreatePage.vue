<template>
  <page-wrapper :breadcrumb="breadcrumb" :title="title">
    <template #extra>
      <slot name="actions"></slot>
    </template>
    <template #default>
      <a-card size="small">
        <component
          :is="child"
          v-for="(child, index) in $slots.default()"
          :key="child.name"
          @on-submit="postResource"
        ></component>
      </a-card>
    </template>
  </page-wrapper>
</template>

<script>
import { defineComponent } from 'vue'
import PageWrapper from '~/Components/PageWrapper'
import useResource from '~/composables/useResource'
import { PageProps } from './types'

export default defineComponent({
  name: 'CreatePage',
  components: {
    PageWrapper,
  },
  props: {
    ...PageProps,
  },
  setup(props) {
    const { postResource } = useResource(props.resource, true, {
      extraPostParams: props.extraParams,
      resourceUrl: props.resourceUrl,
    })

    return {
      postResource,
    }
  },
})
</script>

<style scoped></style>
