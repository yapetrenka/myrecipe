<template>
  <div class="home">
    <h1>Добро пожаловать на сайт рецептов</h1>
    <RecipeList :showOnHome="true" />
    <div class="center mt-40">
      <router-link to="/recipes" class="btn btn_primary">Посмотреть все рецепты</router-link>
    </div>
  </div>
</template>

<script>
import RecipeList from '../components/RecipeList.vue';

export default {
  name: 'Home',
  components: {
    RecipeList
  }
};
</script>