@extends('layouts.templates')

@section('content')
<div class="container my-5">
  <div class="row">
    <!-- Sidebar Kiri -->
    <div class="col-md-3" id="main-search">
      <div
        class="text-light p-3 rounded"
        style="overflow-y: auto"
        id="leftSide"
      ></div>
    </div>

    <!-- Main Content Tengah -->
    <div class="col-md-9" id="main-div">
      <div class="p-md-3 rounded shadow-sm text-light">
        <p>
          <a href="/" style="text-decoration: none">
            <i class="fas fa-arrow-left" style="color: white"></i>
          </a>
        </p>
        <div class="d-flex align-items-center custom-padding">
          <!-- Logo -->
          <div class="me-3">
            <img
              src="{{ $data['data']['IMGTeam'] }}"
              alt="{{ $data['data']['NMTeam'] }}"
              style="width: 30px; height: 30px"
              id="ImgTeam"
            />
          </div>
          <!-- Text Content -->
          <div>
            <h5 class="m-0" id="team-name">{{ $data['data']['NMTeam'] }}</h5>
            <p class="m-0">{{ $data['data']['CoNm'] }}</p>
          </div>
        </div>
        <div class="menu-bar">
          <div class="menu-items">
            <p class="menu-link active" id="matches-link">MATCHES</p>
            <p class="menu-link" id="table-link">TABLES</p>
            <p class="menu-link" id="squad-link">SQUAD</p>
            <p class="menu-link" id="player-link">PLAYER STAT</p>
            <p class="menu-link" id="team-link">TEAM STAT</p>
            <p class="menu-link" id="team-news">NEWS</p>
          </div>
        </div>
        <div class="py-3" id="dinamic-content"></div>
      </div>
    </div>
  </div>
@endsection

