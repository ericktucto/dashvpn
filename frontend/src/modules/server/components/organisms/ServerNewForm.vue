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
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { isAxiosError } from 'axios'
import { toast } from 'vue-sonner'
import IPInput from '@/components/dashvpn/modules/IPInput.vue'
import type Ip from '@/components/dashvpn/types/ip'
import { getInterfaces, postServer } from '../../services/fetch'
import { ip2Str } from '@/lib/ip'
import { useRouter } from 'vue-router'
import InstructionsStack from '@/components/dashvpn/InstructionsStack.vue'
import { NativeSelect, NativeSelectOption } from '@/components/ui/native-select'

const router = useRouter()

const interfaces = ref<string[]>([])

const selectedInterface = ref('')

const post_up = ref<string[]>([])
const post_down = ref<string[]>([])

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
    fourth: 1,
})
const listen_port = ref(0)

async function handleSubmit() {
    try {
        await postServer({
            ip: ip2Str(ip.value),
            listen_port: listen_port.value,
            address: ip2Str(address.value),
            dns: ip2Str(dns.value),
            post_up: post_up.value,
            post_down: post_down.value,
            'interface': selectedInterface.value
        });
        toast.success('Server created')
        router.push('/server')
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

type ConfigData = {
    address: Ip
    listen_port: number
    dns: Ip
    post_up: string[]
    post_down: string[]
    'interface': string
}
function setConfig(data: ConfigData) {
    address.value = data.address
    listen_port.value = data.listen_port
    dns.value = data.dns

    post_up.value = data.post_up
    post_down.value = data.post_down
    selectedInterface.value = data['interface']
}

defineExpose({
    setConfig,
})

onMounted(() => {
    getInterfaces().then(res => interfaces.value = res.data.data)
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
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <Label>Endpoint</Label>
                    <IPInput v-model="ip" />
                    <Label>Listen Port</Label>
                    <Input type="number" required v-model="listen_port" />
                    <Label>Interface Address</Label>
                    <IPInput v-model="address" disabled-fourth />
                    <Label>DNS</Label>
                    <IPInput v-model="dns" />
                    <Label>Interface</Label>
                    <NativeSelect class="w-full" v-model="selectedInterface">
                        <NativeSelectOption v-for="i in interfaces" :key="`${i}-option`" :value="i">{{ i }}
                        </NativeSelectOption>
                    </NativeSelect>
                </div>
                <Tabs default-value="postup">
                    <TabsList>
                        <TabsTrigger value="postup">
                            Post Up
                        </TabsTrigger>
                        <TabsTrigger value="postdown">
                            Post Down
                        </TabsTrigger>
                    </TabsList>
                    <TabsContent value="postup">
                        <div class="grid grid-cols-1 gap-2">
                            <small>Lista de instrucciones para cuando el servidor se inicie.</small>
                            <InstructionsStack v-model:instructions="post_up" />
                        </div>
                    </TabsContent>
                    <TabsContent value="postdown">
                        <div class="grid grid-cols-1 gap-2">
                            <small>Lista de instrucciones para cuando el servidor se detiene.</small>
                            <InstructionsStack v-model:instructions="post_down" />
                        </div>
                    </TabsContent>
                </Tabs>
            </form>
        </CardContent>
        <CardFooter class="flex flex-col gap-2">
            <Button class="w-full" form="loginform">
                Guardar
            </Button>
        </CardFooter>
    </Card>
</template>
