<script setup lang="ts">
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from "@/components/ui/input-group";
import { PlusIcon, TrashIcon } from "lucide-vue-next";
import { Separator } from "@/components/ui/separator";
import { ref } from "vue";

const props = defineProps<{
    instructions: string[]
}>()
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
            <InputGroupInput placeholder="Escribe la instrucción" :modelValue="instruction"
                @update:modelValue="handleInput($event, index)" @keydown.enter.prevent />
            <InputGroupAddon align="inline-end">
                <InputGroupButton aria-label="Trash" title="Eliminar" size="icon-xs" @click="handleTrash(index)">
                    <TrashIcon />
                </InputGroupButton>
            </InputGroupAddon>
        </InputGroup>
        <Separator class="my-4" />
        <InputGroup>
            <InputGroupInput placeholder="Escribe la instrucción" v-model="newInstruction"
                @keydown.enter.prevent="handleAdd" />
            <InputGroupAddon align="inline-end">
                <InputGroupButton aria-label="Add" title="Agregar" size="icon-xs" @click="handleAdd">
                    <PlusIcon />
                </InputGroupButton>
            </InputGroupAddon>
        </InputGroup>
    </div>
</template>
