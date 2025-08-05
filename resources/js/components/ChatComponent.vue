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
    matchId: { type: String, required: true },
    initialMessages: { type: Array, default: () => [] },
    user: { type: Object, default: null }, // <-- Terima prop user (bisa null)
});

// 2. State management menggunakan ref
const messages = ref(
    Array.isArray(props.initialMessages) ? [...props.initialMessages] : []
);
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
    // Fungsi ini sekarang hanya untuk GUEST
    let name = localStorage.getItem("guest_name");
    if (!name) {
        name = "Guest" + Math.floor(Math.random() * 9000 + 1000);
        localStorage.setItem("guest_name", name);
    }
    senderName.value = name;
};

// Fungsi untuk mengirim pesan
const sendMessage = async () => {
    if (newMessage.value.trim() === "" || !senderName.value) return;

    // --- PERUBAHAN DIMULAI DI SINI ---

    // 1. Tentukan nama yang benar untuk ditampilkan di UI secara langsung
    const optimisticName = props.user ? props.user.name : senderName.value;

    // 2. Buat objek pesan untuk ditampilkan di UI (Optimistic Update)
    // Gunakan optimisticName yang baru saja kita tentukan
    const optimisticMessage = {
        id: Date.now(), // ID sementara untuk rendering
        sender_name: optimisticName,
        message: newMessage.value,
    };
    messages.value.push(optimisticMessage);
    scrollToBottom();

    // 3. Buat data yang akan dikirim ke backend
    // Backend tetap akan memvalidasi dan menggunakan nama dari sesi login
    const dataToSend = {
        sender_name: senderName.value,
        message: newMessage.value,
    };

    // Kosongkan input setelah data disiapkan
    newMessage.value = "";

    try {
        // Kirim data ke backend
        await axios.post(`/chat/${props.matchId}`, dataToSend);
    } catch (error) {
        console.error("Gagal mengirim pesan:", error);
        // Opsional: Hapus pesan dari UI jika pengiriman gagal
        // messages.value.pop();
    }

    // --- AKHIR PERUBAHAN ---
};

// --- LIFECYCLE HOOK ---

// Kode yang akan dijalankan saat komponen pertama kali dirender
onMounted(() => {
    // Tentukan nama pengirim berdasarkan status login
    if (props.user) {
        // Jika user login, gunakan namanya
        senderName.value = props.user.name;
    } else {
        // Jika guest, jalankan logika nama acak
        getOrSetGuestName();
    }

    // Dengarkan channel broadcast
    window.Echo.channel(`stream-chat.${props.matchId}`).listen(
        ".new-message",
        (e) => {
            // Cek agar pesan dari diri sendiri tidak duplikat (opsional tapi bagus)
            if (e.message.sender_name !== senderName.value) {
                messages.value.push(e.message);
                scrollToBottom();
            }
        }
    );

    scrollToBottom();
});
</script>
