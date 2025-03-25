import isEmpty from 'lodash/isEmpty'
import template from 'lodash/template'
import templateSettings from 'lodash/templateSettings'
import moment from 'moment'

export const prepareFiltersQuery = (filters = {}) => {
  const result = {}

  for (const field of Object.keys(filters)) {
    if (typeof filters[field] !== 'number' && isEmpty(filters[field])) {
      continue
    }

    const key = `filter[${field}]`

    result[key] = filters[field]
  }

  return result
}

export const globalDateFormat = 'YYYY-MM-DD'
export const globalDateTimeFormat = 'YYYY-MM-DD HH:mm'

export const formatDateTime = (value, format) => {
  return value && moment(value).format(format || globalDateTimeFormat)
}

templateSettings.interpolate = /{{([\s\S]+?)}}/g
export const formatString = (string, data) => {
  try {
    const compiled = template(string)
    return compiled(data)
  } catch (e) {
    return string
  }
}
