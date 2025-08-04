document.addEventListener("DOMContentLoaded",function(){if(typeof currentPage<"u"){G();const e=document.getElementById("loading-container"),s=document.getElementById("overlay");if(currentPage==="home")R(),A();else if(currentPage==="match"){const r=window.location.pathname;console.log(r);const d=r.split("/"),a=`${d[2]}.${d[3]}`,n=d[d.length-1],l={blsh:"Blocked Shot",catt:"Counter Attack",crs:"Corner Kicks",fls:"Fouls",gks:"Goalkeeper Saves",goa:"Goal kicks",ofs:"Offsides",pss:"Posession (%)",shof:"Shots Off Target",shon:"Shots On Target",ths:"Throw ins",ycs:"Yellow Cards",rcs:"Red Cards"};if(!n){console.error("ID not found in the URL!");return}const i=document.getElementById("summary-link");i.href=r;const o=[{id:"stats-link",endpoint:c=>`/api/football/detailMatch/stat/${c}`},{id:"lineups-link",endpoint:c=>`/api/football/detailMatch/lineup/${c}`},{id:"table-link",endpoint:c=>`/api/football/detailMatch/table/${a}`},{id:"news-link",endpoint:c=>`/api/football/detailMatch/news/${c}`},{id:"info-link",endpoint:c=>`/api/football/detailMatch/info/${c}`}];o.forEach(c=>{const g=document.getElementById(c.id);if(!g){console.warn(`Element with ID "${c.id}" not found.`);return}g.addEventListener("click",function(f){f.preventDefault(),e.style.display="block",s.style.display="block",i.classList.remove("active"),o.forEach(t=>{const m=document.getElementById(t.id);m&&m.classList.contains("active")&&m.classList.remove("active")}),g.classList.add("active"),fetch(c.endpoint(n),{method:"GET",headers:{"Content-Type":"application/json"}}).then(t=>{if(!t.ok)throw new Error(`HTTP error! status: ${t.status}`);return t.json()}).then(t=>{e.style.display="none",s.style.display="none";const m=document.getElementById("dinamic-content");if(m.innerHTML="",m.innerHTML=`<div class="info">
                      <div class="card bg-black border-2 border-secondary-subtle">
                        <div class="card-body">
                          <h4 class="text-center fw-semibold">
                            No Data
                          </h4>
                      </div>
                    </div>`,c.id=="stats-link"){const u=t.data[0];if(u){m.innerHTML="";for(const w in u.sum_stat[0]){const y=u.sum_stat[0][w]||0,$=u.sum_stat[1][w]||0,T=y+$;if(T>0){const S=Math.round(y/T*100),E=Math.round($/T*100),M=100-S,C=100-E,x=document.createElement("div");x.innerHTML+=`
                      <div class="d-flex justify-content-between py-2">
                        <span class="text-light">${y}</span>
                        <span class="stat-label">${l[w]||w}</span>
                        <span class="text-light">${$}</span>
                      </div>
                      <div class="d-flex justify-content-between">
                        <div class="pe-1 gap-1 d-flex justify-content-between w-50">
                          <div class="dot-white rounded-start" style="width: ${M}%;"></div>
                          <div class="dot-orange rounded-end" style="width: ${S}%;"></div>
                        </div>
                        <div class="ps-1 gap-1 d-flex justify-content-between w-50">
                          <div class="dot-orange rounded-start" style="width: ${E}%;"></div>
                          <div class="dot-white rounded-end" style="width: ${C}%;"></div>
                        </div>
                        </div>
                      </div>
                    `,m.appendChild(x)}}}}else if(c.id=="lineups-link"){if(t.data.MatchID){m.innerHTML="";const w={1:{x:[47],y:"5%"},2:{x:[26,66],y:"17%"},3:{x:[16,47,76],y:"25%"},4:{x:[11,31,61,81],y:"35%"},5:{x:[8,26,47,68,86],y:"42%"}},y={1:{x:[47],y:"91%"},2:{x:[26,66],y:"79%"},3:{x:[16,47,76],y:"71%"},4:{x:[11,31,61,81],y:"61%"},5:{x:[8,26,47,68,86],y:"54%"}},$=t.data.Team1,T=t.data.Team2,S=document.getElementsByClassName("team-nameMatch")[0].innerText,E=document.getElementsByClassName("team-nameMatch")[1].innerText,M=document.createElement("div");M.classList.add("field-container"),M.innerHTML=`
                  <img
                    src="/img/lapangan_posisi.webp"
                    alt="Soccer Field"
                    style="opacity: 0.6;"
                    class="field-image"
                  />
                  <div class="name-team" style="top: 1%; left: 6%">
                    <b>${S}</b> ${$.Fo.join("-")}
                  </div>
                  <div class="name-team" style="top: 96%; left: 6%">
                    <b>${E}</b> ${T.Fo.join("-")}
                  </div>
                  `,m.appendChild(M),P($,w,"team1"),P(T,y,"team2"),q($,T),V($,T),O($,T),X($,T)}}else if(c.id=="table-link"){if(t.data){m.innerHTML="";const w=document.getElementsByClassName("team-nameMatch")[0].innerText,y=document.getElementsByClassName("team-nameMatch")[1].innerText,$=t.data.LeagueTable,T=document.createElement("div");T.style.margin="12px 0 12px",T.innerHTML=`
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
                `,m.appendChild(T);const S=document.getElementById("rankTable");$.forEach(E=>{const M=document.createElement("tr");M.classList.add("bg-dark-1"),E.teamNM===w||E.teamNM===y?M.classList.add("highlighted"):M.classList.remove("bg-dark-1"),M.style.fontSize="14px",M.innerHTML=`
                    <td style="font-weight: 500; width: 5%; color: white">${E.rank}</td>
                    <td style="width: 40%; color: white">
                      <img
                        loading="lazy"
                        src="${E.teamIMG}"
                        class="icon"
                        style="height: 30px; width: 30px"
                      />
                      ${E.teamNM}
                    </td>
                    <td class="col-p" style="width: 7.5%; color: white">${E.p}</td>
                    <td class="col-w" style="width: 7.5%; color: white">${E.w}</td>
                    <td class="col-d" style="width: 7.5%; color: white">${E.d}</td>
                    <td class="col-l" style="width: 7.5%; color: white">${E.l}</td>
                    <td class="col-f" style="width: 7.5%; color: white">${E.f}</td>
                    <td class="col-a" style="width: 7.5%; color: white">${E.a}</td>
                    <td class="col-gd" style="width: 7.5%; color: white">${E.gd}</td>
                    <td class="col-pts" style="width: 7.5%; color: white">${E.pts}</td>
                  `,S.appendChild(M)})}}else if(c.id=="news-link"){if(t.data.News){let T=function(E){const M=document.getElementById("btnTeam1"),C=document.getElementById("btnTeam2");w==="team1"?(M.classList.add("active"),C.classList.remove("active")):(M.classList.remove("active"),C.classList.add("active"))},S=function(){T(),$.innerHTML="";let E=t.data.News[0]["news-team"];const M=t.data.URL;w==="team1"&&(E=t.data.News[1]["news-team"]),E.forEach(C=>{const x=document.createElement("div");x.classList.add("py-2");const W=`${M}/${C.cover}`,B=`${M}news/${C.slug}`;x.innerHTML=`
                          <div class="news">
                            <div class="card bg-black mb-3" id="cardNEws">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                      <a class="title" href="${B}" target="_blank">
                                        <img src=" ${W||"https://placehold.co/600x400"}" class="img-fluid rounded-start"alt="${C.title}" loading="lazy" />
                                      </a>    
                                      </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                              <a class="title" href="${B}" target="_blank">
                                                ${C.title}
                                              </a>
                                            </h5>
                                            <p class="card-text">${C.description}</p>
                                            <p class="card-text text-end"><small class="">${ee(C.updated_at)}</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                      `,x.querySelector(".card").addEventListener("click",()=>{window.open(B,"_blank")}),$.appendChild(x)})};var h=T,L=S;m.innerHTML="";let w="team1";if(!m){console.error("Container element 'dinamic-content' not found!");return}m.innerHTML="";const y=document.createElement("div");y.innerHTML=`
                <button class="button-matchesDetailTeam active" id="btnTeam1">
                  ${t.data.News[0]["name-team"]}
                </button>
                <button class="button-matchesDetailTeam" id="btnTeam2">${t.data.News[1]["name-team"]}</button>`,m.appendChild(y);const $=document.createElement("div");$.id="boxNews",m.appendChild($),document.getElementById("btnTeam1").addEventListener("click",()=>{w="team1",S()}),document.getElementById("btnTeam2").addEventListener("click",()=>{w="team2",S()}),S()}}else if(c.id=="info-link"){m.innerHTML="";const u=t.data;var b=u.stadium;b||(b="-");var v=u.time_start;v?v=k(u.time_start):v="-";var p=u.views;p?p=p.toLocaleString("id-ID"):p="-";let w=`
                <div class="col-4 col-md-4">
                  <div class="pt-2 d-flex flex-column align-items-center">
                    <div class="p-2 px-0 text-secondary" style="font-size: 11px;">
                      Select your team
                    </div>
                    <div class="p-2 bg-black border border- border-secondary rounded-3 draw" id="clickDiv" data-id="" data-match="${u.match.idMatch}">
                      <h6 class="fw-semibold">
                        Draw
                      </h6>
                    </div>
                  </div>
                </div>
                        `;u.score1!==""&&(w=`
                    <div class="col-4 col-md-4">
                      <div class="progress-container">
                        <div class="progress-labels">
                            <div class="progress-label">${u.vote.team1[1]}%</div>
                            <div class="progress-label">${u.vote.draw[1]}%</div>
                            <div class="progress-label">${u.vote.team2[1]}%</div>
                        </div>
                        <div class="progress bg-transparent border" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-orange" style="width: ${u.vote.team1[1]}%"></div>
                            <div class="progress-bar bg-secondary" style="width: ${u.vote.draw[1]}%"></div>
                            <div class="progress-bar bg-danger" style="width: ${u.vote.team2[1]}%"></div>
                        </div>
                      </div>
                      <div class="votes text-center ">
                        ${u.vote.total_vote} votes
                      </div>
                    </div>
                  `),m.innerHTML=`
                <div class="info">
                  <h6>
                    Match Info
                  </h6>
                    <div class="card bg-black border-2 border-secondary-subtle">
                      <div class="card-body">
                        <div class="d-flex flex-row justify-content-center flex-wrap">
                          <div class="p-2 gap-2 align-items-center">
                            <i class="fa-regular fa-calendar-days fa-xl"></i>
                            ${v}
                          </div>
                          <div class="p-2 gap-2 align-items-center">
                            <i class="fa-solid fa-monument fa-xl"></i> 
                            ${b}
                          </div>
                          <div class="p-2 gap-2 align-items-center">
                            <i class="fa-solid fa-users"></i>
                            ${p}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                    <div class="win">
                      <h6 class="fw-bold mb-4">
                        WHO WILL WIN?
                      </h6>
                      <div class="row cols3">
                        <div class="col-4 col-md-4 p-0 p-md-2">
                          <div class="d-flex flex-column align-items-center">
                            <div class="p-md-2 px-0 py-2  team-name text-uppercase fw-semibold">
                              ${u.match.team1.NMTeam}
                            </div>
                            <div class="p-2 bg-black border border- border-secondary rounded-3" id="clickDiv" data-id="${u.match.team1.IDTeam}" data-match="${u.match.idMatch}">
                              <img class="" src="/${u.match.team1.IMGJersey}" width="50" alt="Default Team 1">
                            </div>
                          </div>
                        </div>
                        ${w}
                        <div class="col-4 col-md-4 p-0 p-md-2">
                          <div class="d-flex flex-column align-items-center">
                            <div class="p-2 team-name text-uppercase fw-semibold">
                              ${u.match.team2.NMTeam}
                            </div>
                            <div class="p-2 bg-black border border- border-secondary rounded-3" id="clickDiv" data-id="${u.match.team2.IDTeam}" data-match="${u.match.idMatch}">
                              <img class="" src="/${u.match.team2.IMGJersey}" width="50" alt="Default Team 1">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  `}else console.error("Container element not found!");document.addEventListener("click",function(u){const w=u.target.closest(".p-2.bg-black");if(w&&w.hasAttribute("data-id")&&w.hasAttribute("data-match")){const y=w.getAttribute("data-id"),$=w.getAttribute("data-match");fetch("/api/sendVote",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({id:y,match:$})}).then(S=>S.json()).then(S=>S.status===200?(e.style.display="none",s.style.display="none",Swal.fire({icon:"success",title:"Success Vote!"})):Swal.fire({icon:"error",title:"Terjadi Kesalahan!",text:S.message})).catch(S=>{console.error("Terjadi kesalahan:",S),alert("Terjadi kesalahan saat mengirimkan request.")})}})}).catch(t=>{console.error(`Error fetching ${c.endpoint(n)}:`,t)})})})}else if(currentPage==="comp"){const r=[{id:"overview-link"},{id:"matches-link"},{id:"table-link"}];D(data.data),r.forEach(d=>{const a=document.getElementById(d.id);if(!a){console.warn(`Element with ID "${d.id}" not found.`);return}a.addEventListener("click",function(n){n.preventDefault(),a.classList.remove("active"),r.forEach(l=>{const i=document.getElementById(l.id);i&&i.classList.contains("active")&&i.classList.remove("active")}),a.classList.add("active"),d.id=="overview-link"?D(data.data):d.id=="matches-link"?H(data.data.Events,"FIXTURES",data.data.urlComp):d.id=="table-link"?U(data.data.LeagueTable):console.error("404")})})}}});function F(){const e=document.getElementById("leftSide"),s=document.getElementById("new_search");window.innerWidth<=720?s.appendChild(e):document.getElementById("main-search").appendChild(e)}window.addEventListener("resize",F);window.addEventListener("load",F);function D(e){const s=document.getElementById("dinamic-content");s.innerHTML="";const r=document.createElement("div");r.classList.add("d-flex","justify-content-between"),r.innerHTML="<span>FIXTURES</span>",s.appendChild(r);const d=document.createElement("div");d.classList.add("grid-container"),s.appendChild(d);let a=0;for(let p=0;p<e.Events.length;p++)if(e.Events[p].Status_Match=="NS"&&a<=2){a++;const h=e.Events[p],L=h.Team1,u=h.Team2,w=k(h.time_start),y=document.createElement("div");y.classList.add("card"),y.innerHTML=`
              <div class="component-1">
                <div class="d-flex justify-content-between ms-3 me-3 py-3">
                  <div class="live-wrapper">
                      <b class="live">${h.Status_Match}</b>
                  </div>
                  <span class="team-score">
                    <div class="star-container" onclick="toggleStar(this)">
                      <img src="/img/star_border.png" class="star default" alt="Star Border">
                      <img src="/img/star.png" class="star filled" alt="Star Filled">
                    </div>
                  </span>
                </div>
                <div class="frame-group">
                  <div class="chelsea-fcsvg-parent">
                      <img class="chelsea-fcsvg-icon" alt="${L.NMTeam}" src="${L.IMGTeam}">
                      
                      <i class="vs">VS</i>
                      <img class="chelsea-fcsvg-icon" alt="${u.NMTeam}" src="${u.IMGTeam}">
                      
                  </div>
                  <div class="frame-container">
                      <div class="nd-round-parent">
                        <i class="nd-round"></i>
                        <i class="nd-round"></i>
                      </div>
                      <div class="spacing-line" ></div>
                  </div>
                  <div class="frame-div">
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${L.NMTeam}</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${u.NMTeam}</div>
                    </div>
                    <img class="group-item" alt="" src="/img/Line 244.png">
                  </div>
                </div>
                <div class="button-primary">
                    <b class="button"><img alt="" src="/img/calendar_card.png"> ${w}</b>
                </div>
              </div>
            `,y.addEventListener("click",()=>{window.location.href=`/match/${e.urlComp}/${h.IDMatch}`}),y.style.cursor="pointer",d.appendChild(y)}const n=document.createElement("div");n.classList.add("d-flex","justify-content-between"),n.innerHTML=`
        <div class="progress-dots">
          <div class="progress-bar-orange rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
        </div>
        <!-- Show All -->
        <p class="show-all" id="show-all-fix">Show All</p>
    `,s.appendChild(n),document.getElementById("show-all-fix").addEventListener("click",()=>{H(e.Events,"FIXTURES",e.urlComp)});const l=document.createElement("div");l.classList.add("d-flex","justify-content-between"),l.innerHTML="<span>RESULTS</span>",s.appendChild(l);const i=document.createElement("div");i.classList.add("grid-container"),s.appendChild(i);let o=0;for(let p=0;p<e.Events.length;p++)if(e.Events[p].Status_Match=="FT"&&o<=2){o++;const h=e.Events[p],L=h.Team1,u=h.Team2,w=k(h.time_start),y=document.createElement("div");y.classList.add("card"),y.innerHTML=`
                <div class="component-1">
                <div class="d-flex justify-content-between ms-3 me-3 py-3">
                  <div class="live-wrapper">
                      <b class="live">${h.Status_Match}</b>
                  </div>
                  <span class="team-score">
                    <div class="star-container" onclick="toggleStar(this)">
                      <img src="/img/star_border.png" class="star default" alt="Star Border">
                      <img src="/img/star.png" class="star filled" alt="Star Filled">
                    </div>
                  </span>
                </div>
                <div class="frame-group">
                  <div class="chelsea-fcsvg-parent">
                      <img class="chelsea-fcsvg-icon" alt="${L.NMTeam}" src="${L.IMGTeam}">
                      
                      <i class="vs">VS</i>
                      <img class="chelsea-fcsvg-icon" alt="${u.NMTeam}" src="${u.IMGTeam}">
                      
                  </div>
                  <div class="frame-container">
                      <div class="nd-round-parent">
                        <i class="nd-round"></i>
                        <i class="nd-round"></i>
                      </div>
                      <div class="spacing-line" ></div>
                  </div>
                  <div class="frame-div">
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${L.NMTeam}</div>
                        <span class="team-score">${h.Score1}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${u.NMTeam}</div>
                        <span class="team-score">${h.Score2}</span>
                    </div>
                    <img class="group-item" alt="" src="/img/Line 244.png">
                  </div>
                </div>
                <div class="button-primary">
                    <b class="button"><img alt="" src="/img/calendar_card.png"> ${w}</b>
                </div>
              </div>
            `,y.addEventListener("click",()=>{window.location.href=`/match/${e.urlComp}/${h.IDMatch}`}),y.style.cursor="pointer",i.appendChild(y)}const c=document.createElement("div");c.classList.add("d-flex","justify-content-between"),c.innerHTML=`
        <div class="progress-dots">
          <div class="progress-bar-orange rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
        </div>
        <!-- Show All -->
        <p class="show-all" id="show-all-res">Show All</p>
    `,s.appendChild(c),document.getElementById("show-all-res").addEventListener("click",()=>{H(e.Events,"RESULTS",e.urlComp)});const g=document.createElement("div");g.classList.add("d-flex","justify-content-between"),g.innerHTML="<span>TABLE</span>",s.appendChild(g);const f=document.createElement("div");f.style.margin="12px 0 12px",f.innerHTML=`
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
    `,s.appendChild(f);const t=document.getElementById("rankTable"),m=e.LeagueTable;let b=0;m.forEach(p=>{if(b<=4){const h=document.createElement("tr");h.style.fontSize="14px",h.innerHTML=`
        <td style="font-weight: 500; width: 5%; color: white">${p.rank}</td>
        <td style="width: 40%; color: white">
                <img
                  loading="lazy"
                  src="${p.teamIMG}"
                  class="icon"
                  style="height: 30px; width: 30px"
                />
                ${p.teamNM}
              </td>
              <td style="width: 7.5%; color: white">${p.p}</td>
              <td style="width: 7.5%; color: white">${p.w}</td>
              <td style="width: 7.5%; color: white">${p.d}</td>
              <td style="width: 7.5%; color: white">${p.l}</td>
              <td style="width: 7.5%; color: white">${p.f}</td>
              <td style="width: 7.5%; color: white">${p.a}</td>
              <td style="width: 7.5%; color: white">${p.gd}</td>
              <td style="width: 7.5%; color: white">${p.pts}</td>
        `,t.appendChild(h)}b++});const v=document.createElement("div");v.classList.add("d-flex","justify-content-between"),v.innerHTML=`
        <div class="progress-dots">
          <div class="progress-bar-orange rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
        </div>
        <!-- Show All -->
        <p class="show-all" id="show-all-rank">Show All</p>
    `,s.appendChild(v),document.getElementById("show-all-rank").addEventListener("click",()=>{U(e.LeagueTable)})}function H(e,s,r){const d=document.getElementById("dinamic-content");d.innerHTML="";for(var a=document.getElementById("matches-link"),n=document.getElementsByClassName("menu-link"),l=0;l<n.length;l++)n[l].classList.remove("active");a.classList.add("active");const i=document.createElement("button");i.classList.add("button-matchesDetailTeam"),i.textContent="FIXTURES";const o=document.createElement("button");o.classList.add("button-matchesDetailTeam","active"),o.textContent="RESULTS";let c="FT";s=="FIXTURES"&&(c="NS",i.classList.add("active"),o.classList.remove("active")),d.appendChild(i),i.addEventListener("click",()=>{H(e,"FIXTURES",r)}),d.appendChild(o),o.addEventListener("click",()=>{H(e,"RESULTS",r)});const g=document.createElement("div");g.classList.add("grid-container"),d.appendChild(g);for(let f=0;f<e.length;f++){const t=e[f];let m="",b="";if(t.Score1!==null&&t.Score1!==void 0&&t.Score1!==""&&(m=`<span class="team-score">${t.Score1}</span>`),t.Score2!==null&&t.Score2!==void 0&&t.Score2!==""&&(b=`<span class="team-score">${t.Score2}</span>`),e[f].Status_Match==c){const v=t.Team1,p=t.Team2,h=k(t.time_start),L=document.createElement("div");L.classList.add("card"),L.innerHTML=`
                <div class="component-1">
                <div class="d-flex justify-content-between ms-3 me-3 py-3">
                  <div class="live-wrapper">
                      <b class="live">${t.Status_Match}</b>
                  </div>
                  <span class="team-score">
                    <div class="star-container" onclick="toggleStar(this)">
                      <img src="/img/star_border.png" class="star default" alt="Star Border">
                      <img src="/img/star.png" class="star filled" alt="Star Filled">
                    </div>
                  </span>
                </div>
                <div class="frame-group">
                  <div class="chelsea-fcsvg-parent">
                      <img class="chelsea-fcsvg-icon" alt="${v.NMTeam}" src="${v.IMGTeam}">
                      
                      <i class="vs">VS</i>
                      <img class="chelsea-fcsvg-icon" alt="${p.NMTeam}" src="${p.IMGTeam}">
                      
                  </div>
                  <div class="frame-container">
                      <div class="nd-round-parent">
                        <i class="nd-round"></i>
                        <i class="nd-round"></i>
                      </div>
                      <div class="spacing-line" ></div>
                  </div>
                  <div class="frame-div">
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${v.NMTeam}</div>
                        ${m}
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${p.NMTeam}</div>
                        ${b}
                    </div>
                    <img class="group-item" alt="" src="/img/Line 244.png">
                  </div>
                </div>
                <div class="button-primary">
                    <b class="button"><img alt="" src="/img/calendar_card.png"> ${h}</b>
                </div>
              </div>
            `,L.addEventListener("click",()=>{window.location.href=`/match/${r}/${t.IDMatch}`}),L.style.cursor="pointer",g.appendChild(L)}}}function U(e){const s=document.getElementById("dinamic-content");s.innerHTML="";for(var r=document.getElementById("table-link"),d=document.getElementsByClassName("menu-link"),a=0;a<d.length;a++)d[a].classList.remove("active");r.classList.add("active");const n=document.createElement("div");n.style.margin="12px 0 12px",n.innerHTML=`
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
    `,s.appendChild(n);const l=document.getElementById("rankTable");e.forEach(o=>{const c=document.createElement("tr");c.style.fontSize="14px",c.innerHTML=`
        <td style="font-weight: 500; width: 5%; color: white">${o.rank}</td>
       <td style="width: 40%; color: white">
                <img
                  loading="lazy"
                  src="${o.teamIMG}"
                  class="icon"
                  style="height: 30px; width: 30px"
                />
                ${o.teamNM}
              </td>
              <td style="width: 7.5%; color: white">${o.p}</td>
              <td style="width: 7.5%; color: white">${o.w}</td>
              <td style="width: 7.5%; color: white">${o.d}</td>
              <td style="width: 7.5%; color: white">${o.l}</td>
              <td style="width: 7.5%; color: white">${o.f}</td>
              <td style="width: 7.5%; color: white">${o.a}</td>
              <td style="width: 7.5%; color: white">${o.gd}</td>
              <td style="width: 7.5%; color: white">${o.pts}</td>
        `,l.appendChild(c)})}document.querySelectorAll(".player-name").forEach(e=>{const s=e.getAttribute("data-name"),r=10;if(s.length>r){const d=s.split(" "),a=d.length>1?`${d[0]} ${d[1][0]}.`:s.slice(0,r-1)+".";e.textContent=a}else e.textContent=s});function X(e,s){const r=document.getElementById("dinamic-content"),d=document.createElement("div");d.classList.add("py-2"),d.innerHTML=`
      <span class="fw-10">COACHES</span>
    `,r.appendChild(d);const a=document.createElement("div");a.classList.add("border","rounded");for(let n=0;n<e.Pos.length;n++)if(e.Pos[n].Pon=="COACH"){for(let l=0;l<s.Pos.length;l++)if(s.Pos[l].Pon=="COACH"){const i=document.createElement("div");i.classList.add("substitution","d-flex","justify-content-between","align-items-center"),i.innerHTML=`
              <div class="subs">
                <span class="px-2">${e.Pos[n].Fn} ${e.Pos[n].Ln}</span>
              </div>

              <div class="subs">
                <span class="px-2">${s.Pos[l].Fn} ${s.Pos[l].Ln}</span>
              </div>
            `,a.appendChild(i)}}a.children.length>0&&d.appendChild(a)}function O(e,s){var r=e.IS.length;const d=document.getElementById("dinamic-content");e.IS.length<s.IS.length&&(r=s.IS.length);const a=document.createElement("div");a.classList.add("py-2"),a.innerHTML=`
      <span class="fw-10">INJURIES & SUSPENSIONS</span>
    `,d.appendChild(a);const n=document.createElement("div");n.classList.add("border","rounded");for(let l=0;l<r;l+=1){const i=e.IS[l],o=s.IS[l];if(i||o){const c=document.createElement("div");c.classList.add("substitution","d-flex","justify-content-between","align-items-center"),i?c.innerHTML+=`
          <div class="subs">
            <div>
              <div class="out"><b>${i.Fn} ${i.Ln}</b></div>
              <div class="in">${i.Rs}</div>
            </div>
          </div>
        `:c.innerHTML+=`
          <div class="subs">
            <div>
              <div class="out"><b></b></div>
              <div class="in"></div>
            </div>
          </div>
        `,o?c.innerHTML+=`
          <div class="subs">
            <div>
              <div class="out"><b>${o.Fn} ${o.Ln}</b></div>
              <div class="in">${o.Rs}</div>
            </div>
          </div>
        `:c.innerHTML+=`
          <div class="subs">
            <div>
              <div class="out"><b></b></div>
              <div class="in"></div>
            </div>
          </div>
        `,n.appendChild(c)}}n.children.length>0&&a.appendChild(n)}function V(e,s){var r=e.Pos.length;const d=document.getElementById("dinamic-content");e.Pos.length<s.Pos.length&&(r=s.Pos.length);const a=document.createElement("div");a.classList.add("py-2"),a.innerHTML=`
      <span class="fw-10">SUBSTITUTE PLAYERS</span>
    `,d.appendChild(a);const n=document.createElement("div");n.classList.add("border","rounded");for(let l=0;l<r;l+=1){const i=e.Pos[l],o=s.Pos[l];if(i&&i.Pon=="SUBSTITUTE_PLAYER"||o&&o.Pon=="SUBSTITUTE_PLAYER"){const c=document.createElement("div");c.classList.add("substitution","d-flex","justify-content-between","align-items-center"),i&&(c.innerHTML+=`
          <div class="subs">
            <div class="subs-time border rounded-circle">${i.Np}</div>
            <div>
              <div>
                <span class="px-2">${i.Fn} ${i.Ln}</span>
              </div>
            </div>
          </div>
        `),o&&(c.innerHTML+=`
          <div class="subs">
            <div class="subs-time border rounded-circle">${i.Np}</div>
            <div>
              <span class="px-2">${o.Fn} ${o.Ln}</span>
            </div>
          </div>
        `),n.appendChild(c)}}n.children.length>0&&a.appendChild(n)}function q(e,s){var r=e.Subs.length;const d=document.getElementById("dinamic-content");e.Subs.length<s.Subs.length&&(r=s.Subs.length);const a=document.createElement("div");a.classList.add("py-2"),a.innerHTML=`
      <span class="fw-10">SUBSTITUTION</span>
    `,d.appendChild(a);const n=document.createElement("div");n.classList.add("border","rounded");for(let l=0;l<r;l+=2){const i=e.Subs[l],o=e.Subs[l+1],c=s.Subs[l],g=s.Subs[l+1];if(i||c){const f=document.createElement("div");f.classList.add("substitution","d-flex","justify-content-between","align-items-center"),i&&(f.innerHTML+=`
          <div class="subs">
            <div class="subs-time">${i.Min}'</div>
            <div>
              <div class="out">
                <i class="fas fa-arrow-alt-circle-down text-danger"></i>
                ${i.Pn}
              </div>
              ${o?`<div class="in">
                      <i class="fas fa-arrow-alt-circle-up text-warning"></i>
                      <b>${o.Pn}</b>
                    </div>`:""}
            </div>
          </div>
        `),c&&(f.innerHTML+=`
          <div class="subs">
            <div class="subs-time">${c.Min}'</div>
            <div>
              <div class="out">
                <i class="fas fa-arrow-alt-circle-down text-danger"></i>
                ${c.Pn}
              </div>
              ${g?`<div class="in">
                      <i class="fas fa-arrow-alt-circle-up text-warning"></i>
                      <b>${g.Pn}</b>
                    </div>`:""}
            </div>
          </div>
        `),n.appendChild(f)}}n.children.length>0&&a.appendChild(n)}function P(e,s,r){const d=document.querySelector(".field-container"),a=document.getElementById("dinamic-content");e.Pos.forEach(n=>{if(n.Fp&&n.Fp!="1:1"){const[l,i]=n.Fp.split(":").map(Number),o=e.Fo[l-2],g=s[o].x[i-1],f=s[l].y,t=document.createElement("div");t.className=`player ${r}`,t.style.top=f,t.style.left=`${g}%`,t.textContent=n.Np;const m=document.createElement("span");m.className="player-name";const b=`${n.Fn} ${n.Ln}`,v=10;if(m.textContent=b,b.length>v){const p=b.split(" "),h=p.length>1?`${p[0]} ${p[1][0]}.`:b.slice(0,v-1)+".";m.textContent=h}t.appendChild(m),d.appendChild(t)}else if(n.Fp&&n.Fp=="1:1"){const l=document.createElement("div");l.className=`player ${r}`,r=="team1"?l.style.top="5%":l.style.top="91%",l.style.left="47%",l.textContent=n.Np;const i=document.createElement("span");i.className="player-name";const o=`${n.Fn} ${n.Ln}`,c=10;if(i.textContent=o,o.length>c){const g=o.split(" "),f=g.length>1?`${g[0]} ${g[1][0]}.`:o.slice(0,c-1)+".";i.textContent=f}l.appendChild(i),d.appendChild(l)}}),a.appendChild(d)}async function G(){try{const e=(t,m)=>{let b;return(...v)=>{clearTimeout(b),b=setTimeout(()=>t(...v),m)}},r=await(await fetch("/api/search",{method:"GET",headers:{"Content-Type":"application/json"}})).json(),d=document.getElementById("leftSide");d.innerHTML="";const a=document.createElement("div");a.classList.add("mb-4"),a.innerHTML=`
        <input
          type="text"
          class="form-control"
          placeholder="Search..."
          id="searchInput"
        />
      `,d.appendChild(a);const n=document.createElement("div");n.classList.add("mb-4"),n.innerHTML='<h6 class="text-uppercase">Region</h6>';const l=document.createElement("ul");l.classList.add("list-unstyled"),l.id="region",r.data.categories.forEach(t=>{const m=document.createElement("li");m.classList.add("d-flex","align-items-center","mb-3","p-2"),m.id="leftCard",m.setAttribute("onclick",`detailCountry('${t.Ccd}', '${t.Cnm}')`),m.innerHTML=`
          <img
            src="${t.badgeUrl||"default-region.png"}"
            alt="${t.Ccd}"
            class="me-3"
            style="width: 20px; object-fit: cover"
          />
          <div class="d-flex flex-column">
            <span class="text-truncate">${t.Cnm}</span>
            <small class="text-light text-truncate"></small>
          </div>
        `,l.appendChild(m)}),n.appendChild(l),d.appendChild(n);const i=document.createElement("div");i.innerHTML='<h6 class="text-uppercase">Teams</h6>';const o=document.createElement("ul");o.classList.add("list-unstyled"),o.id="teams",r.data.teams.forEach(t=>{const m=document.createElement("li");m.classList.add("d-flex","align-items-center","mb-3","p-2"),m.id="leftCard",m.innerHTML=`
          <img
            src="${t.IMGTeam||"default-team.png"}"
            alt="${t.NMTeam}"
            class="me-3"
            style="width: 20px; object-fit: cover"
          />
          <div class="d-flex flex-column">
            <span class="text-truncate">${t.NMTeam}</span>
            <small class="text-light text-truncate">${t.CoNm}</small>
          </div>
        `,m.addEventListener("click",()=>{window.location.href=`/team/${t.IDTeam}/`}),o.appendChild(m)}),i.appendChild(o),d.appendChild(i);const c=document.createElement("div");c.classList.add("mb-4"),c.innerHTML='<h6 class="text-uppercase">Competition</h6>';const g=document.createElement("ul");g.classList.add("list-unstyled"),g.id="competition",r.data.stages.forEach(t=>{const m=document.createElement("li");m.classList.add("d-flex","align-items-center","mb-3","p-2"),m.id="leftCard",m.innerHTML=`
          <img
            src="${t.badgeUrl||"default-competition.png"}"
            alt="${t.Scd}"
            class="me-3"
            style="width: 20px; object-fit: cover"
          />
          <div class="d-flex flex-column">
            <span class="text-truncate">${t.Snm}</span>
            <small class="text-light text-truncate">${t.Cnm}</small>
          </div>
        `,m.addEventListener("click",()=>{var b=t.urlComp.replace(".","/");window.location.href=`/comp/${b}/`}),g.appendChild(m)}),c.appendChild(g),d.appendChild(c),document.getElementById("searchInput").addEventListener("input",e(async t=>{const m=t.target.value.trim();m?await z(m):G()},300))}catch(e){console.error("Error fetching data:",e)}}const z=async e=>{try{const r=await(await fetch(`/api/search/${e}`)).json(),d=document.getElementById("region"),a=document.getElementById("teams"),n=document.getElementById("competition");d.innerHTML="",a.innerHTML="",n.innerHTML="",N(d,r.data.categories,J),N(a,r.data.teams,Y),N(n,r.data.stages,K)}catch(s){console.error("Error in performSearch:",s)}},N=(e,s,r)=>{s&&s.length?s.forEach(d=>e.appendChild(r(d))):e.innerHTML="<li class='text-muted'>No results found</li>"},J=e=>{const s=document.createElement("li");return s.classList.add("d-flex","align-items-center","mb-3","p-2"),s.id="leftCard",s.setAttribute("onclick",`detailCountry('${e.Ccd}', '${e.Cnm}')`),s.innerHTML=`
      <img
        src="${e.badgeUrl||"default-region.png"}"
        alt="${e.Ccd}"
        class="me-3"
        style="width: 20px; object-fit: cover"
      />
      <div class="d-flex flex-column">
        <span class="text-truncate">${e.Cnm}</span>
      </div>
    `,s},Y=e=>{const s=document.createElement("li");return s.classList.add("d-flex","align-items-center","mb-3","p-2"),s.id="leftCard",s.innerHTML=`
      <img
        src="${e.IMGTeam||"default-team.png"}"
        alt="${e.NMTeam}"
        class="me-3"
        style="width: 20px; object-fit: cover"
      />
      <div class="d-flex flex-column">
        <span class="text-truncate">${e.NMTeam}</span>
        <small class="text-light text-truncate">${e.CoNm}</small>
      </div>
    `,s.addEventListener("click",()=>{window.location.href=`/team/${e.IDTeam}/`}),s},K=e=>{const s=document.createElement("li");return s.classList.add("d-flex","align-items-center","mb-3","p-2"),s.id="leftCard",s.innerHTML=`
      <img
        src="${e.badgeUrl||"default-competition.png"}"
        alt="${e.Scd}"
        class="me-3"
        style="width: 20px; object-fit: cover"
      />
      <div class="d-flex flex-column">
        <span class="text-truncate">${e.Snm}</span>
        <small class="text-light text-truncate">${e.Cnm}</small>
      </div>
    `,s.addEventListener("click",()=>{var r=e.urlComp.replace(".","/");window.location.href=`/comp/${r}/`}),s},Q=document.getElementById("kt_datepicker_1");Q.flatpickr({dateFormat:"Ymd",disableMobile:"true",onChange:function(e,s,r){A(s),R(s)}});async function A(e=null){const s=e?`/api/sorted-data/${e}`:"/api/sorted-data",r=document.getElementsByClassName("scrollable-container");Array.from(r).forEach(d=>{d.innerHTML=""});try{const a=await(await fetch(s,{method:"GET",headers:{"Content-Type":"application/json"}})).json(),n=document.getElementById("live");n.innerHTML="";const l=a.live;document.getElementById("show-all-live").addEventListener("click",function(){_(l,"LIVE")}),l&&l.length>0&&l.forEach((v,p)=>{const h=document.createElement("button");h.classList.add("circle-button"),p===0&&(h.classList.add("active"),I("live",v,0,v.urlComp)),h.innerHTML=`
              <div class="icon">
                  <img src="${v.badgeUrl||"default-icon.png"}" alt="${v.Scd}" />
              </div>
              <span>${v.Snm}</span>
          `,h.addEventListener("click",()=>{const L=n.getElementsByClassName("circle-button");Array.from(L).forEach(u=>u.classList.remove("active")),h.classList.add("active"),I("live",v,0,v.urlComp)}),n.appendChild(h)});const o=document.getElementById("next");o.innerHTML="";const c=a.next;document.getElementById("show-all-upcoming").addEventListener("click",function(){_(c,"UPCOMING")}),c&&c.length>0&&c.forEach((v,p)=>{const h=document.createElement("button");h.classList.add("circle-button"),p===0&&(h.classList.add("active"),I("next",v,1,v.urlComp)),h.innerHTML=`
              <div class="icon">
                  <img src="${v.badgeUrl||"default-icon.png"}" alt="${v.Scd}" />
              </div>
              <span>${v.Snm}</span>
          `,h.addEventListener("click",()=>{const L=o.getElementsByClassName("circle-button");Array.from(L).forEach(u=>u.classList.remove("active")),h.classList.add("active"),I("next",v,1,v.urlComp)}),o.appendChild(h)});const f=document.getElementById("previous");f.innerHTML="";const t=a.previous;document.getElementById("show-all-previous").addEventListener("click",function(){_(t,"PREVIOUS")}),t&&t.length>0&&t.forEach((v,p)=>{const h=document.createElement("button");h.innerHTML="",h.classList.add("circle-button"),p===0&&(h.classList.add("active"),I("prev",v,2,v.urlComp)),h.innerHTML=`
              <div class="icon">
                  <img src="${v.badgeUrl||"default-icon.png"}" alt="${v.Scd}" />
              </div>
              <span class="title">${v.Snm}</span>
          `,h.addEventListener("click",()=>{const L=f.getElementsByClassName("circle-button");Array.from(L).forEach(u=>u.classList.remove("active")),h.classList.add("active"),I("prev",v,2,v.urlComp)}),f.appendChild(h)}),document.querySelectorAll(".button-container").forEach(v=>{let p=!1,h,L;v.addEventListener("mousedown",u=>{u.preventDefault(),p=!0,h=u.pageX,L=v.scrollLeft,document.body.style.cursor="grabbing"}),v.addEventListener("mousemove",u=>{if(!p)return;u.preventDefault();const w=u.pageX-h,y=L-w;y<=0?v.scrollLeft=0:y>=v.scrollWidth-v.clientWidth?v.scrollLeft=v.scrollWidth-v.clientWidth:v.scrollLeft=y}),v.addEventListener("mouseup",()=>{p=!1,document.body.style.cursor="default"}),v.addEventListener("mouseleave",()=>{p=!1,document.body.style.cursor="default"})})}catch(d){console.error("Error fetching data:",d)}}function _(e,s){const r=document.getElementById("main-div"),d=document.getElementById("right-content");d.innerHTML="",r.innerHTML="",r.className="col-md-8";const a=document.createElement("div");a.classList.add("p-3","rounded","shadow-sm","text-light"),a.innerHTML=`
      <p>
        <a href="/" style="text-decoration: none;">
          <i class="fas fa-arrow-left" style="color: white;"></i>
        </a> 
        <b>${s}</b> MATCHES
      </p>
      <div class="button-container" id="button-container"></div>

      <div class="grid-container"></div>
    `,r.appendChild(a);const n=document.getElementById("button-container");e&&e.length>0?e.forEach((i,o)=>{const c=document.createElement("button");c.classList.add("circle-button"),o===0&&(c.classList.add("active"),j(i,0,i.urlComp)),c.innerHTML=`
          <div class="icon">
            <img src="${i.badgeUrl||"default-icon.png"}" alt="${i.Scd}" />
          </div>
          <span>${i.Snm}</span>
        `,c.addEventListener("click",()=>{const g=n.getElementsByClassName("circle-button");Array.from(g).forEach(f=>f.classList.remove("active")),c.classList.add("active"),j(i,0,i.urlComp)}),n.appendChild(c)}):n.innerHTML='<p class="text-center text-muted">No matches available.</p>',document.querySelectorAll(".button-container").forEach(i=>{let o=!1,c,g;i.addEventListener("mousedown",f=>{f.preventDefault(),o=!0,c=f.pageX,g=i.scrollLeft,document.body.style.cursor="grabbing"}),i.addEventListener("mousemove",f=>{if(!o)return;f.preventDefault();const t=f.pageX-c,m=g-t;m<=0?i.scrollLeft=0:m>=i.scrollWidth-i.clientWidth?i.scrollLeft=i.scrollWidth-i.clientWidth:i.scrollLeft=m}),i.addEventListener("mouseup",()=>{o=!1,document.body.style.cursor="default"}),i.addEventListener("mouseleave",()=>{o=!1,document.body.style.cursor="default"})})}function j(e,s,r){const d=document.getElementsByClassName("grid-container")[s];d.innerHTML="",e&&e.events&&Array.isArray(e.events)?e.events.forEach(a=>{const n=document.createElement("div");n.classList.add("card");let l="",i="";a.Score1!==null&&a.Score1!==void 0&&a.Score1!==""&&(l=`<div class="chelsea">${a.Score1}</div>`),a.Score2!==null&&a.Score2!==void 0&&a.Score2!==""&&(i=`<div class="chelsea">${a.Score2}</div>`),n.innerHTML=`
              <div class="component-1">
                <div class="d-flex justify-content-between ms-3 me-3 py-3">
                  <div class="live-wrapper">
                      <b class="live">${a.Status_Match}</b>
                  </div>
                  <span class="team-score">
                    <div class="star-container" onclick="toggleStar(this)">
                      <img src="/img/star_border.png" class="star default" alt="Star Border">
                      <img src="/img/star.png" class="star filled" alt="Star Filled">
                    </div>
                  </span>
                </div>
                <div class="frame-group">
                  <div class="chelsea-fcsvg-parent">
                      <img class="chelsea-fcsvg-icon" alt="${a.Team1.NMTeam}" src="${a.Team1.IMGTeam}">
                      
                      <i class="vs">VS</i>
                      <img class="chelsea-fcsvg-icon" alt="${a.Team2.NMTeam}" src="${a.Team2.IMGTeam}">
                      
                  </div>
                  <div class="frame-container">
                      <div class="nd-round-parent">
                        <i class="nd-round"></i>
                        <i class="nd-round"></i>
                      </div>
                      <div class="spacing-line" ></div>
                  </div>
                  <div class="frame-div">
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${a.Team1.NMTeam}</div>
                        ${l}
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${a.Team2.NMTeam}</div>
                        ${i}
                    </div>
                    <img class="group-item" alt="" src="/img/Line 244.png">
                  </div>
                </div>
                <div class="button-primary">
                    <b class="button"><img alt="" src="/img/calendar_card.png"> ${k(a.time_start)}</b>
                </div>
              </div>
          `,n.addEventListener("click",()=>{window.location.href=`/match/${r}/${a.IDMatch}`}),n.style.cursor="pointer",d.appendChild(n)}):console.log("No events data available.")}function I(e,s,r,d){const a=document.getElementsByClassName("scrollable-container")[r];a.innerHTML="",s&&s.events&&Array.isArray(s.events)?s.events.forEach(t=>{const m=document.createElement("div");m.classList.add("card");let b="",v="";t.Score1!==null&&t.Score1!==void 0&&t.Score1!==""&&(b=`<div class="chelsea">${t.Score1}</div>`),t.Score2!==null&&t.Score2!==void 0&&t.Score2!==""&&(v=`<div class="chelsea">${t.Score2}</div>`),m.innerHTML=`
                <div class="component-1">
                    <div class="d-flex justify-content-between ms-3 me-3 py-3">
                        <div class="live-wrapper">
                            <b class="live">${t.Status_Match}</b>
                        </div>
                        <span class="team-score">
                            <div class="star-container" onclick="toggleStar(this)">
                                <img src="/img/star_border.png" class="star default" alt="Star Border">
                                <img src="/img/star.png" class="star filled" alt="Star Filled">
                            </div>
                        </span>
                    </div>
                    <div class="frame-group">
                        <div class="chelsea-fcsvg-parent">
                            <div class="image-container">
                                <img src="${t.Team1.IMGTeam}" alt="${t.Team1.NMTeam}" />
                            </div>
                            <i class="vs">VS</i>
                            <div class="image-container">
                                <img src="${t.Team2.IMGTeam}" alt="${t.Team2.NMTeam}" />
                            </div>
                        </div>
                        <div class="frame-container">
                            <div class="nd-round-parent">
                                <i class="nd-round"></i>
                                <i class="nd-round"></i>
                            </div>
                            <div class="spacing-line"></div>
                        </div>
                        <div class="frame-div">
                            <div class="d-flex justify-content-between">
                                <div class="chelsea">${t.Team1.NMTeam}</div>
                                ${b}
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="chelsea">${t.Team2.NMTeam}</div>
                                ${v}
                            </div>
                            <img class="group-item" alt="" src="/img/Line 244.png">
                        </div>
                    </div>
                    <div class="button-primary">
                        ${t.has_live_url?`<b href="#" onclick="goToLive(event, '${t.IDMatch}')" class="button blinking-live">
                                LIVE NOW!!
                            </b>`:`<b class="button">
                                <img alt="" src="/img/calendar_card.png"> 
                                ${k(t.time_start)}
                            </b>`}
                    </div>
                </div>
            `,m.addEventListener("click",()=>{window.location.href=`/match/${d}/${t.IDMatch}`}),m.style.cursor="pointer",a.appendChild(m)}):console.log("No events data available.");const n=a,l=document.querySelectorAll("#prevBtn")[r],i=document.querySelectorAll("#nextBtn")[r],o=100;let c=!1,g,f;n.addEventListener("mousedown",t=>{t.preventDefault(),c=!0,g=t.pageX,f=n.scrollLeft,document.body.style.cursor="grabbing"}),n.addEventListener("mousemove",t=>{if(!c)return;t.preventDefault();const m=t.pageX-g,b=f-m;b<=0?n.scrollLeft=0:b>=n.scrollWidth-n.clientWidth?n.scrollLeft=n.scrollWidth-n.clientWidth:n.scrollLeft=b}),n.addEventListener("mouseup",()=>{c=!1,document.body.style.cursor="default"}),n.addEventListener("mouseleave",()=>{c=!1,document.body.style.cursor="default"}),l.addEventListener("click",()=>{n.scrollBy({left:-o,behavior:"smooth"})}),i.addEventListener("click",()=>{n.scrollBy({left:o,behavior:"smooth"})})}function Z(e){const s=parseInt(e.substring(0,4)),r=parseInt(e.substring(4,6))-1,d=parseInt(e.substring(6,8));return new Date(s,r,d)}function R(e=null){const s=e?Z(e):new Date,r=document.getElementById("calendar-grid");r.innerHTML="";const d=document.getElementById("calendar-month"),a={month:"long",year:"numeric"};d.textContent=s.toLocaleDateString("en-US",a);const n=[];for(let l=-3;l<=3;l++){const i=new Date;i.setDate(s.getDate()+l),n.push(i)}n.forEach((l,i)=>{const o=document.createElement("div");o.classList.add("calendar-card");const c=l.getFullYear(),g=(l.getMonth()+1).toString().padStart(2,"0"),f=l.getDate().toString().padStart(2,"0"),t=`${c}${g}${f}`;i===3&&o.classList.add("active");const m=l.toLocaleDateString("en-US",{weekday:"short"}).toUpperCase(),b=l.getDate();o.innerHTML=`
        <span>${m}</span>
        <span class="fw-normal">${b}</span>
      `,o.addEventListener("click",()=>{document.querySelectorAll(".calendar-card").forEach(v=>v.classList.remove("active")),o.classList.add("active"),A(t)}),r.appendChild(o)})}function k(e){let s=e.toString(),r=s.substring(0,4),d=s.substring(4,6),a=s.substring(6,8),n=s.substring(8,10),l=s.substring(10,12);return`${a} ${["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"][parseInt(d)-1]} ${r} - ${n}.${l}`}function ee(e){const s=new Date,r=new Date(e),d=Math.floor((s-r)/1e3),a={year:31536e3,month:2592e3,week:604800,day:86400,hour:3600,minute:60,second:1};for(let n in a){const l=Math.floor(d/a[n]);if(l>0)return`${l} ${n}${l!==1?"s":""} ago`}return"Just now"}
