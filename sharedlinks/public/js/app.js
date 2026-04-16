const { createApp } = Vue

createApp({
    data() {
        return {
            code: '',
        }
    },
    computed: {
        focusClassOTP() {
            return this.code.split('').map(() => "x")
        },
        first() {
            return this.code[0] ?? ''
        },
        second() {
            return this.code[1] ?? ''
        },
        third() {
            return this.code[2] ?? ''
        },
        fourth() {
            return this.code[3] ?? ''
        },
        fifth() {
            return this.code[4] ?? ''
        },
        sixth() {
            return this.code[5] ?? ''
        }
    },
    methods: {
        onlyNumbers(e) {
            if (e.ctrlKey || e.metaKey) return
            if (
                !/[0-9]/.test(e.key) &&
                !['Backspace', 'Tab', 'Enter'].includes(e.key)
            ) {
                e.preventDefault()
            }
        },
        handleInput(e) {
            const value = e.target.value
                .replace(/\D/g, '') // elimina todo lo que no sea número
                .slice(0, 6)

            this.code = value
        }
    },
    mounted() {
        document.getElementById('formotp').classList.remove('hidden')
    }
}).mount('#app')

