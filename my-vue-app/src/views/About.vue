// File: `src/views/About.vue`
<template>
  <div>
    <h1 v-if="title">{{ title }}</h1>
    <div v-html="content"></div>
  </div>
</template>

<script>
import { getPages } from '@/services/api.js';

export default {
  name: 'About',
  data() {
    return { title: '', content: '' };
  },
  async created() {
    try {
      const pages = await getPages();
      const page = pages.find(p => String(p.id) === '2');
      if (page) {
        this.title = page.name || '';
        this.content = page.content || '';
      }
    } catch (err) {
      console.error('Ошибка загрузки страницы:', err);
    }
  }
};
</script>