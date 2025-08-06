{{-- Navigasi untuk Layar Kecil (Mobile) --}}
<nav class="navbar navbar-expand-lg navbar-dark" id="NavSm">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft" aria-controls="offcanvasLeft">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="https://yuksports.com">
            <img src="{{ $logo ?? '/img/default_logo.png' }}" alt="Logo" />
        </a>
        <button class="btn search-icon" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
            <img src="{{ asset('img/search.png') }}" alt="Search" />
        </button>
        <div class="offcanvas offcanvas-start bg-dark" tabindex="-1" id="offcanvasLeft" aria-labelledby="offcanvasLeftLabel" style="width: 225px">
            <div class="offcanvas-body">
                <div class="d-flex justify-content-between">
                    <a class="navbar-brand" href="https://yuksports.com">
                        <img src="{{ $logo ?? '/img/default_logo.png' }}" alt="Logo" />
                    </a>
                    <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="d-lg-none">
                    <div class="d-flex flex-row gap-2 my-3">
                        @auth
                            <a href="/profile" class="btn btn-sm btn-signin">Profile</a>
                            <a href="{{ route('logout') }}" class="btn btn-sm btn-signup"
                               onclick="event.preventDefault(); document.getElementById('global-logout-form').submit();">
                               Logout
                            </a>
                            {{-- FORM LOGOUT INDIVIDUAL DIHAPUS DARI SINI --}}
                        @else
                            <button class="btn btn-sm btn-signin" data-bs-toggle="modal" data-bs-target="#loginModal" style="width: 91px">
                                Sign In
                            </button>
                        @endauth
                    </div>
                </div>

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="https://score.yuksports.com">
                            <i class="fas fa-futbol"></i>
                            <span class="d-lg-inline"> Scores </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-star"></i>
                            <span class="d-lg-inline"> Favorite </span>
                        </a>
                    </li>
                    {{-- Tambahkan item menu lain jika ada --}}
                </ul>

                {{-- Jika #NavSm hanya untuk mobile, bagian ini mungkin tidak diperlukan di sini atau kelasnya perlu disesuaikan --}}
                <div class="d-none d-lg-flex align-items-center mt-auto">

                    {{-- Opsi Bahasa --}}
                    <span class="text-light me-3">ENG</span>

                    {{-- Tampilan untuk pengguna yang sudah login --}}
                    @auth
                        <a href="/profile" class="btn btn-sm btn-signin me-2">Profile</a>
                        
                        <a href="{{ route('logout') }}"
                        class="btn btn-sm btn-signup"
                        onclick="event.preventDefault(); document.getElementById('global-logout-form').submit();">
                            Logout
                        </a>
                    @endauth

                    {{-- Tampilan untuk pengunjung (guest) --}}
                    @guest
                        <button class="btn btn-sm btn-signin" onclick="showModal()">
                            Sign In
                        </button>
                    @endguest

                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-end bg-dark" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" style="width: 100%">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasRightLabel">
                    Search
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" id="new_search">
                {{-- Konten hasil pencarian akan dimuat di sini --}}
            </div>
        </div>
    </div>
</nav>

{{-- Navigasi untuk Layar Besar (Desktop) --}}
<nav class="navbar navbar-expand-lg navbar-dark" id="nav-large">
    <div class="container">
        <a class="navbar-brand" href="https://yuksports.com">
            <img src="{{ $logo ?? '/img/default_logo.png' }}" alt="Logo" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="https://score.yuksports.com">
                        <i class="fas fa-futbol"></i> Scores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-star"></i> Favorite
                    </a>
                </li>
                {{-- Tambahkan item menu lain jika ada --}}
            </ul>
            <div class="d-none d-lg-flex align-items-center mt-auto">

                {{-- Opsi Bahasa --}}
                <span class="text-light me-3">ENG</span>

                {{-- Tampilan untuk pengguna yang sudah login --}}
                @auth
                    <a href="/profile" class="btn btn-sm btn-signin me-2">Profile</a>
                    
                    <a href="{{ route('logout') }}"
                    class="btn btn-sm btn-signup"
                    onclick="event.preventDefault(); document.getElementById('global-logout-form').submit();">
                        Logout
                    </a>
                @endauth

                {{-- Tampilan untuk pengunjung (guest) --}}
                @guest
                    <button class="btn btn-sm btn-signin me-2" onclick="showModal()">
                        Sign In
                    </button>

                    <button class="btn btn-sm btn-signup" onclick="showSignupRedirectAlert()">
                        Sign Up
                    </button>
                @endguest

            </div>
        </div>
    </div>
</nav>