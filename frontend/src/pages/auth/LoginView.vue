<script setup lang="ts">
import ContainerCenter from '@/layouts/ContainerCenter.vue';
import FormSign from '@/modules/auth/components/organisms/FormSign.vue';
</script>
<template>
    <ContainerCenter>
        <div class="grid justify-center">
            <div class="max-w-[400px]">
                <FormSign class="w-[400px]" />
            </div>
        </div>
    </ContainerCenter>
</template>
