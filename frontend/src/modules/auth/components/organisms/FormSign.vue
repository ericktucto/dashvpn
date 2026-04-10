<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardAction,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { login } from '@/modules/auth/services/fetch'
import { useRouter } from 'vue-router'
import { isAxiosError } from 'axios'
import { toast } from 'vue-sonner'

const router = useRouter()
const username = ref('')
const password = ref('')

async function handleSubmit() {
    try {
        const response = await login(username.value, password.value);
        localStorage.setItem('token', response.data.data);
        router.push({ name: 'dashboard' })
    } catch (error) {
        if (isAxiosError(error) && error.response) {
            toast.error(error.response.data.message)
        } else {
            console.log(error)
        }
    }
}

</script>

<template>
    <Card class="w-full max-w-sm">
        <CardHeader class="">
            <CardTitle class="h-full row-span-2 grid items-center">Iniciar Sesion</CardTitle>
            <CardAction>
                <Button variant="link" class="hover:cursor-pointer">
                    Registro
                </Button>
            </CardAction>
        </CardHeader>
        <CardContent>
            <form id="loginform" @submit.prevent="handleSubmit">
                <div class="grid w-full items-center gap-4">
                    <div class="flex flex-col space-y-1.5">
                        <Label for="email">Nombre de usuario</Label>
                        <Input id="email" type="text" placeholder="username" required v-model.trim="username" />
                    </div>
                    <div class="flex flex-col space-y-1.5">
                        <div class="flex items-center">
                            <Label for="password">Contraseña</Label>
                            <a href="#" class="ml-auto inline-block text-sm underline">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                        <Input id="password" type="password" required v-model="password" />
                    </div>
                </div>
            </form>
        </CardContent>
        <CardFooter class="flex flex-col gap-2">
            <Button class="w-full" form="loginform">
                Login
            </Button>
        </CardFooter>
    </Card>
</template>