@section('extra_js')
  <script>
        document.addEventListener("DOMContentLoaded", function () {
          const loading = document.getElementById("loading-container");
          const overlay = document.getElementById("overlay");
          const dataTeam = @json($data);
          matche(dataTeam);
          const currentPath = window.location.pathname; // Contoh: "/match/1251295"
          const segments = currentPath.split("/"); // ["", "match", "1251295"]
          const idTable = `${segments[2]}.${segments[3]}`;
          const id = segments[segments.length - 2]; // Ambil elemen terakhir sebagai ID ("1251295")
          const links = [
            {
              id: "matches-link",
              endpoint: (id) => `/api/football/detailTeam/${id}`,
            },
            {
              id: "table-link",
              endpoint: (id) => `/api/football/detailTeam/table/${id}`,
            },
            {
              id: "squad-link",
              endpoint: (id) => `/api/football/detailTeam/squad/${id}`,
            },
            {
              id: "player-link",
              endpoint: (id) => `/api/football/detailTeam/playerstat/${id}`,
            },
            {
              id: "team-link",
              endpoint: (id) => `/api/football/detailTeam/stat/${id}`,
            },
            {
              id: "team-news",
              endpoint: (id) => `/api/football/detailTeam/news/${id}`,
            }

          ];



          // Iterasi setiap link dan tambahkan event listener
          links.forEach((link) => {
            const element = document.getElementById(link.id);

            // Pastikan elemen dengan ID tersebut ada
            if (!element) {
              console.warn(`Element with ID "${link.id}" not found.`);
              return;
            }

            // Tambahkan event listener ke elemen
            element.addEventListener("click", function (event) {
              event.preventDefault(); // Cegah tindakan default (misalnya, navigasi <a>)
              loading.style.display = "block";
              overlay.style.display = "block";

              // Hapus class active dari semua elemen lainnya
              element.classList.remove("active");
              links.forEach((otherLink) => {
                const otherElement = document.getElementById(otherLink.id);
                if (otherElement && otherElement.classList.contains("active")) {
                  otherElement.classList.remove("active");
                }
              });

              // Tambahkan class active ke elemen yang diklik
              element.classList.add("active");

              // Kirim fetch request ke endpoint
              fetch(link.endpoint(id), {
                method: "GET",
                headers: { "Content-Type": "application/json" },
              })
                .then((response) => {
                  if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                  }
                  return response.json();
                })
                .then((data) => {
                  const container = document.getElementById("dinamic-content");
                  container.innerHTML = "";
                  loading.style.display = "none";
                  overlay.style.display = "none";
                  if (link.id == "matches-link") {
                    matche(data);
                  } else if (link.id == "table-link"){
                    rank(data);
                  } else if (link.id == "squad-link"){
                    squad(data);
                  } else if (link.id == "player-link"){
                    playerstat(data);
                  } else if (link.id == "team-link"){
                    teamstat(data);
                  } else if (link.id == "team-news"){
                    teamnews(data);
                  }
                  else {
                    console.error("Container element not found!");
                  }
                })
                .catch((error) => {
                  console.error(`Error fetching ${link.endpoint(id)}:`, error);
                });
            });
          });
        })

        // Event listener untuk menangani perubahan filter kompetisi
        document
        .getElementById("competitionFilter")
        .addEventListener("change", function (event) {
        selectedComp = event.target.value; // Mengubah kompetisi yang dipilih
        updateMatches(); // Memperbarui data berdasarkan kompetisi yang dipilih
        });

        function teamstat(data, additionalData = null){
          const container = document.getElementById("dinamic-content");
          container.innerHTML = "";  // Reset kontainer sebelum menambah konten baru
          const TeamStat = data.data.TeamStat[0];

          // Menambahkan data ke dalam dropdown
          const dropdownContent = document.createElement("div");
          dropdownContent.classList.add("dropdown");

          // Fungsi untuk menambahkan item ke dalam dropdown
          function populateDropdown(events) {
            // Mengosongkan dropdown sebelum menambahkan data baru
            dropdownContent.innerHTML = '';

            // Menambahkan item 'All' yang menampilkan semua event
            const allOption = document.createElement('button');
            allOption.classList.add("dropdown-btn");
            allOption.id = "dropdownBtn";
            allOption.innerHTML = `${events[0]['Snm']} <span>&#9662;</span>`;
            if (additionalData !== null) {
              allOption.innerHTML = `${additionalData} <span>&#9662;</span>`;
            }
            dropdownContent.appendChild(allOption);

            const divDropdown = document.createElement("div");
            divDropdown.classList.add('dropdown-content');
            divDropdown.id = 'dropdownContent';

            const currentPath = window.location.pathname; // Contoh: "/match/1251295"
            const segments = currentPath.split("/"); // ["", "match", "1251295"]
            const idTable = `${segments[2]}.${segments[3]}`;
            const idTeam = segments[segments.length - 2]; // Ambil elemen terakhir sebagai ID ("1251295")

            // Menambahkan setiap event ke dropdown
            events.forEach(event => {
              const eventLink = document.createElement('a');
              eventLink.href = "#";  // Bisa juga menambahkan href jika diperlukan
              eventLink.innerHTML = event.Snm;

              // Tambahkan event listener pada setiap link <a>
              eventLink.addEventListener('click', (e) => {
                e.preventDefault();  // Mencegah link untuk navigasi

                // Lakukan request ke URL yang sesuai (misalnya URL menggunakan event.Snm atau ID)
                const url = `/api/football/detailTeam/stat/${idTeam}/${event.Sid}`;  // Contoh URL
                fetch(url)
                  .then(response => response.json())
                  .then(data => {
                    teamstat(data, event.Snm);
                    // Panggil fungsi lain untuk memproses data yang diterima
                  })
                  .catch(error => {
                    console.error("Error fetching data:", error);
                  });
              });

              // Menambahkan link ke dalam dropdown
              divDropdown.appendChild(eventLink);
            });
            dropdownContent.appendChild(divDropdown);
            container.appendChild(dropdownContent);
          }

          // Memanggil fungsi untuk mengisi dropdown
          populateDropdown(data.data.Events);

          TeamStat.forEach(section => {
            // Membuat elemen untuk setiap section (ATTACKING, DEFENDING, DISCIPLINE)
            const sectionDiv = document.createElement('div');
            const titleDiv = document.createElement('div');
            titleDiv.classList.add('title-detail');
            titleDiv.textContent = section.name;  // Nama kategori seperti ATTACKING
            sectionDiv.appendChild(titleDiv);

            // Membuat tabel untuk stats
            const table = document.createElement('table');
            table.classList.add('no-border-bottom');

            // Membuat header tabel
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            headerRow.innerHTML = `
              <th></th>
              <th class="teks-tengah">Per Game</th>
              <th class="teks-tengah">Total</th>
              <th class="border-column teks-tengah">Rank</th>
            `;
            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Membuat body tabel dan mengisi data dengan looping
            const tbody = document.createElement('tbody');
            section.stats.forEach(stat => {
              const row = document.createElement('tr');
              row.innerHTML = `
                <td>${stat.name}</td>
                <td class="teks-tengah">${stat.pgValue || '-'}</td>
                <td class="teks-tengah">${stat.totalValue}</td>
                <td class="border-column teks-tengah">${stat.rank}</td>
              `;
              tbody.appendChild(row);
            });
            table.appendChild(tbody);

            sectionDiv.appendChild(table);
            container.appendChild(sectionDiv);
          });
        }

        function playerstat(data){
          let activeFilter = 'all';
          let globalData = data;
          const container = document.getElementById("dinamic-content");
          container.innerHTML = "";  // Reset kontainer sebelum menambah konten baru
          const playerData = data.data.PlayerStat;
          // Tipe data yang valid
          const validTypes = [
          { Typ: 1, name: "GOALS" },
          { Typ: 2, name: "POINTS" },
          { Typ: 3, name: "ASSISTS" },
          { Typ: 4, name: "RED CARDS" },
          { Typ: 5, name: "YELLOW RED CARDS" },
          { Typ: 6, name: "YELLOW CARDS" },
          { Typ: 7, name: "OWN GOALS" },
          { Typ: 8, name: "SHOTS ON TARGET" },
          { Typ: 9, name: "SHOTS OFF TARGET" },
          { Typ: 10, name: "GOALS BY HEAD" },
          { Typ: 11, name: "GOALS BY PENALTY" },
          { Typ: 12, name: "MINUTES PLAYED" }
          ];

          function generateFilterButtons(data) {
            const buttonDiv = document.createElement("div");
            buttonDiv.innerHTML += `
              <button class="button-matchesDetailTeam active m-2" tabindex="0" id="all-button">
                All
              </button>
            `;
            validTypes.forEach(type => {
              // Cek jika ada data untuk typ yang sesuai
              const typeData = data.find(item => item.Typ === type.Typ);
              if (typeData) {
                buttonDiv.innerHTML += `
                  <button class="button-matchesDetailTeam m-2" tabindex="0">${type.name}</button>
                `;
              }
            });
            // Menambahkan event listener pada button filter
            buttonDiv.querySelectorAll('button').forEach(button => {
              button.addEventListener('click', () => {
                // Menandai button aktif
                document.querySelectorAll('.button-matchesDetailTeam').forEach(btn => {
                  btn.classList.remove('active');
                });
                button.classList.add('active');

                // Mengubah filter aktif
                activeFilter = button.id === 'all-button' ? 'all' : button.textContent.toUpperCase();
                tampil_stat(globalData.data.PlayerStat);
              });
            });

            // Tambahkan buttonDiv ke dalam halaman (misal ke div dengan id 'buttons-container')
            container.appendChild(buttonDiv);
          }

          // Panggil fungsi untuk generate tombol filter
          generateFilterButtons(playerData);

          // Menambahkan data ke dalam dropdown
          const dropdownContent = document.createElement("div");
          dropdownContent.classList.add("dropdown");

          // Fungsi untuk menambahkan item ke dalam dropdown
          function populateDropdown(events) {
            // Mengosongkan dropdown sebelum menambahkan data baru
            dropdownContent.innerHTML = '';

            // Menambahkan item 'All' yang menampilkan semua event
            const allOption = document.createElement('button');
            allOption.classList.add("dropdown-btn");
            allOption.id = "dropdownBtn";
            allOption.innerHTML = `${events[0]['Snm']} <span>&#9662;</span>`;
            dropdownContent.appendChild(allOption);

            const divDropdown = document.createElement("div");
            divDropdown.classList.add('dropdown-content');
            divDropdown.id = 'dropdownContent';

            const currentPath = window.location.pathname; // Contoh: "/match/1251295"
            const segments = currentPath.split("/"); // ["", "match", "1251295"]
            const idTable = `${segments[2]}.${segments[3]}`;
            const idTeam = segments[segments.length - 2]; // Ambil elemen terakhir sebagai ID ("1251295")

            // Menambahkan setiap event ke dropdown
            events.forEach(event => {
              const eventLink = document.createElement('a');
              eventLink.href = "#";  // Bisa juga menambahkan href jika diperlukan
              eventLink.innerHTML = event.Snm;

              // Tambahkan event listener pada setiap link <a>
              eventLink.addEventListener('click', (e) => {
                e.preventDefault();  // Mencegah link untuk navigasi

                // Lakukan request ke URL yang sesuai (misalnya URL menggunakan event.Snm atau ID)
                const url = `/api/football/detailTeam/playerstat/${idTeam}/${event.Sid}`;  // Contoh URL
                fetch(url)
                  .then(response => response.json())
                  .then(data => {
                    allOption.innerHTML = `${event.Snm} <span>&#9662;</span>`;
                    globalData = data;
                    tampil_stat(globalData.data.PlayerStat);
                    // Panggil fungsi lain untuk memproses data yang diterima
                  })
                  .catch(error => {
                    console.error("Error fetching data:", error);
                  });
              });

              // Menambahkan link ke dalam dropdown
              divDropdown.appendChild(eventLink);
            });
            dropdownContent.appendChild(divDropdown);
            container.appendChild(dropdownContent);
          }

          // Memanggil fungsi untuk mengisi dropdown
          populateDropdown(data.data.Events);

          // Menampilkan 5 pemain pertama per tipe
          const statContainer = document.createElement("div");
          statContainer.id = "dinamic-stat";
          container.appendChild(statContainer);
          tampil_stat(data.data.PlayerStat);

          function tampil_stat(data){
            const statContainer = document.getElementById("dinamic-stat");
            statContainer.innerHTML = "";

            // Jika activeFilter bukan 'all', cari Typ berdasarkan activeFilter
            let filteredData = data;
            if (activeFilter !== 'all') {
              const type = validTypes.find(type => type.name === activeFilter);
              if (type) {
                // Filter data berdasarkan Typ yang sesuai
                filteredData = data.filter(stat => stat.Typ === type.Typ);
              }
            }

            // Menampilkan data berdasarkan Typ yang sudah difilter
            filteredData.forEach(stat => {
              const typeName = validTypes.find(type => type.Typ === stat.Typ)?.name || "UNKNOWN TYPE";

              // Menyusun HTML untuk setiap statistik pemain
              let htmlContent = `
                <div class="title-detail py-2">${typeName}</div>
                <div class="containerListStat">
              `;

              const imgteam = document.getElementById("ImgTeam").src;
              // Jika activeFilter 'all', ambil hanya 5 pemain pertama
              const playersToShow = activeFilter === 'all' ? stat.Plrs.slice(0, 5) : stat.Plrs;

              playersToShow.forEach(player => {
                htmlContent += `
                  <div class="cardListStat">
                    <div style="display: flex; align-items: center">
                      <div style="color: #b0b0b0; width: 20px">${player.Rnk}</div>
                      <div style="display: flex; align-items: center">
                        <img loading="lazy" src="${imgteam}" class="icon" style="height: 20px; width: 20px" />
                        <div class="m-2">
                          <div class="namePlayer-statsDetailTeam">${player.Pnm}</div>
                          <div style="color: #b0b0b0; margin-top: 4px">${player.Tnm}</div>
                        </div>
                      </div>
                    </div>
                    <div style="color: white; font-weight: bold">${player.Scr}</div>
                  </div>
                `;
              });

              htmlContent += `</div>`; // Closing containerListStat
              statContainer.innerHTML += htmlContent;  // Append to the container
            });
            container.appendChild(statContainer);
          }
        }

        function matche(data) {
          const container = document.getElementById("dinamic-content");
          container.innerHTML = "";  // Reset kontainer sebelum menambah konten baru

          let currentCompetitionFilter = "all";  // Variabel untuk menyimpan kompetisi yang aktif
          let currentMatchType = "fixtures";  // Variabel untuk menyimpan jenis pertandingan yang ditampilkan (fixtures atau results)


          if(data.data.NextEvent != null){
            // Format tanggal dan waktu
            let date = formatTimestamp(data.data.NextEvent.time_start);
            let words = date.split("-");

            // Membuat elemen untuk match berikutnya
            const nextMatch = document.createElement("div");
            nextMatch.classList.add("next-match");
            nextMatch.innerHTML = `
              <div class="title-detail">NEXT MATCH</div>
              <div class="background-container" id="next-match">
                <div class="match-details">
                  <div class="match-info-detail">
                    <div class="match-date">${words[0]}</div>
                    <div class="match-time-wrapper">
                      <div class="teams-container">
                        <div style="display: flex; align-items: center; justify-content: center;">
                          <div class="team-info" id="team1-info">
                            <img
                              loading="lazy"
                              src="${data.data.NextEvent.team1.teamIMG}"
                              alt="${data.data.NextEvent.team1.teamNM}"
                              class="team-badge"
                              style="width: 50px; height: 50px"
                            />
                            <div class="team-nameMatch">${data.data.NextEvent.team1.teamNM}</div>
                          </div>
                          <div class="match-time">${words[1]}</div>
                          <div class="team-info" id="team2-info">
                            <img
                              loading="lazy"
                              src="${data.data.NextEvent.team2.teamIMG}"
                              alt="${data.data.NextEvent.team2.teamNM}"
                              class="team-badge"
                            />
                            <div class="team-nameMatch">${data.data.NextEvent.team2.teamNM}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            `;
            container.appendChild(nextMatch);
            let cardNext = document.getElementById("next-match");
            cardNext.addEventListener("click", () => {
              let urlcomp = data.data.NextEvent.urlComp.replace('.','/');
              window.location.href = `/match/${urlcomp}/${data.data.NextEvent.matchID}`; // Gunakan data.url sebagai target URL
            });

            // Mengambil elemen untuk team 1
            const team1 = document.getElementById("team1-info");
            const team2 = document.getElementById("team2-info")
            const info1 = document.createElement("div");
            const info2 = document.createElement("div");

            // Looping melalui lastMt untuk menambahkan status
            for (let i = data.data.NextEvent.team1.lastMt.length - 1; i >= 0; i--) {
              const matchResult = data.data.NextEvent.team1.lastMt[i];
              let dotColor = "#929292";  // Default warna abu-abu

              if (matchResult == 1) {
                dotColor = "#01c246";  // Hijau untuk kemenangan
              } else if (matchResult == 2) {
                dotColor = "#d5020d";  // Merah untuk kekalahan
              }

              info1.innerHTML += `<span class="dots" style="background-color: ${dotColor}"></span>`;
            }

            // Menambahkan info1 ke dalam team1
            team1.appendChild(info1);

            // Looping melalui lastMt untuk menambahkan status
            for (let i = data.data.NextEvent.team2.lastMt.length - 1; i >= 0; i--) {
              const matchResult = data.data.NextEvent.team2.lastMt[i];
              let dotColor = "#929292";  // Default warna abu-abu

              if (matchResult == 1) {
                dotColor = "#01c246";  // Hijau untuk kemenangan
              } else if (matchResult == 2) {
                dotColor = "#d5020d";  // Merah untuk kekalahan
              }

              info2.innerHTML += `<span class="dots" style="background-color: ${dotColor}"></span>`;
            }

            // Menambahkan info1 ke dalam team1
            team2.appendChild(info2);
          }

          // Menambahkan tombol "FIXTURES" dan "RESULTS"
          const buttonSection = document.createElement("div");
          buttonSection.classList.add("py-3");
          buttonSection.innerHTML = `
            <button class="button-matchesDetailTeam active" id="btnFixtures">
              FIXTURES
            </button>
            <button class="button-matchesDetailTeam" id="btnResults">RESULTS</button>
          `;
          container.appendChild(buttonSection);

          // Menambahkan dropdown untuk "All Competition"
          const dropdownSection = document.createElement("div");
          dropdownSection.classList.add("dropdown");
          dropdownSection.innerHTML = `
            <button class="dropdown-btn" id="dropdownBtn">
              All Competition <span>&#9662;</span>
            </button>
            <div class="dropdown-content" id="dropdownContent">
              ${data.data.Stages.map((stage, index) => {
                return `
                  <a href="#" data-index="${index}" data-snm="${stage.Snm}" data-stages="${encodeURIComponent(JSON.stringify(data.data.Stages))}">
                    ${stage.Snm}
                  </a>
                `;
              }).join("")}
            </div>
          `;
          container.appendChild(dropdownSection);

          // Event listener untuk tombol Fixtures dan Results
          document.getElementById("btnFixtures").addEventListener("click", () => {
            currentMatchType = "fixtures";
            setActiveButton("fixtures");
            renderMatches();
          });

          document.getElementById("btnResults").addEventListener("click", () => {
            currentMatchType = "results";
            setActiveButton("results");
            renderMatches();
          });

          // Event listener untuk filter kompetisi
          const dropdownContent = document.getElementById('dropdownContent');
          const dropdownBtn = document.getElementById('dropdownBtn');

          dropdownContent.addEventListener('click', function(event) {
            if (event.target && event.target.tagName === 'A') {
              event.preventDefault(); // Mencegah aksi default <a> (navigasi)

              // Mengambil data dari atribut data-*
              const index = event.target.dataset.index;
              const snm = event.target.dataset.snm;
              currentCompetitionFilter = snm; // Mengubah filter kompetisi
              setActiveCompetitionFilter(snm); // Menandai kompetisi yang aktif
              dropdownBtn.innerHTML = `${snm} <span>&#9662;</span>`; // Update teks tombol dropdown
              renderMatches();  // Render ulang setelah filter kompetisi berubah
            }
          });

          // Fungsi untuk merender ulang pertandingan berdasarkan jenis dan filter yang dipilih
          function renderMatches() {
            // Hapus konten pertandingan sebelumnya
            const formSection = document.getElementById("dinamic-comp");
            if (formSection) formSection.remove();

            const formSectionUpdated = document.createElement("div");
            formSectionUpdated.classList.add("form");
            formSectionUpdated.style.margin = "12px 0 12px";
            formSectionUpdated.id = "dinamic-comp";

            // Filter berdasarkan kompetisi yang aktif
            const filteredStages = currentCompetitionFilter === "all"
              ? data.data.Stages
              : data.data.Stages.filter(stage => stage.Snm === currentCompetitionFilter);

            // Looping untuk setiap Stage
            filteredStages.forEach((comp) => {
              const stageDiv = document.createElement("div");

              // Judul untuk setiap Stage
              const titleDetail = document.createElement("div");
              titleDetail.classList.add("title-detail", "d-flex", "justify-content-between");
              titleDetail.innerHTML = `
                <span>${comp.Snm}</span><i class="fas fa-angle-right"></i>
              `;
              stageDiv.appendChild(titleDetail);

              // Daftar pertandingan untuk setiap Stage
              const listForm = document.createElement("div");
              listForm.classList.add("list-form");

              // Filter berdasarkan jenis pertandingan (Fixtures atau Results)
              const matchesToDisplay = comp.events.filter((match) => {
                if (currentMatchType === "fixtures") {
                  return match.Status_Match !== "FT";  // Tampilkan hanya pertandingan yang belum selesai
                } else {
                  return match.Status_Match === "FT";  // Tampilkan hanya pertandingan yang sudah selesai
                }
              });

              // Jika tidak ada pertandingan yang sesuai, jangan tampilkan kompetisi ini
              if (matchesToDisplay.length === 0) return;

              matchesToDisplay.forEach((match) => {
                const cardForm = document.createElement("div");
                cardForm.classList.add("card-form", "mt-2", "mb-2");

                const infoForm = document.createElement("div");
                infoForm.classList.add("info-form");

                // Detail pertandingan
                infoForm.innerHTML = `
                  <div class="status-match">${match.Status_Match}</div>
                  <div class="info-teamForm">
                    <div class="team1-detail">
                      <img
                        loading="lazy"
                        src="${match.Team1.IMGTeam}"
                        class="icon"
                        style="height: 30px; width: 30px"
                      />
                      <div class="name-team-detail">${match.Team1.NMTeam}</div>
                    </div>
                    <div class="team2-detail">
                      <img
                        loading="lazy"
                        src="${match.Team2.IMGTeam}"
                        class="icon"
                        style="height: 30px; width: 30px"
                      />
                      <div class="name-team-detail">${match.Team2.NMTeam}</div>
                    </div>
                  </div>
                `;
                cardForm.appendChild(infoForm);

                const infoForm2 = document.createElement("div");
                infoForm2.classList.add("info-form");

                // Jika status match "FT", tampilkan skor
                if (match && match.Status_Match === "FT") {
                  const scoreForm = document.createElement("div");
                  scoreForm.classList.add("info-teamForm");
                  scoreForm.innerHTML = `
                    <div class="score-form" style="margin-bottom: 20px">${match.Score1}</div>
                    <div class="score-form">${match.Score2}</div>
                  `;
                  infoForm2.appendChild(scoreForm);
                }

                // Menambahkan status favorit
                const statusFavorite = document.createElement("div");
                statusFavorite.classList.add("status-favorite");
                statusFavorite.innerHTML = `
                  <img
                    loading="lazy"
                    src="/img/star_border.png"
                    class="icon"
                    style="height: 36px; width: 36px"
                  />
                `;
                infoForm2.appendChild(statusFavorite);
                cardForm.appendChild(infoForm2);

                listForm.appendChild(cardForm);
                // Tambahkan event listener untuk mengarahkan ke URL
                cardForm.addEventListener("click", () => {
                  let urlcomp = comp.urlComp.replace('.','/');
                  window.location.href = `/match/${urlcomp}/${match.IDMatch}`; // Gunakan data.url sebagai target URL
                });

                // Opsional: Tambahkan gaya kursor agar pengguna tahu card dapat diklik
                cardForm.style.cursor = "pointer";
              });

              stageDiv.appendChild(listForm);
              formSectionUpdated.appendChild(stageDiv);
            });

            container.appendChild(formSectionUpdated);
          }

          // Fungsi untuk menandai tombol aktif (Fixtures/Results)
          function setActiveButton(type) {
            const fixturesButton = document.getElementById("btnFixtures");
            const resultsButton = document.getElementById("btnResults");

            if (type === "fixtures") {
              fixturesButton.classList.add("active");
              resultsButton.classList.remove("active");
            } else {
              fixturesButton.classList.remove("active");
              resultsButton.classList.add("active");
            }
          }

          // Fungsi untuk menandai filter kompetisi aktif
          function setActiveCompetitionFilter(snm) {
            const dropdownLinks = document.querySelectorAll('#dropdownContent a');
            dropdownLinks.forEach(link => {
              if (link.dataset.snm === snm) {
                link.classList.add("active");
              } else {
                link.classList.remove("active");
              }
            });
          }

          // Memulai render pertama kali
          renderMatches();
        }

        function rank(data){
          const container = document.getElementById("dinamic-content");
          container.innerHTML = "";  // Reset kontainer sebelum menambah konten baru
          const name_team = document.getElementById("team-name").innerText;
          const tableRank = data.data.LeagueTable;
          const tableContainer = document.createElement("div");
          tableContainer.style.margin = "12px 0 12px";

          // Menambahkan tabel dengan struktur HTML
          tableContainer.innerHTML = `
            <table>
              <thead>
                <tr>
                  <th style="font-weight: 500; width: 5%">#</th>
                 <th class="col-team" style="width: 40%; color: white">TEAM</th>
                  <th class="col-p" style="width: 7.5%; color: white">P</th>
                  <th class="col-w"style="width: 7.5%; color: white">W</th>
                  <th class="col-d"style="width: 7.5%; color: white">D</th>
                  <th class="col-l"style="width: 7.5%; color: white">L</th>
                  <th class="col-f"style="width: 7.5%; color: white">F</th>
                  <th class="col-a"style="width: 7.5%; color: white">A</th>
                  <th class="col-gd"style="width: 7.5%; color: white">GD</th>
                  <th class="col-pts"style="width: 7.5%; color: white">PTS</th>
                </tr>
              </thead>
              <tbody id="rankTable"></tbody>
            </table>
          `;

          // Menambahkan tableContainer ke dalam container utama
          container.appendChild(tableContainer);

          // Menyiapkan tbody untuk menambahkan data tim
          const rankTable = document.getElementById("rankTable");

          // Menggunakan loop untuk menambahkan data ke dalam table
          tableRank.forEach((team) => {
            // Membuat baris baru (tr) untuk setiap tim
            const row = document.createElement("tr");
            row.classList.add("bg-secondary");
            if (team.teamNM != name_team) {
              row.classList.remove("bg-secondary");
            }

            row.style.fontSize = "14px";

            // Menambahkan data ke dalam setiap sel (td)
            row.innerHTML = `
              <td style="font-weight: 500; width: 5%; color: white">${team.rank}</td>
              <td style="width: 40%; color: white">
                <img
                  loading="lazy"
                  src="${team.teamIMG}"
                  class="icon"
                  style="height: 30px; width: 30px"
                />
                ${team.teamNM}
              </td>
              <td class="col-p" style="width: 7.5%; color: white">${team.p}</td>
              <td class="col-w" style="width: 7.5%; color: white">${team.w}</td>
              <td class="col-d" style="width: 7.5%; color: white">${team.d}</td>
              <td class="col-l" style="width: 7.5%; color: white">${team.l}</td>
              <td class="col-f" style="width: 7.5%; color: white">${team.f}</td>
              <td class="col-a" style="width: 7.5%; color: white">${team.a}</td>
              <td class="col-gd" style="width: 7.5%; color: white">${team.gd}</td>
              <td class="col-pts" style="width: 7.5%; color: white">${team.pts}</td>
            `;

            // Menambahkan baris baru ke dalam tbody
            rankTable.appendChild(row);
          });
        }

        function squad(data){
          const container = document.getElementById("dinamic-content");
          container.innerHTML = "";  // Reset kontainer sebelum menambah konten baru
          // Fungsi untuk menampilkan pemain berdasarkan posisi
          const players = data.data.player;
          const renderPlayers = (players) => {
            const groupedPlayers = players.reduce((acc, player) => {
              if (!acc[player.Pos]) {
                acc[player.Pos] = [];
              }
              acc[player.Pos].push(player);
              return acc;
            }, {});

            // Membuat HTML dinamis berdasarkan posisi
            let playerHTML = '';

            // Loop untuk setiap posisi
            for (const position in groupedPlayers) {
              const positionPlayers = groupedPlayers[position];
              playerHTML += `<span class="fw-10">${position.toUpperCase()}</span>`;
              playerHTML += `<div class="${position.toLowerCase()}-list py-2">`;

              positionPlayers.forEach(player => {
                playerHTML += `
                  <div class="subs d-flex align-items-center mb-2">
                  ${player.Np ? `<div class="subs-time border rounded-circle p-3">${player.Np}</div>` : '<div class="subs-time border rounded-circle p-3">#</div>'}
                    <div class="ms-3">
                      <div class="out"><b>${player.Pn}</b></div>
                      <div class="in"><img src="${player.RegImg}" style="width:20px"> ${player.CoNm}</div>
                    </div>
                  </div>
                `;
              });

              playerHTML += '</div>';
            }

            container.innerHTML = playerHTML;
          };

          // Render pemain setelah data siap
          renderPlayers(players);

        }

        function teamnews(data) {
            const container = document.getElementById("dinamic-content");
            if (!container) {
                console.error("Container element 'dinamic-content' not found!");
                return;
            }

            // Kosongkan container sebelum mengisi dengan berita baru
            container.innerHTML = "";

            const news = data.data.News;
            const baseURL = data.data.URL;
            news.forEach((item) => {
                const contentContainer = document.createElement("div");
                contentContainer.classList.add('py-2')
                const imageUrl = `${baseURL}/${item.cover}`;
                const newsUrl = `${baseURL}news/${item.slug}`
                contentContainer.innerHTML = `
                      <div class="news">
                        <a class="" href="#">
                          <div class="card bg-black mb-3">
                              <div class="row g-0">
                                  <div class="col-md-4">
                                      <img src=" ${imageUrl || 'https://placehold.co/600x400'}" class="img-fluid rounded-start"alt="${item.title}" loading="lazy">
                                  </div>
                                  <div class="col-md-8">
                                      <div class="card-body">
                                          <h5 class="card-title text-white">${item.title}</h5>
                                          <p class="card-text text-white">${item.description}</p>
                                          <p class="card-text text-white"><small class="text-white">Last updated ${timeAgo(item.updated_at)}</small></p>
                                      </div>
                                  </div>
                              </div>
                          </div>
                        </a>
                      </div>
                `;

                // Tambahkan setiap contentContainer ke container
                contentContainer.querySelector(".card").addEventListener("click", () => {
                    window.open(newsUrl, "_blank"); // Membuka link di tab baru
                });
                // Tambahkan ke container utama
                container.appendChild(contentContainer);

            });
        }

        function timeAgo(dateString) {
            const now = new Date();
            const pastDate = new Date(dateString);
            const diffInSeconds = Math.floor((now - pastDate) / 1000);

            const intervals = {
                year: 31536000,
                month: 2592000,
                week: 604800,
                day: 86400,
                hour: 3600,
                minute: 60,
                second: 1
            };

            for (let key in intervals) {
                const count = Math.floor(diffInSeconds / intervals[key]);
                if (count > 0) {
                    return `${count} ${key}${count !== 1 ? 's' : ''} ago`;
                }
            }
            return "Just now";
        }
  </script>
<script>
  const currentPage = "{{ $page_name }}";
</script>
<script src="{{ asset('js/script.js') }}"></script>
@endsection
