<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { Html5QrcodeScanner } from 'html5-qrcode'

const emit = defineEmits(['result', 'close'])
const scanner = ref(null)

onMounted(() => {
  scanner.value = new Html5QrcodeScanner('reader', {
    fps: 10,
    qrbox: { width: 250, height: 150 }
  })

  scanner.value.render((decodedText) => {
    emit('result', decodedText)
    scanner.value.clear()
  })
})

onUnmounted(() => {
  if (scanner.value) scanner.value.clear()
})
</script>

<template>
  <div
    style="background: white; padding: 20px; border-radius: 15px; border: 1px solid #ccc;"
  >
    <div id="reader"></div>
    <button
      @click="$emit('close')"
      style="width: 100%; margin-top: 10px; padding: 10px; background: #eee; border-radius: 8px;"
    >
      Fermer
    </button>
  </div>
</template>
