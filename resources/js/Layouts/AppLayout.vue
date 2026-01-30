<script setup>
import Navbar_home from '@/Components/Navbar_home.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <div>
         <head>
  <title>Application</title>
</head>

        <Navbar_home />

        <main class="min-h-screen bg-gray-100">
            <slot />
        </main>
    </div>
</template>
