<script setup lang="ts">
import { NetworkIcon, ServerIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button'

defineProps<{
    active: 'server' | 'peers' | null
}>()
defineEmits<{
    go: [name: '/server' | '/peers'],
}>()
</script>
<template>
    <h3 class="p-4">WIREGUARD</h3>
    <ul class="p-4">
        <li class="mb-2">
            <Button class="w-full justify-start" :variant="active === 'peers' ? 'default' : 'ghost'"
                @click="$emit('go', '/peers')">
                <NetworkIcon />
                Peers
            </Button>
        </li>
        <li>
            <Button class="w-full justify-start" :variant="active === 'server' ? 'default' : 'ghost'"
                @click="$emit('go', '/server')">
                <ServerIcon />
                Server
            </Button>
        </li>
    </ul>
</template>
