<template>
  <Layout>
    <transition name="fade" mode="out-in">
      <router-view :key="$route.fullPath" />
    </transition>
  </Layout>
</template>

<script>
import Layout from './components/layout/Layout.vue';

export default {
  name: 'App',
  components: {
    Layout
  },
  mounted() {
    document.body.classList.add('loaded');
  },
};
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease-in-out;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
