<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading  from '@/components/Heading.vue';
import { index as bookings } from '@/routes/bookings';
import { Button } from '@/components/ui/button';
import { PlusIcon } from 'lucide-vue-next';
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Bookings',
                href: bookings(),
            },
        ],
    },
});
</script>

<template>
    <div class="p-6">

    <Head title="Bookings" />
    <header class="mb-6 flex items-center justify-between">

        <Heading title="Bookings" description="Manage your event bookings." />
        <Button
            variant="outline"
            size="sm"
            as-child
            class="cursor-pointer"
        >
            <a href="#"> 
                <PlusIcon class="mr-2" />
                Create Booking
            </a>        </Button>
    </header>
    </div>
</template>