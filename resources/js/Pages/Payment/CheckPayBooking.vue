<template>
  <span>{{ text }}</span>
  <div class="container">
    <div class="loading-bar">
      <div class="percentage" :style="{'width' : percentage + '%'}">

      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'CheckPayBooking',
  data() {
    return {
      percentage: 0,
      text: 'Проверка оплаты'
    }
  },
  created() {
    var intval = setInterval(() => {
      if (this.percentage < 100) {
        this.percentage += 1;
        if (this.percentage > 70)
          this.text = 'Успешно. Возврат в приложение';
      }
      else {
        clearInterval(intval);
        window.location.href = '/pay-booking/success'
      }
    }, 150);
  }
}
</script>

<style lang="scss">
#app {
  height: 100%;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  background: linear-gradient(to bottom, #efefef, #ccc);
}
.loading-bar {
  position: relative;
  width: 300px;
  height: 30px;
  border-radius: 15px;
  overflow: hidden;
  border-bottom: 1px solid #ddd;
  box-shadow: inset 0 1px 2px rgba($color: #000, $alpha: .4), 0 -1px 1px #fff, 0 1px 0 #fff;
  .percentage {
    position: absolute;
    top: 1px; left: 1px; right: 1px;
    display: block;
    height: 100%;
    border-radius: 15px;
    background-color: #a5df41;
    background-size: 30px 30px;
    background-image: linear-gradient(135deg, rgba($color: #fff, $alpha: .15) 25%, transparent 25%,
      transparent 50%, rgba($color: #fff, $alpha: .15) 50%,
      rgba($color: #fff, $alpha: .15) 75%, transparent 75%,
      transparent);
    animation: animate-stripes 2s linear infinite;
  }
}

@keyframes animate-stripes {
  0% {background-position: 0 0;}
  100% {background-position: 60px 0;}
}
</style>
