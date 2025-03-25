import {computed, unref} from 'vue'
import {usePage} from '@inertiajs/inertia-vue3'
import isPlainObject from 'lodash/isPlainObject'
import transform from 'lodash/transform'

export default function useDictionary(resource) {
  if (!resource) {
    return;
  }

  const pageData = computed(() => usePage().props.value[resource])
  let data = []

  if (isPlainObject(pageData.value)) {
    data = transform(
      unref(pageData),
      function (result, value, key) {
        result.push({key: key, label: value})
      },
      []
    )
  } else {
    data = pageData.value
  }

  return {
    data,
  }
}
