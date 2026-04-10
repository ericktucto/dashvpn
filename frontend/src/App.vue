<script setup lang="ts">
import 'vue-sonner/style.css'
import { LogOutIcon } from 'lucide-vue-next';
import { Toaster } from 'vue-sonner'
import { Button } from '@/components/ui/button';
import { useRouter } from 'vue-router';
import { isAuthenticated } from './router/middlewares/auth';
import { ref } from 'vue';

const router = useRouter();

const isAuth = ref(false)

router.afterEach(async () => {
    try {
        isAuth.value = await isAuthenticated()
    } catch (error) {
        console.error(error)
    }
})

function handleLogout() {
    localStorage.removeItem('token')
    router.push({ name: 'auth@login' })
}
</script>
<template>
    <nav class="grid justify-end p-4">
        <Button v-show="isAuth" @click="handleLogout">
            <LogOutIcon />
            Logout
        </Button>
    </nav>
    <RouterView />
    <Toaster position="top-center" />
</template>
