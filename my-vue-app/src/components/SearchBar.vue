<template>
  <div class="search-bar">
    <input class="inp-base" type="text" v-model="query" @input="onInput" placeholder="Поиск по рецептам" />
  </div>
</template>

<script>
export default {
  name: 'SearchBar',
  data() {
    return {
      query: ''
    };
  },
  methods: {
    onInput() {
      this.$emit('search', this.query);
    }
  }
};
</script>

<style lang="scss" scoped>
@use '@styles/variables' as *;
.search-bar {
  margin-bottom: 20px;
  width: 240px;
}
</style>