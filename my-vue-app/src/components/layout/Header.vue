<template>
  <header class="main-header" :class="{ 'is-open': isOpen }">
    <div class="main-header__logo">
      <router-link to="/" class="main-header__logo-link">
        <img src="@images/logo.svg" alt="Логотип" />
      </router-link>
    </div>

    <button
        class="main-header__burger"
        :aria-expanded="isOpen ? 'true' : 'false'"
        aria-label="Toggle menu"
        @click="toggleMenu"
    >
      <span class="main-header__burger-bar" />
      <span class="main-header__burger-bar" />
      <span class="main-header__burger-bar" />
    </button>

    <nav class="main-header__nav" v-show="isOpen || !isMobile">
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
import { getCategories } from '@/services/api.js';

export default {
  name: 'Header',
  data() {
    return {
      categories: [],
      isOpen: false,
      isMobile: false
    };
  },
  async mounted() {
    this.checkViewport();
    window.addEventListener('resize', this.checkViewport);
    this.$watchRouteClose();

    try {
      this.categories = await getCategories();
    } catch (e) {
      this.categories = [];
    }
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.checkViewport);
  },
  methods: {
    toggleMenu() {
      this.isOpen = !this.isOpen;
    },
    checkViewport() {
      this.isMobile = window.innerWidth <= 768;
      if (!this.isMobile) this.isOpen = false;
    },
    $watchRouteClose() {
      // закрывать меню при навигации
      this.$router && this.$router.afterEach(() => { this.isOpen = false; });
    }
  }
};
</script>

<style lang="scss" scoped>
@use '@styles/variables' as *;

.main-header {
  padding: 30px 0;
  position: relative;

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
      background-color: $bg-color;
      display: block;
      padding: 0 25px;
      position: relative;
      z-index: 2;
    }
  }

  &__burger {
    display: none;
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

  /* mobile styles */
  @media (max-width: 768px) {
    background-color: $bg-color;
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 60px;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    padding: 5px 15px;
    z-index: 100;

    &__logo {
      justify-content: flex-start;
      &:before {
        display: none;
      }
      &-link {
        padding: 0;
        width: 120px;
      }
    }
    &__burger {
      display: flex;
      flex-direction: column;
      width: 40px;
      height: 32px;
      align-items: center;
      justify-content: center;
      background: transparent;
      border: none;
      cursor: pointer;
      z-index: 3;
      padding: 0;

      &-bar {
        display: block;
        width: 24px;
        height: 2px;
        background: $base-color;
        margin: 4px 0;
        transition: transform .25s ease, opacity .25s ease;
      }
    }

    &__nav {
      position: absolute;
      left: 0;
      right: 0;
      top: 100%;
      background: $bg-color;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 10px 0;
      margin-top: 0;
      transform-origin: top;
      transition: transform .22s ease, opacity .22s ease;
      opacity: 0;
      pointer-events: none;
      transform: translateY(-10px) scaleY(0.98);
      box-shadow: 0 6px 18px rgba(0,0,0,0.08);
      z-index: 2;

      a {
        margin: 0;
        padding: 8px 16px;
        width: 100%;
        text-align: center;
      }

      &-link {
        margin: 6px 0;
      }
    }

    /* when open */
    &.is-open {
      .main-header__nav {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0) scaleY(1);
      }
      .main-header__burger {
        .main-header__burger-bar:nth-child(1) { transform: translateY(10px) rotate(45deg); }
        .main-header__burger-bar:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .main-header__burger-bar:nth-child(3) { transform: translateY(-10px) rotate(-45deg); }
      }
    }
  }
}
</style>
