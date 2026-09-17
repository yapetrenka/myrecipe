<template>
  <div>
    <div class="filter-panel" v-if="!showOnHome">
      <div class="filter-panel__category">
        <CategoryFilter
            :categories="categories"
            v-model="selectedCategory"
        />
      </div>
      <div class="filter-panel__search">
        <SearchBar @search="searchRecipes" />
      </div>
    </div>

    <div class="recipe-list">
      <div v-if="loading">Загрузка...</div>
      <div v-else-if="error">Ошибка: {{ error }}</div>
      <div class="recipe-list__items" v-else>
        <RecipeItem
            v-for="r in displayedRecipes"
            :key="r.id"
            :recipe="r"
            :category-name="categoryMap[r.category]"
            @toggle="toggleDescription"
        />
      </div>
    </div>
  </div>
</template>

<script>
import RecipeItem from '@/components/RecipeItem.vue';
import SearchBar from '@/components/SearchBar.vue';
import CategoryFilter from '@/components/CategoryFilter.vue';

export default {
  name: 'RecipeList',
  components: { RecipeItem, SearchBar, CategoryFilter },
  props: {
    showOnHome: {
      type: Boolean,
      default: false
    },
    initialCategory: {
      type: [String, Number],
      default: ''
    }
  },
  data() {
    return {
      recipes: [],
      categories: [],
      searchQuery: '',
      selectedCategory: '', // выбранная категория ('' — все)
      loading: true,
      error: null,
      perPage: 8,
      displayedCount: 8
    };
  },
  computed: {
    filteredRecipes() {
      const q = (this.searchQuery || '').trim().toLowerCase();

      return this.recipes.filter(r => {
        if (r && r.is_active && String(r.is_active).toLowerCase() === 'no') return false;
        if (this.showOnHome && r && r.is_show_home && String(r.is_show_home).toLowerCase() === 'no') return false;

        const title = (r.title || '').toLowerCase();
        const desc = (r.description || '').toLowerCase();
        const ingredients = (r.ingredients || '').toLowerCase();
        const matchesText = !q || title.includes(q) || desc.includes(q) || ingredients.includes(q);

        const categorySelected = (this.selectedCategory || '').toString();
        const matchesCategory = !categorySelected || String(r.category) === categorySelected;

        return matchesText && matchesCategory;
      });
    },
    categoryMap() {
      return this.categories.reduce((m, c) => {
        if (c && c.id != null) m[String(c.id)] = c.name || '';
        return m;
      }, {});
    },
    displayedRecipes() {
      return this.filteredRecipes.slice(0, this.displayedCount);
    }
  },
  methods: {
    searchRecipes(query) {
      this.searchQuery = query || '';
    },
    toggleDescription(id) {
      const idx = this.recipes.findIndex(r => r.id == id);
      if (idx === -1) return;
      this.recipes.splice(
          idx,
          1,
          Object.assign({}, this.recipes[idx], { show: !this.recipes[idx].show })
      );
    },
    loadMore() {
      const total = this.filteredRecipes.length;
      if (this.displayedCount >= total) return;
      this.displayedCount = Math.min(this.displayedCount + this.perPage, total);
    },
    onScroll() {
      if (this.loading) return;
      const nearBottom = (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 300);
      if (nearBottom) this.loadMore();
    },
    resetDisplayed() {
      this.displayedCount = this.perPage;
    }
  },
  watch: {
    initialCategory: {
      immediate: true,
      handler(newVal) {
        this.selectedCategory = newVal != null ? String(newVal) : '';
      }
    },
    selectedCategory() {
      this.resetDisplayed();
    },
    searchQuery() {
      this.resetDisplayed();
    }
  },
  mounted() {
    window.addEventListener('scroll', this.onScroll);

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
          this.categories = Array.isArray(data.recipe_category) ? data.recipe_category : [];
          const arr = (data && data.recipe) ? data.recipe : [];
          this.recipes = arr.map(r => ('show' in r ? r : Object.assign({}, r, { show: false })));

          // Сортировка по полю orders (числовая, по возрастанию)
          this.recipes.sort((a, b) => {
            const ao = Number(a && a.orders != null ? a.orders : 0);
            const bo = Number(b && b.orders != null ? b.orders : 0);
            return ao - bo;
          });

          if (this.initialCategory) {
            this.selectedCategory = String(this.initialCategory);
          }
        })
        .catch(err => { this.error = err.message; })
        .finally(() => { this.loading = false; });
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.onScroll);
  }
};
</script>

<style lang="scss" scoped>
@use '@styles/variables' as *;

.recipe-list {
  &__items {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin: 0;
    padding: 0;
    list-style: none;
  }
}

.filter-panel {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}
</style>
