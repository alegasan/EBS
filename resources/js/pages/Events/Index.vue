<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading  from '@/components/Heading.vue';
import { index as events } from '@/routes/events';
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Events',
                href: events(),
            },
        ],
    },
});
</script>

<template>
    <div class="p-6">

        <Head title="Events" />
        <header class="mb-6">
            <Heading title="Events" description="Manage your events." />
        </header>
    </div>
</template>