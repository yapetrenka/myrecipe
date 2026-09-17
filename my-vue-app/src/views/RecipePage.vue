<template>
  <div class="recipe-detail">
    <div v-if="loading">Загрузка...</div>
    <div v-else-if="error">Ошибка: {{ error }}</div>
    <div v-else-if="!recipe">Рецепт не найден</div>
    <div v-else>
      <h1>{{ recipe.title }}</h1>
      <p class="recipe-detail__description">{{ recipe.description }}</p>

      <!-- если есть хоть одно изображение — показываем карусель -->
      <div class="recipe-detail__layout">
        <Carousel v-if="carouselItems.length" class="recipe-detail__carousel" v-bind="config">
          <Slide v-for="(src, idx) in carouselItems" :key="idx">
            <img :src="src" :alt="recipe.title + ' — ' + (idx + 1)" class="recipe-detail__image" @click="showLightbox(index)" />
          </Slide>
          <template #addons>
            <Navigation />
            <Pagination />
          </template>
        </Carousel>
        <vue-easy-lightbox
            :visible="lightboxVisible"
            :imgs="lightboxImages"
            :index="lightboxIndex"
            @hide="lightboxVisible = false"
        />
        <div class="recipe-detail__ingredients" v-if="recipe.ingredients">
          <div v-html="recipe.ingredients"></div>
        </div>
      </div>
      <div class="recipe-detail__instructions" v-if="recipe.content">
        <div v-html="cleanedContent"></div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'
import 'vue3-carousel/dist/carousel.css'
import VueEasyLightbox from 'vue-easy-lightbox'

export default {
  name: 'RecipePage',
  components: {
    Carousel,
    Slide,
    Pagination,
    Navigation,
    VueEasyLightbox
  },
  setup() {
    const route = useRoute()
    const urlParam = route.params.url

    const recipe = ref(null)

    // основной рисунок: два варианта размеров
    const imageUrlBig = ref(null)
    const imageUrlSource = ref(null)

    // галерея: два варианта размеров
    const galleryImagesBig = ref([])
    const galleryImagesSource = ref([])

    const loading = ref(true)
    const error = ref(null)

    // лайтбокс
    const lightboxVisible = ref(false)
    const lightboxIndex = ref(0)

    // вытаскивает путь файла для нужного размера (например 'big' или 'source')
    function parseImageSize(serialized, size) {
      if (!serialized || typeof serialized !== 'string') return null
      const re = new RegExp('s:\\d+:"' + size + '"[\\s\\S]*?s:4:"file";s:\\d+:"([^"]+)"')
      const m = re.exec(serialized)
      if (!m) return null
      return m[1].replace(/\\\//g, '/')
    }

    function showLightbox(idx) {
      const i = Number(idx)
      lightboxIndex.value = Number.isNaN(i) ? 0 : i
      lightboxVisible.value = true
    }

    // карусель использует big
    const carouselItems = computed(() => {
      const out = []
      if (imageUrlBig.value) out.push(imageUrlBig.value)
      if (Array.isArray(galleryImagesBig.value) && galleryImagesBig.value.length) {
        out.push(...galleryImagesBig.value)
      }
      return out
    })

    // лайтбокс использует source
    const lightboxImages = computed(() => {
      const out = []
      if (imageUrlSource.value) out.push(imageUrlSource.value)
      if (Array.isArray(galleryImagesSource.value) && galleryImagesSource.value.length) {
        out.push(...galleryImagesSource.value)
      }
      return out
    })

    // чистим content от экранированных слешей и декодируем сущности
    const cleanedContent = computed(() => {
      const raw = recipe.value && recipe.value.content ? recipe.value.content : ''
      if (!raw) return ''
      // убрать экранирующие обратные слеши перед кавычками и слешами
      let s = raw.replace(/\\\"/g, '"').replace(/\\\//g, '/')
      // декодировать HTML-сущности и нормализовать HTML через DOMParser
      try {
        const doc = new DOMParser().parseFromString(s, 'text/html')
        return doc.body.innerHTML || s
      } catch (e) {
        return s.replace(/&quot;/g, '"')
      }
    })

    onMounted(async () => {
      try {
        const isLocalHostNames = ['localhost', '127.0.0.1', '::1']
        const isLocal = isLocalHostNames.includes(window.location.hostname) || process.env.NODE_ENV === 'development'
        const apiUrl = isLocal ? '/api/local.json' : '/api.php'

        const res = await fetch(apiUrl)
        const text = await res.text()
        const ct = (res.headers.get('Content-Type') || '').toLowerCase()
        if (!res.ok) throw new Error('HTTP ' + res.status)
        if (!ct.includes('application/json')) throw new Error('Unexpected response (not JSON)')
        const data = JSON.parse(text)

        const arr = (data && data.recipe) ? data.recipe : []
        const found = arr.find(r => String(r.url) === String(urlParam)) || null

        if (!found) {
          recipe.value = null
          galleryImagesBig.value = []
          galleryImagesSource.value = []
          imageUrlBig.value = null
          imageUrlSource.value = null
        } else {
          if (found.is_active && String(found.is_active).toLowerCase() === 'no') {
            recipe.value = null
            galleryImagesBig.value = []
            galleryImagesSource.value = []
            imageUrlBig.value = null
            imageUrlSource.value = null
          } else {
            recipe.value = found

            if (found.image) {
              const bigPath = parseImageSize(found.image, 'big')
              const srcPath = parseImageSize(found.image, 'source')
              if (bigPath) imageUrlBig.value = bigPath
              if (srcPath) imageUrlSource.value = srcPath
            }

            const galleryArr = Array.isArray(data.recipe_gallery) ? data.recipe_gallery : []
            const bigs = []
            const srcs = []
            galleryArr
                .filter(g => g && (g.recipe_id != null) && String(g.recipe_id) === String(found.id))
                .forEach(g => {
                  const b = parseImageSize(g.image, 'big')
                  const s = parseImageSize(g.image, 'source')
                  if (b) bigs.push(b)
                  if (s) srcs.push(s)
                })
            galleryImagesBig.value = bigs
            galleryImagesSource.value = srcs
          }
        }
      } catch (e) {
        error.value = e.message
      } finally {
        loading.value = false
      }
    })

    return {
      recipe,
      imageUrlBig,
      imageUrlSource,
      galleryImagesBig,
      galleryImagesSource,
      loading,
      error,
      carouselItems,
      // лайтбокс
      lightboxVisible,
      lightboxImages,
      lightboxIndex,
      showLightbox,
      cleanedContent
    }
  }
}
</script>


<style lang="scss">
@use '@styles/variables' as *;

.recipe-detail {
  max-width: 1000px;
  margin: 0 auto;
  &__description {
    color: $base-color-light;
    text-align: center;
    margin-bottom: 30px;
  }

  &__layout {
    display: flex;
  }

  &__carousel {
    flex: none;
    width: 50%;
  }

  &__image {
    max-width: 100%;
    border-radius: $border-radius-base;
    display: block;
    margin: 0 auto;
    cursor: pointer;
  }

  &__ingredients {
    margin-left: 50px;
    flex: 1;
    table {
      border: none;
      line-height: 1.1;
      width: 100%;
      td {
        border: none;
        padding: 10px 0;
        border-bottom: 1px dashed #d1d1d1;
        &:last-child {
          text-align: right;
        }
      }
      tr {
        &:last-child {
          td {
            border-bottom: none;
          }
        }
      }
    }
  }

  &__instructions {
    margin-top: 50px;
  }
}
</style>
