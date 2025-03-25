<template>
  <a-layout class="basic-layout-content">
    <a-layout-content class="">
      <div class="basic-layout-content-wrapper">
        <slot/>
      </div>
    </a-layout-content>
  </a-layout>
</template>

<script>
</script>

<style scoped>
.basic-layout-content {
  height: 100vh;
}
.basic-layout-content-wrapper {
  margin: 24px 16px;
  padding: 24px;
  min-height: 280px;
}

</style>
