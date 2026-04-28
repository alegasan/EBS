<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import Heading  from '@/components/Heading.vue'; 
import { index } from '@/routes/bookings';
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Bookings',
                href: index(),
            },
            {
                title: 'Create',
                href: '#',
            },
        ],
    },
});

const form = useForm({
    'event_id': '',
    'attendee_id': '',
    'status': 'pending',
    'booked_at': '',
});

</script>

<template>
    <div class="p-6">

        <Head title="Create Booking" />
        <header class="mb-6">
            <Heading title="Create Booking" description="Fill out the form to create a new booking." />
        </header>
    </div>
</template>