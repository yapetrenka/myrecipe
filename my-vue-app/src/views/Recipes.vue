<template>
  <div class="recipes-page">
    <h1>{{ heading }}</h1>
    <RecipeList :initialCategory="categoryProp" />
  </div>
</template>

<script>
import RecipeList from '@/components/RecipeList.vue';
import { getCategories } from '@/services/api.js';

export default {
  name: 'Recipes',
  components: { RecipeList },
  props: {
    category: {
      type: [String, Number],
      default: ''
    }
  },
  data() {
    return {
      categoryName: '',
      loadingName: true
    };
  },
  computed: {
    categoryProp() {
      return this.category != null ? String(this.category) : '';
    },
    heading() {
      return this.categoryName ? `Категория: ${this.categoryName}` : 'Все рецепты';
    }
  },
  watch: {
    category: {
      immediate: true,
      handler() {
        this.loadCategoryName();
      }
    }
  },
  methods: {
    async loadCategoryName() {
      this.categoryName = '';
      this.loadingName = true;
      const id = this.categoryProp;
      if (!id) { this.loadingName = false; return; }

      try {
        const cats = await getCategories();
        const found = Array.isArray(cats) ? cats.find(c => String(c.id) === id) : null;
        this.categoryName = found ? found.name : '';
      } catch (e) {
        this.categoryName = '';
      } finally {
        this.loadingName = false;
      }
    }
  }
};
</script>

<style scoped>

</style>
