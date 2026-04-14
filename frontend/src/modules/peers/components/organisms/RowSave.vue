<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { TableCell, TableRow } from '@/components/ui/table';
import { BanIcon, SaveIcon } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { getNextAddress } from '../../services/fetch';

const name = ref('')
const nextIp = ref('')

defineEmits<{
    cancel: []
    save: [name: string]
}>()

onMounted(() => {
    getNextAddress().then((res) => {
        nextIp.value = res.data.data.address
    })
})
</script>
<template>
    <TableRow>
        <TableCell>
            <Input v-model.trim="name" />
        </TableCell>
        <TableCell>
            {{ nextIp }}
        </TableCell>
        <TableCell>
            <Switch :modelValue="true" />
        </TableCell>
        <TableCell class="text-right pr-8">
            <div class="flex justify-end gap-6">
                <Button variant="outline" @click="$emit('cancel')">
                    <BanIcon />
                    Cancelar
                </Button>
                <Button :disabled="!name" @click="$emit('save', name)">
                    <SaveIcon />
                    Guardar
                </Button>
            </div>
        </TableCell>
    </TableRow>
</template>
