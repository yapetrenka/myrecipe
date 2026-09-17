<template>
  <div>
    <h1>Фичи</h1>
    <div v-if="loading">Загрузка...</div>
    <div v-else-if="error">Ошибка: {{ error }}</div>
    <ul v-else>
      <li v-for="item in features" :key="item.id">{{ item.name }}</li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'Features',
  data() {
    return {
      features: [],
      loading: true,
      error: null,
    };
  },
  mounted() {
    const isLocalHostNames = ['localhost', '127.0.0.1', '::1'];
    const isLocal = isLocalHostNames.includes(window.location.hostname) || process.env.NODE_ENV === 'development';
    const url = isLocal ? '/api/local.json' : '/api.php';

    fetch(url)
        .then(async res => {
          const text = await res.text();
          const ct = (res.headers.get('Content-Type') || '').toLowerCase();
          if (!res.ok) throw new Error(`HTTP ${res.status}`);
          if (!ct.includes('application/json')) throw new Error('Unexpected response (not JSON): ' + text.slice(0, 200));
          try { return JSON.parse(text); } catch (e) { throw new Error('Invalid JSON: ' + e.message); }
        })
        .then(data => {
          // используем только массив features из ответа
          this.features = (data && data.features) ? data.features : [];
        })
        .catch(err => { this.error = err.message; })
        .finally(() => { this.loading = false; });
  },
};
</script>

<style scoped>
/* небольшой стиль */
</style>