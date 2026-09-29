<template>
  <div>
    <h1 v-if="title">{{ title }}</h1>
    <div v-html="content"></div>
  </div>
</template>

<script>
export default {
  name: 'About',
  data() {
    return {
      title: '',
      content: ''
    }
  },
  async created() {
    try {
      const res = await fetch('/api/local.json');
      if (!res.ok) throw new Error(res.statusText);
      const json = await res.json();
      const page = (json.pages || []).find(p => String(p.id) === '2');
      if (page) {
        this.title = page.name || '';
        this.content = page.content || '';
      }
    } catch (err) {
      console.error('Ошибка загрузки страницы:', err);
    }
  }
}
</script>
