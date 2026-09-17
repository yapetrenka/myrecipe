import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useRecipesStore = defineStore('recipes', () => {
    const recipes = ref([
        { id: 1, title: 'Омлет', description: 'Яйца, молоко, соль. Взбить и жарить 3–4 минуты.', show: false },
        { id: 2, title: 'Салат Цезарь', description: 'Листья салата, курица, пармезан, соус Цезарь.', show: false },
        { id: 3, title: 'Паста с томатным соусом', description: 'Паста, томаты, чеснок, оливковое масло.', show: false }
    ])

    function toggle(id) {
        const r = recipes.value.find(x => x.id === id)
        if (r) r.show = !r.show
    }

    function add(recipe) {
        recipes.value.push({ ...recipe, id: Date.now(), show: false })
    }

    return { recipes, toggle, add }
})