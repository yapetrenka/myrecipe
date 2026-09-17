<template>
  <div class="recipes-page">
    <h1>{{ heading }}</h1>
    <RecipeList :initialCategory="categoryProp" />
  </div>
</template>

<script>
import RecipeList from '@/components/RecipeList.vue';

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
    loadCategoryName() {
      this.categoryName = '';
      this.loadingName = true;
      const id = this.categoryProp;
      if (!id) { this.loadingName = false; return; }

      const isLocalHostNames = ['localhost', '127.0.0.1', '::1'];
      const isLocal = isLocalHostNames.includes(window.location.hostname) || process.env.NODE_ENV === 'development';
      const url = isLocal ? '/api/local.json' : '/api.php';

      fetch(url)
          .then(async res => {
            const text = await res.text();
            const ct = (res.headers.get('Content-Type') || '').toLowerCase();
            if (!res.ok) throw new Error('HTTP ' + res.status);
            if (!ct.includes('application/json')) throw new Error('Unexpected response (not JSON): ' + text.slice(0, 200));
            try { return JSON.parse(text); } catch (e) { throw new Error('Invalid JSON: ' + e.message); }
          })
          .then(data => {
            const cats = Array.isArray(data.recipe_category) ? data.recipe_category : [];
            const found = cats.find(c => String(c.id) === id);
            this.categoryName = found ? found.name : '';
          })
          .catch(() => {
            this.categoryName = '';
          })
          .finally(() => { this.loadingName = false; });
    }
  }
};
</script>

<style scoped>
.recipes-page { padding: 20px; }
</style>
