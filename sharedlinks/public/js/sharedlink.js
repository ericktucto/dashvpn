const { createApp } = Vue

createApp({
    methods: {
        download() {
            const file = new Blob([window.sharedlink.contents], { type: "text/plain;charset=utf-8" })
            const fileURL = URL.createObjectURL(file)
            const a = document.createElement("a")
            a.href = fileURL
            a.download = `${window.sharedlink.slug}.conf`
            a.click()
        },
    },
    mounted() {
        document.getElementById('formotp').classList.remove('hidden')
        new QRious({
            element: document.getElementById('qrcode'),
            value: window.sharedlink.contents,
            size: 300,
            padding: 20,
        })
    }
}).mount('#app')

