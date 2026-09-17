<template>
  <router-link class="recipe-item" :to="`/recipes/${recipe.url}`">
    <img v-if="imageUrl" :src="imageUrl" :alt="recipe.title" class="recipe-item__image" />
    <div class="recipe-item__name">{{ recipe.title }}</div>
    <div v-if="categoryName" class="recipe-item__category">{{ categoryName }}</div>
    <p class="recipe-item__description">{{ recipe.description }}</p>
  </router-link>
</template>

<script>
export default {
  name: 'RecipeItem',
  props: {
    recipe: { type: Object, required: true },
    categoryName: { type: String, default: '' }
  },
  computed: {
    imageUrl() {
      const img = this.recipe && this.recipe.image;
      if (!img) return '';
      try {
        const str = String(img);
        const re = /s:3:"big";[\s\S]*?s:4:"file";s:\d+:"([^"]+)"/;
        const m = re.exec(str);
        if (!m) return '';
        // приводим экранированные \/ к обычным /
        return m[1].replace(/\\\//g, '/');
      } catch (e) {
        return '';
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@use '@styles/variables' as *;
.recipe-item {
  background: $bg-color;
  padding: 20px;
  border-radius: $border-radius-base;
  transition: transform 0.2s;
  display: flex;
  flex-direction: column;

  &:hover {
    transform: scale(1.02);
  }
  &__image {
    width: 100%;
    object-fit: cover;
    border-radius: $border-radius-base;
    margin-bottom: 12px;
  }
  &__name {
    font-size: 22px;
    margin: 0 0 10px;
  }

  &__category {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 8px;
  }

  &__description {
    flex: 1;
    margin: 0 0 10px;
    color: $base-color-light;
    font-size: .8em;
  }
}
</style>
