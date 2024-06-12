<template>
  <div id="app" class="min-vh-100 bg-light">
    <div class="container">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: 'App',
}
</script>

<style>
body {
  font-family: 'Arial', sans-serif;
  background-color: #e0f7fa;
  margin: 0;
}

.container {
  padding: 20px;
}
</style>
