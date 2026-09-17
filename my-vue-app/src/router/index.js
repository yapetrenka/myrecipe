import { createRouter, createWebHistory } from 'vue-router'
import Main from '@/views/Main.vue'
import Recipes from '@/views/Recipes.vue'
import RecipePage from '@/views/RecipePage.vue'
import Features from '@/views/Features.vue'
import About from '@/views/About.vue'

const routes = [
    { path: '/', name: 'Main', component: Main },
    { path: '/features', name: 'Features', component: Features },

    // список всех рецептов
    { path: '/recipes', name: 'Recipes', component: Recipes, props: route => ({ category: '' }) },

    // маршруты категорий с префиксом "category"
    { path: '/recipes/category/:category', name: 'RecipesCategory', component: Recipes, props: true },

    { path: '/about', name: 'About', component: About },

    // детальная страница рецепта (останется /recipes/:url)
    { path: '/recipes/:url', name: 'Recipe', component: RecipePage, props: true }
]

const router = createRouter({
    history: createWebHistory('/'), // явно указать базу
    routes
})

export default router