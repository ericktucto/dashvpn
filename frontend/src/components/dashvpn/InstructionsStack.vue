<script setup lang="ts">
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from "@/components/ui/input-group";
import { PlusIcon, ShieldCheck, TrashIcon } from "lucide-vue-next";
import { Separator } from "@/components/ui/separator";
import { ref } from "vue";

const props = withDefaults(defineProps<{
    instructions: string[]
    placeholder?: string
    readOnly?: boolean
}>(), {
    placeholder: "Escribe la instrucción",
    readOnly: false
})
const emit = defineEmits<{
    (e: "update:instructions", value: string[]): void
}>()

const newInstruction = ref("")

function handleInput(newValue: string, index: number) {
    emit("update:instructions", [...props.instructions.slice(0, index), newValue, ...props.instructions.slice(index + 1)])
}

function handleTrash(index: number) {
    emit("update:instructions", [...props.instructions.slice(0, index), ...props.instructions.slice(index + 1)])
}
function handleAdd() {
    emit("update:instructions", [...props.instructions, newInstruction.value])
    newInstruction.value = ""
}
</script>
<template>
    <div>
        <InputGroup v-for="(instruction, index) in instructions" :key="`${index}-item`" class="mb-2">
            <InputGroupInput :placeholder="placeholder" :modelValue="instruction" :readOnly="readOnly"
                @update:modelValue="handleInput($event, index)" @keydown.enter.prevent />
            <InputGroupAddon align="inline-end">
                <ShieldCheck v-if="readOnly" />
                <InputGroupButton v-else aria-label="Trash" title="Eliminar" size="icon-xs" @click="handleTrash(index)">
                    <TrashIcon />
                </InputGroupButton>
            </InputGroupAddon>
        </InputGroup>
        <div v-show="instructions.length === 0" class="text-center">No hay items</div>
        <template v-if="!readOnly">
            <Separator class="my-4" />
            <InputGroup>
                <InputGroupInput :placeholder="placeholder" v-model="newInstruction"
                    @keydown.enter.prevent="handleAdd" />
                <InputGroupAddon align="inline-end">
                    <InputGroupButton aria-label="Add" title="Agregar" size="icon-xs" @click="handleAdd">
                        <PlusIcon />
                    </InputGroupButton>
                </InputGroupAddon>
            </InputGroup>
        </template>
    </div>
</template>
