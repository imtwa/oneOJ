<template>
  <div id="app">
    <ViewHead></ViewHead>
    <div style="min-height: 85vh;width:100%">
      <router-view></router-view>
    </div>

  </div>
</template>

<script>
import ViewHead from "@/components/viewHead.vue";

export default {
  name: 'App',
  components: {
    'ViewHead': ViewHead
  },
  mounted(){
    this.$store.commit("getLocalStorage");
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
</style>
