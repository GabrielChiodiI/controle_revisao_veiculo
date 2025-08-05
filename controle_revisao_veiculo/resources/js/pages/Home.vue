<template>
  <HomeLayout>
    <div class="container mt-5">
      <div class="row g-4 justify-content-center">
        <div
          v-for="item in cards"
          :key="item.to"
          class="col-12 col-md-4 d-flex justify-content-center"
        >
          <Link
            :href="item.to"
            class="card card-home d-flex flex-column align-items-center justify-content-center text-decoration-none"
          >
            <i :class="item.icon" style="font-size:2.5rem; margin-bottom:0.8rem;"></i>
            <span class="h5 mb-0" style="color: var(--text);">{{ item.label }}</span>
          </Link>
        </div>
      </div>
    </div>
  </HomeLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import HomeLayout from '../layouts/HomeLayout.vue'

const cards = [
  { label: 'Clientes', to: '/clientes', icon: 'bi bi-people' },
  { label: 'Revisões', to: '/revisoes', icon: 'bi bi-tools' },
  { label: 'Relatórios', to: '/relatorios', icon: 'bi bi-bar-chart-line' }
]
</script>

<style scoped>
.card-home {
  width: 200px;
  height: 200px;
  background: var(--surface);
  color: var(--text);
  box-shadow: 0 4px 24px rgba(32, 98, 171, 0.10);
  border-radius: 18px;
  transition: box-shadow .2s, background .2s;
  cursor: pointer;
}
.card-home:hover {
  background: var(--primary);
  color: #fff;
  box-shadow: 0 8px 32px rgba(32, 98, 171, 0.17);
}
.card-home i {
  color: var(--primary);
}
.card-home:hover i {
  color: #fff;
}
</style>