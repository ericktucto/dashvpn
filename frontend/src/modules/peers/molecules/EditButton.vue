<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { Peer } from '../services/fetch';
import { EditIcon } from 'lucide-vue-next';

defineProps<{
    peer: Peer
}>()
defineEmits<{
    edit: [peer: Peer]
}>()
</script>
<template>
    <Button variant="secondary" @click="$emit('edit', peer)">
        <EditIcon />
    </Button>
</template>
