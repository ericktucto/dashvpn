<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
    Sheet,
    SheetContent,
    SheetTrigger,
} from '@/components/ui/sheet'
import { MenuIcon } from 'lucide-vue-next';
</script>
<template>
    <div>
        <Sheet>
            <SheetTrigger as-child>
                <Button variant="outline">
                    <MenuIcon />
                </Button>
            </SheetTrigger>
            <SheetContent class="w-[200px]" side="left">
                <slot></slot>
            </SheetContent>
        </Sheet>
    </div>
</template>
