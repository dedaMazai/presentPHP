<template>
  <a-form-item :label="label">
    <a-switch v-if="!installed" v-model:checked="value" :disabled="!is_published"/>
    <a-switch v-else v-model:checked="value" :disabled="true"/>
  </a-form-item>
</template>

<script>
import useField from '~/composables/useField'
import {InputProps} from './types'

export default {
  name: 'NewsSwitchInput',
  data() {
    return {
      installed: false
    }
  },
  props: {
    ...InputProps,
  },
  setup(props) {
    const {value} = useField(props.name)

    return {
      value,
    }
  },
  mounted() {
    if (this.is_published && this.value) {
      this.installed = true
      console.log(this.is_published, this.value)
    }
  }
}
</script>

<style scoped></style>


<!--<template>-->
<!--  <a-form-item :label="label" >-->
<!--    <a-switch v-if="!installed" v-model:checked="value" :disabled="disabled" v-model:value="disabled"/>-->
<!--    <a-switch v-else :checked="true" :disabled="true" v-model:value="disabled"/>-->
<!--  </a-form-item>-->
<!--</template>-->

<!--<script>-->
<!--import useField from '~/composables/useField'-->
<!--import {InputProps} from './types'-->

<!--export default {-->
<!--  name: 'NewsSwitchInput',-->
<!--  data() {-->
<!--    return {-->
<!--      installed: false-->
<!--    }-->
<!--  },-->
<!--  props: {-->
<!--    ...InputProps,-->
<!--  },-->
<!--  setup(props) {-->
<!--    const {value} = useField(props.is_checked)-->

<!--    return {-->
<!--      value,-->
<!--    }-->
<!--  },-->
<!--  created() {-->
<!--    if (this.props.disabled && this.props.is_checked) {-->
<!--      this.installed = this.props.disabled-->
<!--    } else {-->
<!--      this.installed = false;-->
<!--    }-->

<!--    if (this.props.is_checked && !this.props.disabled) {-->
<!--      this.installed = false;-->
<!--    }-->
<!--  },-->
<!--  updated() {-->
<!--    console.log(-->
<!--      this.installed, this.value, this.disabled, this.is_checked-->
<!--    )-->
<!--  }-->
<!--}-->
<!--</script>-->

<!--<style scoped></style>-->
