<script setup lang="ts">
import { onMounted, ref, type Ref } from 'vue'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { isAxiosError } from 'axios'
import { toast } from 'vue-sonner'
import IPInput from '@/components/dashvpn/modules/IPInput.vue'
import type Ip from '@/components/dashvpn/types/ip'
import { getServer, postServer } from '../../services/fetch'
import { ip2Str, str2Ip } from '@/lib/ip'

const dns = ref<Ip>({
    first: 0,
    second: 0,
    third: 0,
    fourth: 0,
})
const address = ref<Ip>({
    first: 0,
    second: 0,
    third: 0,
    fourth: 0,
})
const listen_port = ref(0)

async function handleSubmit() {
    try {
        const response = await postServer({
            ip: ip2Str(ip.value),
            listen_port: listen_port.value,
            address: ip2Str(address.value),
            dns: ip2Str(dns.value),
        });
        toast.success('Server updated')
        console.log('token', response.data.data);
    } catch (error) {
        if (isAxiosError(error) && error.response) {
            toast.error(error.response.data.message)
        } else {
            console.log(error)
        }
    }
}
const ip: Ref<Ip> = ref({
    first: 0,
    second: 0,
    third: 0,
    fourth: 0,
})
onMounted(async () => {
    const response = await getServer()
    ip.value = str2Ip(response.data.data.ip)
    listen_port.value = response.data.data.port
    address.value = str2Ip(response.data.data.address)
    dns.value = str2Ip(response.data.data.dns ?? '1.1.1.1')
})
</script>
<template>
    <Card class="w-full max-w-md">
        <CardHeader class="">
            <CardTitle class="h-full row-span-2 grid items-center">wg0</CardTitle>
            <CardDescription>IP: {{ ip2Str(ip) }} / DNS: {{ ip2Str(dns) }}</CardDescription>
        </CardHeader>
        <CardContent>
            <form id="loginform" @submit.prevent="handleSubmit">
                <div class="grid grid-cols-1 gap-4">
                    <Label for="address">Interface Address</Label>
                    <IPInput v-model="address" disabled-fourth />
                    <Label for="ip">Listen Port</Label>
                    <Input id="listen-port" type="number" required v-model="listen_port" />
                </div>
            </form>
        </CardContent>
        <CardFooter class="flex flex-col gap-2">
            <Button class="w-full" form="loginform">
                Actualizar
            </Button>
        </CardFooter>
    </Card>
</template>
