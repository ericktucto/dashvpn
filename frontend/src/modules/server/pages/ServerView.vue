<script setup lang="ts">
import ContainerCenter from '@/layouts/ContainerCenter.vue';
import ServerNewForm from '../components/organisms/ServerNewForm.vue';
import { onMounted, ref } from 'vue';
import { getServer } from '../services/fetch';
import { str2Ip } from '@/lib/ip';

const form = ref<InstanceType<typeof ServerNewForm> | null>(null)

onMounted(() => {
    getServer().then((response) => {
        const data = response.data.data
        if (form.value) {
            form.value.setConfig({
                ip: str2Ip(data.ip),
                address: str2Ip(data.address),
                listen_port: data.port,
                dns: str2Ip(data.dns),
                post_up: data.post_up,
                post_down: data.post_down,
                'interface': data['interface'],
            })
        }
    })
})

</script>
<template>
    <ContainerCenter>
        <ServerNewForm ref="form" />
    </ContainerCenter>
</template>
