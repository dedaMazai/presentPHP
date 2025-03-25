<template>
  <page-wrapper :breadcrumb="breadcrumb" :title="title">
    <template #extra>
      <a-button type="link" @click="showFilter = !showFilter">
        <template #icon>
          <FilterOutlined/>
        </template>
      </a-button>
      <slot name="actions"></slot>
    </template>
    <a-row :gutter="16" type="flex">
      <a-col v-if="showFilter && hasFilters" flex="265px">
        <a-card size="small">
          <filters :initial-values="filters" @on-submit="changeFilter">
            <slot :data="filters" name="filters"></slot>
          </filters>
        </a-card>
      </a-col>
      <a-col
        :class="{ 'full-screen': !showFilter || !hasFilters }"
        class="list-body"
        flex="auto"
      >
        <a-card size="small">
          <slot :data="data"></slot>
        </a-card>
      </a-col>
    </a-row>
  </page-wrapper>
</template>

<script>
import {ref} from 'vue'
import {FilterOutlined} from '@ant-design/icons-vue'
import PageWrapper from '~/Components/PageWrapper'
import Filters from '~/Components/Filters'
import useResource from '~/composables/useResource'
import {PageProps} from './types'

export default {
  name: 'ListPage',
  components: {
    PageWrapper,
    Filters,
    FilterOutlined,
  },
  props: {
    ...PageProps,
    initialFilters: {
      type: Object,
    },
  },
  setup(props, context) {
    const showFilter = ref(true)
    const {data, filters, changeFilter} = useResource(props.resource, false, {
      extraParams: props.extraParams,
      extraSortParams: props.extraParams,
      resourceUrl: props.resourceUrl,
    })

    return {
      showFilter,
      data,
      filters: {...props.initialFilters, ...filters},
      changeFilter,
      hasFilters:
        context.slots.filters &&
        context.slots.filters().findIndex((o) => o.type !== Comment) !== -1,
    }
  },
}
</script>

<style scoped>
.list-body {
  max-width: calc(100% - 265px);
}

.list-body.full-screen {
  max-width: 100%;
}
</style>
