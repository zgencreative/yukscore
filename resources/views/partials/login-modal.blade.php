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

    loading.style.display = "block";
    overlay.style.display = "block";

    try {
      const response = await fetch("/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      const result = await response.json();
      loading.style.display = "none";
      overlay.style.display = "none";

      if (result.success) {
        Swal.fire({ title: "Login Berhasil!", text: result.message || "Selamat datang kembali!", icon: "success" }).then(() => {
          closeModal();
          window.location.reload();
        });
      } else {
        Swal.fire({ title: "Login Gagal!", text: result.message || "Email atau password salah!", icon: "error" });
      }
    } catch (error) {
      loading.style.display = "none";
      overlay.style.display = "none";
      Swal.fire({ title: "Terjadi Kesalahan!", text: "Tidak dapat terhubung ke server.", icon: "error" });
    }
  }
</script>
