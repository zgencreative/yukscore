<nav class="navbar navbar-expand-lg navbar-dark" id="NavSm">
  <div class="container">
    <!-- Navbar Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft" aria-controls="offcanvasLeft">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Logo -->
    <a class="navbar-brand" href="https://score.yuksports.com/">
      <img src="{{ $logo }}" alt="Logo" />
    </a>
    <!-- search -->
    <button class="btn search-icon" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
      <img src="{{ asset('img/search.png') }}" alt="Search" />
    </button>

    <!-- Offcanvas Left -->
    <div class="offcanvas offcanvas-start bg-dark" tabindex="-1" id="offcanvasLeft" aria-labelledby="offcanvasLeftLabel" style="width: 225px">
      <div class="offcanvas-body">
        <div class="d-flex justify-content-between">
          <a class="navbar-brand" href="https://score.yuksports.com/">
            <img src="{{ $logo }}" alt="Logo" />
          </a>
          <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="d-lg-none">
          <div class="d-flex flex-row gap-2 my-3">
            @auth
              <a href="/profile" class="btn btn-sm btn-signin">Profile</a>
              <a href="/logout" class="btn btn-sm btn-signup">Logout</a>
            @else
              <button class="btn btn-sm btn-signin" data-bs-toggle="modal" data-bs-target="#loginModal" style="width: 91px">
                Sign In
              </button>
            @endauth
          </div>
        </div>

        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="/"><i class="fas fa-futbol"></i> <span class="d-lg-inline"> Scores </span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fas fa-star"></i> <span class="d-lg-inline"> Favorite </span></a>
          </li>
        </ul>

        <!-- Right Section Desktop -->
        <div class="d-lg-flex align-items-center d-none">
          <span class="text-light me-3">ENG</span>
          @auth
            <a href="/profile" class="btn btn-sm btn-signin">Profile</a>
            <a href="/logout" class="btn btn-sm btn-signup">Logout</a>
          @else
            <button class="btn btn-sm btn-signin me-2" onclick="showModal()">Sign In</button>
          @endauth
        </div>
      </div>
    </div>

    <!-- Offcanvas Right (Search) -->
    <div class="offcanvas offcanvas-end bg-dark" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" style="width: 100%">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">Search</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body" id="new_search"></div>
    </div>
  </div>
</nav>

<nav class="navbar navbar-expand-lg navbar-dark" id="nav-large">
  <div class="container">
    <a class="navbar-brand" href="https://score.yuksports.com/">
      <img src="{{ $logo }}" alt="Logo" />
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/"><i class="fas fa-futbol"></i> Scores</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-star"></i> Favorite</a></li>
      </ul>
      <div class="d-flex align-items-center">
        <span class="text-light me-3">ENG</span>
        @auth
          <a href="/profile" class="btn btn-sm btn-signin">Profile</a>
          <a href="/logout" class="btn btn-sm btn-signup">Logout</a>
        @else
          <button class="btn btn-sm btn-signin me-2" onclick="showModal()">Sign In</button>
        @endauth
      </div>
    </div>
  </div>
</nav>
