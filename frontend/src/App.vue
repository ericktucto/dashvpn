<script setup lang="ts">
import 'vue-sonner/style.css'
import { LogOutIcon } from 'lucide-vue-next';
import { Toaster } from 'vue-sonner'
import { Button } from '@/components/ui/button';
import { useRoute, useRouter } from 'vue-router';
import { isAuthenticated } from './router/middlewares/auth';
import { onMounted, ref, computed } from 'vue';
import AsideContent from './components/dashvpn/AsideContent.vue';
import AsideSheet from './components/dashvpn/AsideSheet.vue';

const router = useRouter();
const route = useRoute();

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

const current = computed(() => {
    const name = (route.name as string) ?? ''
    if (name.startsWith('server')) {
        return 'server'
    }
    if (name.startsWith('peers')) {
        return 'peers'
    }
    return null
})
onMounted(() => {
    isAuthenticated()
        .then((result) => result)
        .catch(() => false)
        .then((result) => isAuth.value = result)
})

</script>
<template>
    <div class="grid w-screen h-screen" :class="[isAuth ? 'grid-cols-[1fr] md:grid-cols-[200px_1fr]' : '']">
        <aside class="hidden md:block w-[200px] border-r">
            <AsideContent :active="current" @go="(path) => router.push(path)" />
        </aside>
        <div class="grid grid-rows-[auto_1fr]">
            <nav class="flex justify-between md:justify-end p-4 border-b">
                <AsideSheet class="md:hidden block">
                    <AsideContent :active="current" @go="(path) => router.push(path)" />
                </AsideSheet>
                <Button v-show="isAuth" @click="handleLogout">
                    <LogOutIcon />
                    Logout
                </Button>
            </nav>
            <RouterView />
        </div>
    </div>
    <Toaster position="top-center" />
</template>
