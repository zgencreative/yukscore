<template>
    <h3>💬 Live Chat</h3>

    <div class="chat-messages" ref="messagesContainer">
        <div v-for="(msg, index) in messages" :key="index" class="chat-message">
            <span class="username">{{ msg.sender_name }}</span>
            <span class="text">{{ msg.message }}</span>
        </div>
    </div>

    <form @submit.prevent="sendMessage" class="chat-form">
        <input
            type="text"
            v-model="newMessage"
            placeholder="Ketik pesan..."
            autocomplete="off"
            required
        />
        <button type="submit">Kirim</button>
    </form>
</template>

<script setup>
import { ref, onMounted, nextTick } from "vue";
import axios from "axios";

// 1. Menerima 'matchId' sebagai properti dari elemen HTML
const props = defineProps({
    matchId: {
        type: String, // Tipe data adalah String
        required: true, // Properti ini wajib ada
    },
});

// 2. State management menggunakan ref
const messages = ref([]);
const newMessage = ref("");
const senderName = ref("");
const messagesContainer = ref(null); // Untuk referensi ke elemen DOM

// --- FUNGSI-FUNGSI ---

// Fungsi untuk auto-scroll ke pesan terbaru
const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop =
                messagesContainer.value.scrollHeight;
        }
    });
};

// Fungsi untuk mendapatkan atau membuat nama Guest acak
const getOrSetGuestName = () => {
    let name = localStorage.getItem("guest_name");
    if (!name) {
        name = "Guest" + Math.floor(Math.random() * 9000 + 1000);
        localStorage.setItem("guest_name", name);
    }
    senderName.value = name;
};

// Fungsi untuk mengirim pesan
const sendMessage = async () => {
    // Jangan kirim jika pesan kosong
    if (newMessage.value.trim() === "" || !senderName.value) return;

    const messageData = {
        sender_name: senderName.value,
        message: newMessage.value,
    };

    // Optimistic Update: Langsung tampilkan pesan di UI pengirim untuk UX yang lebih baik
    messages.value.push(messageData);
    scrollToBottom();

    try {
        // Kirim data ke backend menggunakan URL dinamis sesuai matchId
        await axios.post(`/chat/${props.matchId}`, messageData);
    } catch (error) {
        console.error("Gagal mengirim pesan:", error);
        // Opsional: Tambahkan logika jika pesan gagal terkirim, misal mengubah warna pesan
    } finally {
        // Kosongkan input setelah pesan dikirim
        newMessage.value = "";
    }
};

// --- LIFECYCLE HOOK ---

// Kode yang akan dijalankan saat komponen pertama kali dirender
onMounted(() => {
    getOrSetGuestName();

    // Dengarkan channel yang dinamis sesuai dengan matchId yang diterima
    window.Echo.channel(`stream-chat.${props.matchId}`).listen(
        ".new-message",
        (e) => {
            // Tambahkan pesan baru yang diterima dari broadcast ke dalam array
            messages.value.push(e.message);
            scrollToBottom();
        }
    );
});
</script>
