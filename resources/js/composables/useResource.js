import {computed, provide, reactive, ref, toRaw} from 'vue'
import {Inertia} from '@inertiajs/inertia'
import {usePage} from '@inertiajs/inertia-vue3'
import isObject from 'lodash/isObject'
import pickBy from 'lodash/pickBy'
import identity from 'lodash/identity'
import {message} from 'ant-design-vue'

const route = window.route

const defaultOptions = {
  extraParams: [],
  extraPostParams: [],
  extraPutParams: [],
  extraDeleteParams: [],
  extraSortParams: [],
  resourceUrl: null,
}

export default function useResource(source, singleMode = false, config = {}) {
  if (!source) {
    throw `useResource - source: ${source} не найден!`
  }
  const options = {...defaultOptions, ...config}
  const isSingle = ref(singleMode)
  const dataSource = computed(() =>
    isSingle.value
      ? usePage().props.value[source]
      : usePage().props.value[source]?.data
  )
  const currentPage = computed(
    () => usePage().props.value[source]?.current_page
  )
  const total = computed(() => usePage().props.value[source]?.total)
  const perPage = computed(() => usePage().props.value[source]?.per_page)
  const defaultFilters = computed(() => usePage().props.value.defaultFilters)
  const defaultSorter = computed(() => usePage().props.value.defaultSorter)

  const params = reactive({
    filters: {...defaultFilters.value},
    sorter: defaultSorter.value,
    pagination: {
      current: currentPage,
      total: total,
      pageSize: perPage,
    },
  })

  const getResource = () => {
    Inertia.get(
      route(options.resourceUrl || source, [...options.extraParams]),
      pickBy(
        {
          // ...prepareFiltersQuery(params.filters),
          filter: pickBy(toRaw(params.filters), identity),
          page: toRaw(params.pagination.current),
          sort: toRaw(params.sorter),
        },
        identity
      )
    )
  }

  const postResource = (data) => {
    Inertia.post(
      route(`${options.resourceUrl || source}.store`, [
        ...options.extraPostParams,
      ]),
      data,
      {
        onFinish: () => message.success('Запись сохранена.'),
      }
    )
  }

  const putResource = (data) => {
    Inertia.put(
      route(`${options.resourceUrl || source}.update`, [
        ...options.extraPutParams,
        data.id,
      ]),
      data,
      {
        onFinish: () => message.success('Запись сохранена.'),
      }
    )
  }

  const deleteResource = () => {
    Inertia.delete(
      route(`${options.resourceUrl || source}.destroy`, [
        ...options.extraDeleteParams,
        dataSource.value.id,
      ])
    )
  }

  const sortResource = (order) => {
    Inertia.put(
      route(`${options.resourceUrl || source}.sort`, [
        ...options.extraSortParams,
      ]),
      {order}
    )
  }

  const changePage = (nextPage) => {
    params.pagination.current = nextPage
    getResource()
  }

  const changeSort = (sort) => {
    params.sorter.value = sort
    getResource()
  }

  const changeFilter = (field, value) => {
    if (isObject(field)) {
      params.filters = {...params.filters, ...field}
    } else {
      params.filters[field] = value
    }
    getResource()
  }

  const changeParams = (newParams = {}) => {
    if (newParams.filters) {
      params.filters = {...params.filters, ...newParams.filters}
    }
    params.pagination = { ...params.pagination, current: newParams.page }
    params.sorter = newParams.sort
    getResource()
  }

  const resourceObject = {
    data: dataSource,
    filters: params.filters,
    sorter: params.sorter,
    pagination: params.pagination,
    getResource,
    postResource,
    putResource,
    deleteResource,
    sortResource,
    changePage,
    changeSort,
    changeFilter,
    changeParams,
  }

  provide('$sourceName', options.resourceUrl || source)
  provide('$resource', resourceObject)

  return resourceObject
}
