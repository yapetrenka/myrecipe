<template>
  <div class="category-filter">
    <div class="category-filter__lbl">Выберите категорию:</div>
    <select id="category" class="select-base" :value="modelValue" @change="onChange">
      <option value="">Все</option>
      <option
          v-for="c in categories"
          :key="c.id"
          :value="String(c.id)"
      >{{ c.name }}</option>
    </select>
  </div>
</template>

<script>
export default {
  name: 'CategoryFilter',
  props: {
    categories: { type: Array, default: () => [] },
    modelValue: { type: [String, Number], default: '' } // для Vue 3 v-model
  },
  methods: {
    onChange(e) {
      const val = e.target.value;
      this.$emit('update:modelValue', val); // Vue 3
      this.$emit('input', val);              // совместимость с Vue 2 / старым кодом
      this.$emit('select', val);             // дополнительное событие, если используется
    }
  }
}
</script>

<style lang="scss" scoped>
@use '@styles/variables' as *;
.category-filter {
  display: flex;
  align-items: center;
  &__lbl {
    margin-right: 10px;
    flex: none;
  }
}
</style>