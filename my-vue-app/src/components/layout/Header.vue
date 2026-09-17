<template>
  <header class="main-header">
    <div class="main-header__logo">
      <router-link to="/" class="main-header__logo-link">
        <img src="@images/logo.svg" alt="Логотип" />
      </router-link>
    </div>
    <nav class="main-header__nav">
      <router-link class="main-header__nav-link" to="/">Главная</router-link>
      <router-link class="main-header__nav-link" :to="{ name: 'Recipes' }">Рецепты</router-link>
      <template v-if="categories.length">
        <router-link
            class="main-header__nav-link-sub"
            v-for="c in categories"
            :key="c.id"
            :to="{ name: 'RecipesCategory', params: { category: String(c.id) } }"
        >
          {{ c.name }}
        </router-link>
      </template>
      <router-link class="main-header__nav-link" to="/about">Обо мне</router-link>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'Header',
  data() {
    return { categories: [] };
  },
  mounted() {
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
        })
        .catch(() => { this.categories = []; });
  }
};
</script>

<style lang="scss" scoped>
@use '@styles/variables' as *;

.main-header {
  padding: 30px 0;
  &__logo {
    position: relative;
    display: flex;
    justify-content: center;
    &:before {
      border-top: 1px solid $base-color;
      content: '';
      position: absolute;
      left: 0;
      right: 0;
      top: 50%;
    }
    &-link {
      background-color: #fff;
      display: block;
      padding: 0 25px;
      position: relative;
      z-index: 1;
      img {
        width: 100px;
      }
    }
  }
  &__nav {
    display: flex;
    justify-content: center;
    align-items: baseline;
    margin-top: 20px;
    a {
      text-decoration: none;
    }
    &-link {
      color: $base-color;
      font-size: 18px;
      margin: 0 10px;
      &-sub {
        font-size: 14px;
        opacity: .8;
        margin: 0 5px;
      }
    }
  }
}
</style>