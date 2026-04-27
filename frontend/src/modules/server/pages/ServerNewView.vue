<script setup lang="ts">
import ContainerCenter from '@/layouts/ContainerCenter.vue';
import ServerNewForm from '../components/organisms/ServerNewForm.vue';
import { getRecommended } from '../services/fetch';
import { onMounted, ref } from 'vue';
import { str2Ip } from '@/lib/ip';

const form = ref<InstanceType<typeof ServerNewForm> | null>(null)

onMounted(() => {
    getRecommended().then((response) => {
        const data = response.data.data
        if (form.value) {
            form.value.setConfig({
                ip: str2Ip('0.0.0.0'),
                address: str2Ip(data.address),
                listen_port: data.listen_port,
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
