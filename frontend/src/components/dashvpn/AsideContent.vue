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
        <li>
            <Button class="w-full justify-start" :variant="active === 'server' ? 'default' : 'ghost'"
                @click="$emit('go', '/server')">
                <ServerIcon />
                Server
            </Button>
        </li>
        <li>
            <Button class="w-full justify-start" :variant="active === 'peers' ? 'default' : 'ghost'">
                <NetworkIcon />
                Peers
            </Button>
        </li>
    </ul>
</template>
