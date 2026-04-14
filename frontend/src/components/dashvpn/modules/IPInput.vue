<script setup lang="ts">
import type Ip from '../types/ip';
import { Input } from '@/components/ui/input';

const model = defineModel<Ip>({
    required: true,
})

withDefaults(defineProps<{
    disabledFourth?: boolean
}>(), {
    disabledFourth: false
})

function handleInput(value: number | string, type: keyof Ip) {
    model.value[type] = parseInt((value as string)) ?? 0
}
</script>
<template>
    <div class="flex gap-1">
        <Input type="number" :modelValue="modelValue.first" @update:modelValue="handleInput($event, 'first')" />
        .
        <Input type="number" :modelValue="modelValue.second" @update:modelValue="handleInput($event, 'second')" />
        .
        <Input type="number" :modelValue="modelValue.third" @update:modelValue="handleInput($event, 'third')" />
        .
        <Input type="number" :modelValue="modelValue.fourth" @update:modelValue="handleInput($event, 'fourth')"
            :disabled="disabledFourth" />
    </div>
</template>
<style scoped>
input {
    min-width: 1%;
}
</style>
