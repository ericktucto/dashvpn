<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { getConfigPeer, type Peer } from '../services/fetch'
import { DownloadIcon } from 'lucide-vue-next';

defineProps<{
    peer: Peer
}>()

async function handleDownload(peer: Peer) {
    const res = await getConfigPeer(peer.slug)
    const blob = new Blob([res.data], { type: 'text/plain' })
    const url = URL.createObjectURL(blob)

    const a = document.createElement('a')
    a.href = url
    a.download = `${peer.slug}.conf`
    a.click()

    URL.revokeObjectURL(url)
}

</script>
<template>
    <Button variant="outline" @click="handleDownload(peer)">
        <DownloadIcon />
    </Button>
</template>
