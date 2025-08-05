<!-- Login Modal -->
<div class="modall" id="loginModal">
  <div class="modall-dialog">
    <div class="modall-content">
      <div class="modall-header d-flex justify-content-between align-items-center">
        <h5 class="modall-title">Login</h5>
        <button type="button" class="btn-close" onclick="closeModal()"></button>
      </div>
      <div class="modall-body">
        <form onsubmit="handleLogin(event)">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" placeholder="Masukkan email" required />
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Masukkan password" required />
          </div>
          <button type="submit" class="btn btn-dark w-100">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Overlay -->
<div class="overlay" id="overlay"></div>

<!-- Loading Spinner -->
<div class="loading-container" id="loading-container" style="display: none;">
  <div class="spinner-border text-primary" role="status">
    <span class="sr-only">Loading...</span>
  </div>
</div>

<script>
  function showModal() {
    document.getElementById("loginModal").classList.add("show");
  }

  function closeModal() {
    document.getElementById("loginModal").classList.remove("show");
  }

  async function handleLogin(event) {
    event.preventDefault();
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const loading = document.getElementById("loading-container");
    const overlay = document.getElementById("overlay");

    // Ambil token CSRF (asumsikan sudah ada di meta tag)
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    loading.style.display = "block";
    overlay.style.display = "block";

    try {
        const response = await fetch("{{ route('login') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json", // Penting untuk $request->expectsJson()
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({ email, password }),
        });

        // Sembunyikan loading setelah respons diterima, sebelum parsing JSON
        loading.style.display = "none";
        overlay.style.display = "none";

        if (response.ok) { // Status 200-299 (login berhasil)
            const result = await response.json(); // Sekarang seharusnya JSON
            if (result.success) {
                Swal.fire({
                    title: "Login Berhasil!",
                    text: result.message || "Selamat datang kembali!",
                    icon: "success"
                }).then(() => {
                    window.location.reload(); // Fallback jika tidak ada redirect_url
                });
            } else {
                // Ini terjadi jika server mengembalikan { success: false } dengan status 200 OK
                // Jarang terjadi untuk login, biasanya error validasi akan 422.
                Swal.fire({
                    title: "Login Gagal!",
                    text: result.message || "Terjadi kesalahan yang tidak diketahui.",
                    icon: "error"
                });
            }
        } else { // Tangani error HTTP seperti 422 (Validation Error) atau 500
            let errorMessage = "Email atau password salah atau terjadi kesalahan server.";
            if (response.status === 422) {
                const errorData = await response.json(); // Error validasi dari Laravel
                if (errorData.errors) {
                    // Gabungkan semua pesan error validasi menjadi satu string
                    errorMessage = Object.values(errorData.errors).flat().join("\n");
                } else if (errorData.message) {
                    errorMessage = errorData.message;
                }
            } else if (response.statusText) {
                errorMessage = `Error: ${response.status} - ${response.statusText}`;
            }
            Swal.fire({
                title: "Login Gagal!",
                text: errorMessage,
                icon: "error"
            });
        }
    } catch (error) { // Ini menangkap error network atau error parsing JSON
        loading.style.display = "none";
        overlay.style.display = "none";
        console.error("Error during login fetch:", error);
        Swal.fire({
            title: "Terjadi Kesalahan!",
            text: "Tidak dapat terhubung ke server atau respons tidak valid.",
            icon: "error"
        });
    }
  }
</script>
