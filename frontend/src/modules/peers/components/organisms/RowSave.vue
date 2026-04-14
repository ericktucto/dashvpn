<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { TableCell, TableRow } from '@/components/ui/table';
import { BanIcon, SaveIcon } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

const name = ref('')
const nextIp = ref('')

defineEmits<{
    cancel: []
    save: [name: string]
}>()

onMounted(() => {
    // @todo obtener el next ip disponible
    nextIp.value = '10.8.0.12'
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
            activo
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
