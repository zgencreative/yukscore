document.addEventListener("DOMContentLoaded",function(){if(typeof currentPage<"u"){_();const s=document.getElementById("loading-container"),m=document.getElementById("overlay");if(currentPage==="home"){let n=function(e){const i=parseInt(e.substring(0,4),10),a=parseInt(e.substring(4,6),10)-1,l=parseInt(e.substring(6,8),10),d=parseInt(e.substring(8,10),10),v=parseInt(e.substring(10,12),10),g=parseInt(e.substring(12,14),10);return new Date(i,a,l,d,v,g)};var t=n;W(),j();const r=document.querySelectorAll(".countdown");setInterval(()=>{const e=new Date().getTime();r.forEach(i=>{const a=i.dataset.starttime,d=n(a).getTime()-e;if(d<0){i.innerHTML="SEDANG BERLANGSUNG";return}const v=Math.floor(d/(1e3*60*60*24)),g=Math.floor(d%(1e3*60*60*24)/(1e3*60*60)),o=Math.floor(d%(1e3*60*60)/(1e3*60)),c=Math.floor(d%(1e3*60)/1e3);let p="";v>0&&(p+=v+"h "),(g>0||v>0)&&(p+=g+"j "),p+=o+"m "+c+"d",i.innerHTML=p})},1e3)}else if(currentPage==="match"){const r=window.location.pathname,n=r.split("/"),e=`${n[2]}.${n[3]}`,i=n[n.length-1],a={blsh:"Blocked Shot",catt:"Counter Attack",crs:"Corner Kicks",fls:"Fouls",gks:"Goalkeeper Saves",goa:"Goal kicks",ofs:"Offsides",pss:"Posession (%)",shof:"Shots Off Target",shon:"Shots On Target",ths:"Throw ins",ycs:"Yellow Cards",rcs:"Red Cards"};if(!i){console.error("ID not found in the URL!");return}const l=document.getElementById("summary-link");l.href=r;const d=[{id:"stats-link",endpoint:v=>`/api/football/detailMatch/stat/${v}`},{id:"lineups-link",endpoint:v=>`/api/football/detailMatch/lineup/${v}`},{id:"table-link",endpoint:v=>`/api/football/detailMatch/table/${e}`},{id:"news-link",endpoint:v=>`/api/football/detailMatch/news/${v}`},{id:"info-link",endpoint:v=>`/api/football/detailMatch/info/${v}`}];d.forEach(v=>{const g=document.getElementById(v.id);if(!g){console.warn(`Element with ID "${v.id}" not found.`);return}g.addEventListener("click",function(o){o.preventDefault(),s.style.display="block",m.style.display="block",l.classList.remove("active"),d.forEach(c=>{const p=document.getElementById(c.id);p&&p.classList.contains("active")&&p.classList.remove("active")}),g.classList.add("active"),fetch(v.endpoint(i),{method:"GET",headers:{"Content-Type":"application/json"}}).then(c=>{if(!c.ok)throw new Error(`HTTP error! status: ${c.status}`);return c.json()}).then(c=>{s.style.display="none",m.style.display="none";const p=document.getElementById("dinamic-content");if(p.innerHTML="",p.innerHTML=`<div class="info">
                      <div class="card bg-black border-2 border-secondary-subtle">
                        <div class="card-body">
                          <h4 class="text-center fw-semibold">
                            No Data
                          </h4>
                      </div>
                    </div>`,v.id=="stats-link"){const w=c.data[0];if(w){p.innerHTML="";for(const b in w.sum_stat[0]){const C=w.sum_stat[0][b]||0,$=w.sum_stat[1][b]||0,T=C+$;if(T>0){const S=Math.round(C/T*100),L=Math.round($/T*100),M=100-S,x=100-L,I=document.createElement("div");I.innerHTML+=`
                      <div class="d-flex justify-content-between py-2">
                        <span class="text-light">${C}</span>
                        <span class="stat-label">${a[b]||b}</span>
                        <span class="text-light">${$}</span>
                      </div>
                      <div class="d-flex justify-content-between">
                        <div class="pe-1 gap-1 d-flex justify-content-between w-50">
                          <div class="dot-white rounded-start" style="width: ${M}%;"></div>
                          <div class="dot-orange rounded-end" style="width: ${S}%;"></div>
                        </div>
                        <div class="ps-1 gap-1 d-flex justify-content-between w-50">
                          <div class="dot-orange rounded-start" style="width: ${L}%;"></div>
                          <div class="dot-white rounded-end" style="width: ${x}%;"></div>
                        </div>
                        </div>
                      </div>
                    `,p.appendChild(I)}}}}else if(v.id=="lineups-link"){if(c.data.MatchID){p.innerHTML="";const b={1:{x:[47],y:"5%"},2:{x:[26,66],y:"17%"},3:{x:[16,47,76],y:"25%"},4:{x:[11,31,61,81],y:"35%"},5:{x:[8,26,47,68,86],y:"42%"}},C={1:{x:[47],y:"91%"},2:{x:[26,66],y:"79%"},3:{x:[16,47,76],y:"71%"},4:{x:[11,31,61,81],y:"61%"},5:{x:[8,26,47,68,86],y:"54%"}},$=c.data.Team1,T=c.data.Team2,S=document.getElementsByClassName("team-nameMatch")[0].innerText,L=document.getElementsByClassName("team-nameMatch")[1].innerText,M=document.createElement("div");M.classList.add("field-container"),M.innerHTML=`
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
                    <b>${L}</b> ${T.Fo.join("-")}
                  </div>
                  `,p.appendChild(M),U($,b,"team1"),U(T,C,"team2"),z($,T),q($,T),V($,T),O($,T)}}else if(v.id=="table-link"){if(c.data){p.innerHTML="";const b=document.getElementsByClassName("team-nameMatch")[0].innerText,C=document.getElementsByClassName("team-nameMatch")[1].innerText,$=c.data.LeagueTable,T=document.createElement("div");T.style.margin="12px 0 12px",T.innerHTML=`
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
                `,p.appendChild(T);const S=document.getElementById("rankTable");$.forEach(L=>{const M=document.createElement("tr");M.classList.add("bg-dark-1"),L.teamNM===b||L.teamNM===C?M.classList.add("highlighted"):M.classList.remove("bg-dark-1"),M.style.fontSize="14px",M.innerHTML=`
                    <td style="font-weight: 500; width: 5%; color: white">${L.rank}</td>
                    <td style="width: 40%; color: white">
                      <img
                        loading="lazy"
                        src="${L.teamIMG}"
                        class="icon"
                        style="height: 30px; width: 30px"
                      />
                      ${L.teamNM}
                    </td>
                    <td class="col-p" style="width: 7.5%; color: white">${L.p}</td>
                    <td class="col-w" style="width: 7.5%; color: white">${L.w}</td>
                    <td class="col-d" style="width: 7.5%; color: white">${L.d}</td>
                    <td class="col-l" style="width: 7.5%; color: white">${L.l}</td>
                    <td class="col-f" style="width: 7.5%; color: white">${L.f}</td>
                    <td class="col-a" style="width: 7.5%; color: white">${L.a}</td>
                    <td class="col-gd" style="width: 7.5%; color: white">${L.gd}</td>
                    <td class="col-pts" style="width: 7.5%; color: white">${L.pts}</td>
                  `,S.appendChild(M)})}}else if(v.id=="news-link"){if(c.data.News){let T=function(L){const M=document.getElementById("btnTeam1"),x=document.getElementById("btnTeam2");b==="team1"?(M.classList.add("active"),x.classList.remove("active")):(M.classList.remove("active"),x.classList.add("active"))},S=function(){T(),$.innerHTML="";let L=c.data.News[0]["news-team"];const M=c.data.URL;b==="team1"&&(L=c.data.News[1]["news-team"]),L.forEach(x=>{const I=document.createElement("div");I.classList.add("py-2");const X=`${M}/${x.cover}`,N=`${M}news/${x.slug}`;I.innerHTML=`
                          <div class="news">
                            <div class="card bg-black mb-3" id="cardNEws">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                      <a class="title" href="${N}" target="_blank">
                                        <img src=" ${X||"https://placehold.co/600x400"}" class="img-fluid rounded-start"alt="${x.title}" loading="lazy" />
                                      </a>    
                                      </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                              <a class="title" href="${N}" target="_blank">
                                                ${x.title}
                                              </a>
                                            </h5>
                                            <p class="card-text">${x.description}</p>
                                            <p class="card-text text-end"><small class="">${se(x.updated_at)}</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                      `,I.querySelector(".card").addEventListener("click",()=>{window.open(N,"_blank")}),$.appendChild(I)})};var E=T,y=S;p.innerHTML="";let b="team1";if(!p){console.error("Container element 'dinamic-content' not found!");return}p.innerHTML="";const C=document.createElement("div");C.innerHTML=`
                <button class="button-matchesDetailTeam active" id="btnTeam1">
                  ${c.data.News[0]["name-team"]}
                </button>
                <button class="button-matchesDetailTeam" id="btnTeam2">${c.data.News[1]["name-team"]}</button>`,p.appendChild(C);const $=document.createElement("div");$.id="boxNews",p.appendChild($),document.getElementById("btnTeam1").addEventListener("click",()=>{b="team1",S()}),document.getElementById("btnTeam2").addEventListener("click",()=>{b="team2",S()}),S()}}else if(v.id=="info-link"){p.innerHTML="";const w=c.data;var u=w.stadium;u||(u="-");var f=w.time_start;f?f=H(w.time_start):f="-";var h=w.views;h?h=h.toLocaleString("id-ID"):h="-";let b=`
                <div class="col-4 col-md-4">
                  <div class="pt-2 d-flex flex-column align-items-center">
                    <div class="p-2 px-0 text-secondary" style="font-size: 11px;">
                      Select your team
                    </div>
                    <div class="p-2 bg-black border border- border-secondary rounded-3 draw" id="clickDiv" data-id="" data-match="${w.match.idMatch}">
                      <h6 class="fw-semibold">
                        Draw
                      </h6>
                    </div>
                  </div>
                </div>
                        `;w.score1!==""&&(b=`
                    <div class="col-4 col-md-4">
                      <div class="progress-container">
                        <div class="progress-labels">
                            <div class="progress-label">${w.vote.team1[1]}%</div>
                            <div class="progress-label">${w.vote.draw[1]}%</div>
                            <div class="progress-label">${w.vote.team2[1]}%</div>
                        </div>
                        <div class="progress bg-transparent border" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-orange" style="width: ${w.vote.team1[1]}%"></div>
                            <div class="progress-bar bg-secondary" style="width: ${w.vote.draw[1]}%"></div>
                            <div class="progress-bar bg-danger" style="width: ${w.vote.team2[1]}%"></div>
                        </div>
                      </div>
                      <div class="votes text-center ">
                        ${w.vote.total_vote} votes
                      </div>
                    </div>
                  `),p.innerHTML=`
                <div class="info">
                  <h6>
                    Match Info
                  </h6>
                    <div class="card bg-black border-2 border-secondary-subtle">
                      <div class="card-body">
                        <div class="d-flex flex-row justify-content-center flex-wrap">
                          <div class="p-2 gap-2 align-items-center">
                            <i class="fa-regular fa-calendar-days fa-xl"></i>
                            ${f}
                          </div>
                          <div class="p-2 gap-2 align-items-center">
                            <i class="fa-solid fa-monument fa-xl"></i> 
                            ${u}
                          </div>
                          <div class="p-2 gap-2 align-items-center">
                            <i class="fa-solid fa-users"></i>
                            ${h}
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
                              ${w.match.team1.NMTeam}
                            </div>
                            <div class="p-2 bg-black border border- border-secondary rounded-3" id="clickDiv" data-id="${w.match.team1.IDTeam}" data-match="${w.match.idMatch}">
                              <img class="" src="/${w.match.team1.IMGJersey}" width="50" alt="Default Team 1">
                            </div>
                          </div>
                        </div>
                        ${b}
                        <div class="col-4 col-md-4 p-0 p-md-2">
                          <div class="d-flex flex-column align-items-center">
                            <div class="p-2 team-name text-uppercase fw-semibold">
                              ${w.match.team2.NMTeam}
                            </div>
                            <div class="p-2 bg-black border border- border-secondary rounded-3" id="clickDiv" data-id="${w.match.team2.IDTeam}" data-match="${w.match.idMatch}">
                              <img class="" src="/${w.match.team2.IMGJersey}" width="50" alt="Default Team 1">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  `}else console.error("Container element not found!");document.addEventListener("click",function(w){const b=w.target.closest(".p-2.bg-black");if(b&&b.hasAttribute("data-id")&&b.hasAttribute("data-match")){const C=b.getAttribute("data-id"),$=b.getAttribute("data-match");fetch("/api/sendVote",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({id:C,match:$})}).then(S=>S.json()).then(S=>S.status===200?(s.style.display="none",m.style.display="none",Swal.fire({icon:"success",title:"Success Vote!"})):Swal.fire({icon:"error",title:"Terjadi Kesalahan!",text:S.message})).catch(S=>{console.error("Terjadi kesalahan:",S),alert("Terjadi kesalahan saat mengirimkan request.")})}})}).catch(c=>{console.error(`Error fetching ${v.endpoint(i)}:`,c)})})})}else if(currentPage==="comp"){const r=[{id:"overview-link"},{id:"matches-link"},{id:"table-link"}];P(data.data),r.forEach(n=>{const e=document.getElementById(n.id);if(!e){console.warn(`Element with ID "${n.id}" not found.`);return}e.addEventListener("click",function(i){i.preventDefault(),e.classList.remove("active"),r.forEach(a=>{const l=document.getElementById(a.id);l&&l.classList.contains("active")&&l.classList.remove("active")}),e.classList.add("active"),n.id=="overview-link"?P(data.data):n.id=="matches-link"?B(data.data.Events,"FIXTURES",data.data.urlComp):n.id=="table-link"?R(data.data.LeagueTable):console.error("404")})})}}});function G(){const t=document.getElementById("leftSide"),s=document.getElementById("new_search");window.innerWidth<=720?s.appendChild(t):document.getElementById("main-search").appendChild(t)}window.addEventListener("resize",G);window.addEventListener("load",G);function P(t){const s=document.getElementById("dinamic-content");s.innerHTML="";const m=document.createElement("div");m.classList.add("d-flex","justify-content-between"),m.innerHTML="<span>FIXTURES</span>",s.appendChild(m);const r=document.createElement("div");r.classList.add("grid-container"),s.appendChild(r);let n=0;for(let f=0;f<t.Events.length;f++)if(t.Events[f].Status_Match=="NS"&&n<=2){n++;const h=t.Events[f],E=h.Team1,y=h.Team2,w=H(h.time_start),b=document.createElement("div");b.classList.add("card"),b.innerHTML=`
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
                      <img class="chelsea-fcsvg-icon" alt="${E.NMTeam}" src="${E.IMGTeam}">
                      
                      <i class="vs">VS</i>
                      <img class="chelsea-fcsvg-icon" alt="${y.NMTeam}" src="${y.IMGTeam}">
                      
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
                        <div class="chelsea">${E.NMTeam}</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${y.NMTeam}</div>
                    </div>
                    <img class="group-item" alt="" src="/img/Line 244.png">
                  </div>
                </div>
                <div class="button-primary">
                    <b class="button"><img alt="" src="/img/calendar_card.png"> ${w}</b>
                </div>
              </div>
            `,b.addEventListener("click",()=>{window.location.href=`/match/${t.urlComp}/${h.IDMatch}`}),b.style.cursor="pointer",r.appendChild(b)}const e=document.createElement("div");e.classList.add("d-flex","justify-content-between"),e.innerHTML=`
        <div class="progress-dots">
          <div class="progress-bar-orange rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
        </div>
        <!-- Show All -->
        <p class="show-all" id="show-all-fix">Show All</p>
    `,s.appendChild(e),document.getElementById("show-all-fix").addEventListener("click",()=>{B(t.Events,"FIXTURES",t.urlComp)});const i=document.createElement("div");i.classList.add("d-flex","justify-content-between"),i.innerHTML="<span>RESULTS</span>",s.appendChild(i);const a=document.createElement("div");a.classList.add("grid-container"),s.appendChild(a);let l=0;for(let f=0;f<t.Events.length;f++)if(t.Events[f].Status_Match=="FT"&&l<=2){l++;const h=t.Events[f],E=h.Team1,y=h.Team2,w=H(h.time_start),b=document.createElement("div");b.classList.add("card"),b.innerHTML=`
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
                      <img class="chelsea-fcsvg-icon" alt="${E.NMTeam}" src="${E.IMGTeam}">
                      
                      <i class="vs">VS</i>
                      <img class="chelsea-fcsvg-icon" alt="${y.NMTeam}" src="${y.IMGTeam}">
                      
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
                        <div class="chelsea">${E.NMTeam}</div>
                        <span class="team-score">${h.Score1}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${y.NMTeam}</div>
                        <span class="team-score">${h.Score2}</span>
                    </div>
                    <img class="group-item" alt="" src="/img/Line 244.png">
                  </div>
                </div>
                <div class="button-primary">
                    <b class="button"><img alt="" src="/img/calendar_card.png"> ${w}</b>
                </div>
              </div>
            `,b.addEventListener("click",()=>{window.location.href=`/match/${t.urlComp}/${h.IDMatch}`}),b.style.cursor="pointer",a.appendChild(b)}const d=document.createElement("div");d.classList.add("d-flex","justify-content-between"),d.innerHTML=`
        <div class="progress-dots">
          <div class="progress-bar-orange rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
        </div>
        <!-- Show All -->
        <p class="show-all" id="show-all-res">Show All</p>
    `,s.appendChild(d),document.getElementById("show-all-res").addEventListener("click",()=>{B(t.Events,"RESULTS",t.urlComp)});const v=document.createElement("div");v.classList.add("d-flex","justify-content-between"),v.innerHTML="<span>TABLE</span>",s.appendChild(v);const g=document.createElement("div");g.style.margin="12px 0 12px",g.innerHTML=`
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
    `,s.appendChild(g);const o=document.getElementById("rankTable"),c=t.LeagueTable;let p=0;c.forEach(f=>{if(p<=4){const h=document.createElement("tr");h.style.fontSize="14px",h.innerHTML=`
        <td style="font-weight: 500; width: 5%; color: white">${f.rank}</td>
        <td style="width: 40%; color: white">
                <img
                  loading="lazy"
                  src="${f.teamIMG}"
                  class="icon"
                  style="height: 30px; width: 30px"
                />
                ${f.teamNM}
              </td>
              <td style="width: 7.5%; color: white">${f.p}</td>
              <td style="width: 7.5%; color: white">${f.w}</td>
              <td style="width: 7.5%; color: white">${f.d}</td>
              <td style="width: 7.5%; color: white">${f.l}</td>
              <td style="width: 7.5%; color: white">${f.f}</td>
              <td style="width: 7.5%; color: white">${f.a}</td>
              <td style="width: 7.5%; color: white">${f.gd}</td>
              <td style="width: 7.5%; color: white">${f.pts}</td>
        `,o.appendChild(h)}p++});const u=document.createElement("div");u.classList.add("d-flex","justify-content-between"),u.innerHTML=`
        <div class="progress-dots">
          <div class="progress-bar-orange rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
          <div class="dot rounded"></div>
        </div>
        <!-- Show All -->
        <p class="show-all" id="show-all-rank">Show All</p>
    `,s.appendChild(u),document.getElementById("show-all-rank").addEventListener("click",()=>{R(t.LeagueTable)})}function B(t,s,m){const r=document.getElementById("dinamic-content");r.innerHTML="";for(var n=document.getElementById("matches-link"),e=document.getElementsByClassName("menu-link"),i=0;i<e.length;i++)e[i].classList.remove("active");n.classList.add("active");const a=document.createElement("div");a.classList.add("button-group");const l=document.createElement("button");l.classList.add("button-matchesDetailTeam"),l.textContent="FIXTURES";const d=document.createElement("button");d.classList.add("button-matchesDetailTeam","active"),d.textContent="RESULTS";let v="FT";s=="FIXTURES"&&(v="NS",l.classList.add("active"),d.classList.remove("active")),a.appendChild(l),l.addEventListener("click",()=>{B(t,"FIXTURES",m)}),a.appendChild(d),d.addEventListener("click",()=>{B(t,"RESULTS",m)}),r.appendChild(a);const g=document.createElement("div");g.classList.add("grid-container"),r.appendChild(g);for(let o=0;o<t.length;o++){const c=t[o];let p="",u="";if(c.Score1!==null&&c.Score1!==void 0&&c.Score1!==""&&(p=`<span class="team-score">${c.Score1}</span>`),c.Score2!==null&&c.Score2!==void 0&&c.Score2!==""&&(u=`<span class="team-score">${c.Score2}</span>`),t[o].Status_Match==v){const f=c.Team1,h=c.Team2,E=H(c.time_start),y=document.createElement("div");y.classList.add("card"),y.innerHTML=`
                <div class="component-1">
                <div class="d-flex justify-content-between ms-3 me-3 py-3">
                  <div class="live-wrapper">
                      <b class="live">${c.Status_Match}</b>
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
                      <img class="chelsea-fcsvg-icon" alt="${f.NMTeam}" src="${f.IMGTeam}">
                      
                      <i class="vs">VS</i>
                      <img class="chelsea-fcsvg-icon" alt="${h.NMTeam}" src="${h.IMGTeam}">
                      
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
                        <div class="chelsea">${f.NMTeam}</div>
                        ${p}
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${h.NMTeam}</div>
                        ${u}
                    </div>
                    <img class="group-item" alt="" src="/img/Line 244.png">
                  </div>
                </div>
                <div class="button-primary">
                    <b class="button"><img alt="" src="/img/calendar_card.png"> ${E}</b>
                </div>
              </div>
            `,y.addEventListener("click",()=>{window.location.href=`/match/${m}/${c.IDMatch}`}),y.style.cursor="pointer",g.appendChild(y)}}}function R(t){const s=document.getElementById("dinamic-content");s.innerHTML="";for(var m=document.getElementById("table-link"),r=document.getElementsByClassName("menu-link"),n=0;n<r.length;n++)r[n].classList.remove("active");m.classList.add("active");const e=document.createElement("div");e.style.margin="12px 0 12px",e.innerHTML=`
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
    `,s.appendChild(e);const i=document.getElementById("rankTable");t.forEach(l=>{const d=document.createElement("tr");d.style.fontSize="14px",d.innerHTML=`
        <td style="font-weight: 500; width: 5%; color: white">${l.rank}</td>
       <td style="width: 40%; color: white">
                <img
                  loading="lazy"
                  src="${l.teamIMG}"
                  class="icon"
                  style="height: 30px; width: 30px"
                />
                ${l.teamNM}
              </td>
              <td style="width: 7.5%; color: white">${l.p}</td>
              <td style="width: 7.5%; color: white">${l.w}</td>
              <td style="width: 7.5%; color: white">${l.d}</td>
              <td style="width: 7.5%; color: white">${l.l}</td>
              <td style="width: 7.5%; color: white">${l.f}</td>
              <td style="width: 7.5%; color: white">${l.a}</td>
              <td style="width: 7.5%; color: white">${l.gd}</td>
              <td style="width: 7.5%; color: white">${l.pts}</td>
        `,i.appendChild(d)})}document.querySelectorAll(".player-name").forEach(t=>{const s=t.getAttribute("data-name"),m=10;if(s.length>m){const r=s.split(" "),n=r.length>1?`${r[0]} ${r[1][0]}.`:s.slice(0,m-1)+".";t.textContent=n}else t.textContent=s});function O(t,s){const m=document.getElementById("dinamic-content"),r=document.createElement("div");r.classList.add("py-2"),r.innerHTML=`
      <span class="fw-10">COACHES</span>
    `,m.appendChild(r);const n=document.createElement("div");n.classList.add("border","rounded");for(let e=0;e<t.Pos.length;e++)if(t.Pos[e].Pon=="COACH"){for(let i=0;i<s.Pos.length;i++)if(s.Pos[i].Pon=="COACH"){const a=document.createElement("div");a.classList.add("substitution","d-flex","justify-content-between","align-items-center"),a.innerHTML=`
              <div class="subs">
                <span class="px-2">${t.Pos[e].Fn} ${t.Pos[e].Ln}</span>
              </div>

              <div class="subs">
                <span class="px-2">${s.Pos[i].Fn} ${s.Pos[i].Ln}</span>
              </div>
            `,n.appendChild(a)}}n.children.length>0&&r.appendChild(n)}function V(t,s){var m=t.IS.length;const r=document.getElementById("dinamic-content");t.IS.length<s.IS.length&&(m=s.IS.length);const n=document.createElement("div");n.classList.add("py-2"),n.innerHTML=`
      <span class="fw-10">INJURIES & SUSPENSIONS</span>
    `,r.appendChild(n);const e=document.createElement("div");e.classList.add("border","rounded");for(let i=0;i<m;i+=1){const a=t.IS[i],l=s.IS[i];if(a||l){const d=document.createElement("div");d.classList.add("substitution","d-flex","justify-content-between","align-items-center"),a?d.innerHTML+=`
          <div class="subs">
            <div>
              <div class="out"><b>${a.Fn} ${a.Ln}</b></div>
              <div class="in">${a.Rs}</div>
            </div>
          </div>
        `:d.innerHTML+=`
          <div class="subs">
            <div>
              <div class="out"><b></b></div>
              <div class="in"></div>
            </div>
          </div>
        `,l?d.innerHTML+=`
          <div class="subs">
            <div>
              <div class="out"><b>${l.Fn} ${l.Ln}</b></div>
              <div class="in">${l.Rs}</div>
            </div>
          </div>
        `:d.innerHTML+=`
          <div class="subs">
            <div>
              <div class="out"><b></b></div>
              <div class="in"></div>
            </div>
          </div>
        `,e.appendChild(d)}}e.children.length>0&&n.appendChild(e)}function q(t,s){var m=t.Pos.length;const r=document.getElementById("dinamic-content");t.Pos.length<s.Pos.length&&(m=s.Pos.length);const n=document.createElement("div");n.classList.add("py-2"),n.innerHTML=`
      <span class="fw-10">SUBSTITUTE PLAYERS</span>
    `,r.appendChild(n);const e=document.createElement("div");e.classList.add("border","rounded");for(let i=0;i<m;i+=1){const a=t.Pos[i],l=s.Pos[i];if(a&&a.Pon=="SUBSTITUTE_PLAYER"||l&&l.Pon=="SUBSTITUTE_PLAYER"){const d=document.createElement("div");d.classList.add("substitution","d-flex","justify-content-between","align-items-center"),a&&(d.innerHTML+=`
          <div class="subs">
            <div class="subs-time border rounded-circle">${a.Np}</div>
            <div>
              <div>
                <span class="px-2">${a.Fn} ${a.Ln}</span>
              </div>
            </div>
          </div>
        `),l&&(d.innerHTML+=`
          <div class="subs">
            <div class="subs-time border rounded-circle">${a.Np}</div>
            <div>
              <span class="px-2">${l.Fn} ${l.Ln}</span>
            </div>
          </div>
        `),e.appendChild(d)}}e.children.length>0&&n.appendChild(e)}function z(t,s){var m=t.Subs.length;const r=document.getElementById("dinamic-content");t.Subs.length<s.Subs.length&&(m=s.Subs.length);const n=document.createElement("div");n.classList.add("py-2"),n.innerHTML=`
      <span class="fw-10">SUBSTITUTION</span>
    `,r.appendChild(n);const e=document.createElement("div");e.classList.add("border","rounded");for(let i=0;i<m;i+=2){const a=t.Subs[i],l=t.Subs[i+1],d=s.Subs[i],v=s.Subs[i+1];if(a||d){const g=document.createElement("div");g.classList.add("substitution","d-flex","justify-content-between","align-items-center"),a&&(g.innerHTML+=`
          <div class="subs">
            <div class="subs-time">${a.Min}'</div>
            <div>
              <div class="out">
                <i class="fas fa-arrow-alt-circle-down text-danger"></i>
                ${a.Pn}
              </div>
              ${l?`<div class="in">
                      <i class="fas fa-arrow-alt-circle-up text-warning"></i>
                      <b>${l.Pn}</b>
                    </div>`:""}
            </div>
          </div>
        `),d&&(g.innerHTML+=`
          <div class="subs">
            <div class="subs-time">${d.Min}'</div>
            <div>
              <div class="out">
                <i class="fas fa-arrow-alt-circle-down text-danger"></i>
                ${d.Pn}
              </div>
              ${v?`<div class="in">
                      <i class="fas fa-arrow-alt-circle-up text-warning"></i>
                      <b>${v.Pn}</b>
                    </div>`:""}
            </div>
          </div>
        `),e.appendChild(g)}}e.children.length>0&&n.appendChild(e)}function U(t,s,m){const r=document.querySelector(".field-container"),n=document.getElementById("dinamic-content");t.Pos.forEach(e=>{if(e.Fp&&e.Fp!="1:1"){const[i,a]=e.Fp.split(":").map(Number),l=t.Fo[i-2],v=s[l].x[a-1],g=s[i].y,o=document.createElement("div");o.className=`player ${m}`,o.style.top=g,o.style.left=`${v}%`,o.textContent=e.Np;const c=document.createElement("span");c.className="player-name";const p=`${e.Fn} ${e.Ln}`,u=10;if(c.textContent=p,p.length>u){const f=p.split(" "),h=f.length>1?`${f[0]} ${f[1][0]}.`:p.slice(0,u-1)+".";c.textContent=h}o.appendChild(c),r.appendChild(o)}else if(e.Fp&&e.Fp=="1:1"){const i=document.createElement("div");i.className=`player ${m}`,m=="team1"?i.style.top="5%":i.style.top="91%",i.style.left="47%",i.textContent=e.Np;const a=document.createElement("span");a.className="player-name";const l=`${e.Fn} ${e.Ln}`,d=10;if(a.textContent=l,l.length>d){const v=l.split(" "),g=v.length>1?`${v[0]} ${v[1][0]}.`:l.slice(0,d-1)+".";a.textContent=g}i.appendChild(a),r.appendChild(i)}}),n.appendChild(r)}async function _(){try{const t=(o,c)=>{let p;return(...u)=>{clearTimeout(p),p=setTimeout(()=>o(...u),c)}},m=await(await fetch("/api/search",{method:"GET",headers:{"Content-Type":"application/json"}})).json(),r=document.getElementById("leftSide");r.innerHTML="";const n=document.createElement("div");n.classList.add("mb-4"),n.innerHTML=`
        <input
          type="text"
          class="form-control"
          placeholder="Search..."
          id="searchInput"
        />
      `,r.appendChild(n);const e=document.createElement("div");e.classList.add("mb-4"),e.innerHTML='<h6 class="text-uppercase">Region</h6>';const i=document.createElement("ul");i.classList.add("list-unstyled"),i.id="region",m.data.categories.forEach(o=>{const c=document.createElement("li");c.classList.add("d-flex","align-items-center","mb-3","p-2"),c.id="leftCard",c.setAttribute("onclick",`detailCountry('${o.Ccd}', '${o.Cnm}')`),c.innerHTML=`
          <img
            src="${o.badgeUrl||"default-region.png"}"
            alt="${o.Ccd}"
            class="me-3"
            style="width: 20px; object-fit: cover"
          />
          <div class="d-flex flex-column">
            <span class="text-truncate">${o.Cnm}</span>
            <small class="text-light text-truncate"></small>
          </div>
        `,i.appendChild(c)}),e.appendChild(i),r.appendChild(e);const a=document.createElement("div");a.innerHTML='<h6 class="text-uppercase">Teams</h6>';const l=document.createElement("ul");l.classList.add("list-unstyled"),l.id="teams",m.data.teams.forEach(o=>{const c=document.createElement("li");c.classList.add("d-flex","align-items-center","mb-3","p-2"),c.id="leftCard",c.innerHTML=`
          <img
            src="${o.IMGTeam||"default-team.png"}"
            alt="${o.NMTeam}"
            class="me-3"
            style="width: 20px; object-fit: cover"
          />
          <div class="d-flex flex-column">
            <span class="text-truncate">${o.NMTeam}</span>
            <small class="text-light text-truncate">${o.CoNm}</small>
          </div>
        `,c.addEventListener("click",()=>{window.location.href=`/team/${o.IDTeam}/`}),l.appendChild(c)}),a.appendChild(l),r.appendChild(a);const d=document.createElement("div");d.classList.add("mb-4"),d.innerHTML='<h6 class="text-uppercase">Competition</h6>';const v=document.createElement("ul");v.classList.add("list-unstyled"),v.id="competition",m.data.stages.forEach(o=>{const c=document.createElement("li");c.classList.add("d-flex","align-items-center","mb-3","p-2"),c.id="leftCard",c.innerHTML=`
          <img
            src="${o.badgeUrl||"default-competition.png"}"
            alt="${o.Scd}"
            class="me-3"
            style="width: 20px; object-fit: cover"
          />
          <div class="d-flex flex-column">
            <span class="text-truncate">${o.Snm}</span>
            <small class="text-light text-truncate">${o.Cnm}</small>
          </div>
        `,c.addEventListener("click",()=>{var p=o.urlComp.replace(".","/");window.location.href=`/comp/${p}/`}),v.appendChild(c)}),d.appendChild(v),r.appendChild(d),document.getElementById("searchInput").addEventListener("input",t(async o=>{const c=o.target.value.trim();c?await J(c):_()},300))}catch(t){console.error("Error fetching data:",t)}}window.reloadInitialView=_;const J=async t=>{try{const m=await(await fetch(`/api/search/${t}`)).json(),r=document.getElementById("region"),n=document.getElementById("teams"),e=document.getElementById("competition");r.innerHTML="",n.innerHTML="",e.innerHTML="",A(r,m.data.categories,Y),A(n,m.data.teams,K),A(e,m.data.stages,Q)}catch(s){console.error("Error in performSearch:",s)}},A=(t,s,m)=>{s&&s.length?s.forEach(r=>t.appendChild(m(r))):t.innerHTML="<li class='text-muted'>No results found</li>"},Y=t=>{const s=document.createElement("li");return s.classList.add("d-flex","align-items-center","mb-3","p-2"),s.id="leftCard",s.setAttribute("onclick",`detailCountry('${t.Ccd}', '${t.Cnm}')`),s.innerHTML=`
      <img
        src="${t.badgeUrl||"default-region.png"}"
        alt="${t.Ccd}"
        class="me-3"
        style="width: 20px; object-fit: cover"
      />
      <div class="d-flex flex-column">
        <span class="text-truncate">${t.Cnm}</span>
      </div>
    `,s},K=t=>{const s=document.createElement("li");return s.classList.add("d-flex","align-items-center","mb-3","p-2"),s.id="leftCard",s.innerHTML=`
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
    `,s.addEventListener("click",()=>{window.location.href=`/team/${t.IDTeam}/`}),s},Q=t=>{const s=document.createElement("li");return s.classList.add("d-flex","align-items-center","mb-3","p-2"),s.id="leftCard",s.innerHTML=`
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
    `,s.addEventListener("click",()=>{var m=t.urlComp.replace(".","/");window.location.href=`/comp/${m}/`}),s};async function Z(t,s){try{const r=await(await fetch(`/api/football/detailCountry/${t}`)).json(),n=document.getElementById("leftSide");n.innerHTML="";const e=r.data;if(e&&e.length>0){const i=document.createElement("div");i.classList.add("mb-4"),i.innerHTML=`
          <button
            id="back-button"
            class="btn text-white d-flex align-items-center"
            onclick="reloadInitialView()"
          >
            <i class="fas fa-arrow-left"></i>
            <span id="header-title" class="ms-2">${s}</span>
          </button>`,n.appendChild(i);const a=document.createElement("ul");a.classList.add("list-unstyled"),e.forEach(l=>{const d=document.createElement("li");d.classList.add("d-flex","align-items-center","mb-3","p-2"),d.id="leftCard",d.innerHTML=`
            <img
              src="${l.badgeUrl||"default-badge.png"}"
              alt="${l.Scd}"
              class="me-3"
              style="width: 20px; object-fit: cover"
            />
            <div class="d-flex flex-column">
              <span>${l.Snm}</span>
              <small class="text-light"></small>
            </div>
          `,d.addEventListener("click",()=>{var v=l.urlComp.replace(".","/");window.location.href=`/comp/${v}/`}),a.appendChild(d)}),n.appendChild(a)}}catch(m){console.error("Error fetching country details:",m)}}window.detailCountry=Z;const ee=document.getElementById("kt_datepicker_1");ee.flatpickr({dateFormat:"Ymd",disableMobile:"true",onChange:function(t,s,m){j(s),W(s)}});async function j(t=null){const s=t?`/api/sorted-data/${t}`:"/api/sorted-data",m=document.getElementsByClassName("scrollable-container");Array.from(m).forEach(r=>{r.innerHTML=""});try{const n=await(await fetch(s,{method:"GET",headers:{"Content-Type":"application/json"}})).json(),e=document.getElementById("live");e.innerHTML="";const i=n.live;document.getElementById("show-all-live").addEventListener("click",function(){D(i,"LIVE")}),i&&i.length>0&&i.forEach((u,f)=>{const h=document.createElement("button");h.classList.add("circle-button"),f===0&&(h.classList.add("active"),k("live",u,0,u.urlComp)),h.innerHTML=`
              <div class="icon">
                  <img src="${u.badgeUrl||"default-icon.png"}" alt="${u.Scd}" />
              </div>
              <span>${u.Snm}</span>
          `,h.addEventListener("click",()=>{const E=e.getElementsByClassName("circle-button");Array.from(E).forEach(y=>y.classList.remove("active")),h.classList.add("active"),k("live",u,0,u.urlComp)}),e.appendChild(h)});const l=document.getElementById("next");l.innerHTML="";const d=n.next;document.getElementById("show-all-upcoming").addEventListener("click",function(){D(d,"UPCOMING")}),d&&d.length>0&&d.forEach((u,f)=>{const h=document.createElement("button");h.classList.add("circle-button"),f===0&&(h.classList.add("active"),k("next",u,1,u.urlComp)),h.innerHTML=`
              <div class="icon">
                  <img src="${u.badgeUrl||"default-icon.png"}" alt="${u.Scd}" />
              </div>
              <span>${u.Snm}</span>
          `,h.addEventListener("click",()=>{const E=l.getElementsByClassName("circle-button");Array.from(E).forEach(y=>y.classList.remove("active")),h.classList.add("active"),k("next",u,1,u.urlComp)}),l.appendChild(h)});const g=document.getElementById("previous");g.innerHTML="";const o=n.previous;document.getElementById("show-all-previous").addEventListener("click",function(){D(o,"PREVIOUS")}),o&&o.length>0&&o.forEach((u,f)=>{const h=document.createElement("button");h.innerHTML="",h.classList.add("circle-button"),f===0&&(h.classList.add("active"),k("prev",u,2,u.urlComp)),h.innerHTML=`
              <div class="icon">
                  <img src="${u.badgeUrl||"default-icon.png"}" alt="${u.Scd}" />
              </div>
              <span class="title">${u.Snm}</span>
          `,h.addEventListener("click",()=>{const E=g.getElementsByClassName("circle-button");Array.from(E).forEach(y=>y.classList.remove("active")),h.classList.add("active"),k("prev",u,2,u.urlComp)}),g.appendChild(h)}),document.querySelectorAll(".button-container").forEach(u=>{let f=!1,h,E;u.addEventListener("mousedown",y=>{y.preventDefault(),f=!0,h=y.pageX,E=u.scrollLeft,document.body.style.cursor="grabbing"}),u.addEventListener("mousemove",y=>{if(!f)return;y.preventDefault();const w=y.pageX-h,b=E-w;b<=0?u.scrollLeft=0:b>=u.scrollWidth-u.clientWidth?u.scrollLeft=u.scrollWidth-u.clientWidth:u.scrollLeft=b}),u.addEventListener("mouseup",()=>{f=!1,document.body.style.cursor="default"}),u.addEventListener("mouseleave",()=>{f=!1,document.body.style.cursor="default"})})}catch(r){console.error("Error fetching data:",r)}}function D(t,s){const m=document.getElementById("main-div"),r=document.getElementById("right-content");r.innerHTML="",m.innerHTML="",m.className="col-md-8";const n=document.createElement("div");n.classList.add("p-3","rounded","shadow-sm","text-light"),n.innerHTML=`
      <p>
        <a href="/" style="text-decoration: none;">
          <i class="fas fa-arrow-left" style="color: white;"></i>
        </a> 
        <b>${s}</b> MATCHES
      </p>
      <div class="button-container" id="button-container"></div>

      <div class="grid-container"></div>
    `,m.appendChild(n);const e=document.getElementById("button-container");t&&t.length>0?t.forEach((a,l)=>{const d=document.createElement("button");d.classList.add("circle-button"),l===0&&(d.classList.add("active"),F(a,0,a.urlComp)),d.innerHTML=`
          <div class="icon">
            <img src="${a.badgeUrl||"default-icon.png"}" alt="${a.Scd}" />
          </div>
          <span>${a.Snm}</span>
        `,d.addEventListener("click",()=>{const v=e.getElementsByClassName("circle-button");Array.from(v).forEach(g=>g.classList.remove("active")),d.classList.add("active"),F(a,0,a.urlComp)}),e.appendChild(d)}):e.innerHTML='<p class="text-center text-muted">No matches available.</p>',document.querySelectorAll(".button-container").forEach(a=>{let l=!1,d,v;a.addEventListener("mousedown",g=>{g.preventDefault(),l=!0,d=g.pageX,v=a.scrollLeft,document.body.style.cursor="grabbing"}),a.addEventListener("mousemove",g=>{if(!l)return;g.preventDefault();const o=g.pageX-d,c=v-o;c<=0?a.scrollLeft=0:c>=a.scrollWidth-a.clientWidth?a.scrollLeft=a.scrollWidth-a.clientWidth:a.scrollLeft=c}),a.addEventListener("mouseup",()=>{l=!1,document.body.style.cursor="default"}),a.addEventListener("mouseleave",()=>{l=!1,document.body.style.cursor="default"})})}function F(t,s,m){const r=document.getElementsByClassName("grid-container")[s];r.innerHTML="",t&&t.events&&Array.isArray(t.events)?t.events.forEach(n=>{const e=document.createElement("div");e.classList.add("card");let i="",a="";n.Score1!==null&&n.Score1!==void 0&&n.Score1!==""&&(i=`<div class="chelsea">${n.Score1}</div>`),n.Score2!==null&&n.Score2!==void 0&&n.Score2!==""&&(a=`<div class="chelsea">${n.Score2}</div>`),e.innerHTML=`
              <div class="component-1">
                <div class="d-flex justify-content-between ms-3 me-3 py-3">
                  <div class="live-wrapper">
                      <b class="live">${n.Status_Match}</b>
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
                      <img class="chelsea-fcsvg-icon" alt="${n.Team1.NMTeam}" src="${n.Team1.IMGTeam}">
                      
                      <i class="vs">VS</i>
                      <img class="chelsea-fcsvg-icon" alt="${n.Team2.NMTeam}" src="${n.Team2.IMGTeam}">
                      
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
                        <div class="chelsea">${n.Team1.NMTeam}</div>
                        ${i}
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="chelsea">${n.Team2.NMTeam}</div>
                        ${a}
                    </div>
                    <img class="group-item" alt="" src="/img/Line 244.png">
                  </div>
                </div>
                <div class="button-primary">
                    <b class="button"><img alt="" src="/img/calendar_card.png"> ${H(n.time_start)}</b>
                </div>
              </div>
          `,e.addEventListener("click",()=>{window.location.href=`/match/${m}/${n.IDMatch}`}),e.style.cursor="pointer",r.appendChild(e)}):console.log("No events data available.")}function k(t,s,m,r){const n=document.getElementsByClassName("scrollable-container")[m];n.innerHTML="",s&&s.events&&Array.isArray(s.events)?s.events.forEach(o=>{const c=document.createElement("div");c.classList.add("card");let p="",u="";o.Score1!==null&&o.Score1!==void 0&&o.Score1!==""&&(p=`<div class="chelsea">${o.Score1}</div>`),o.Score2!==null&&o.Score2!==void 0&&o.Score2!==""&&(u=`<div class="chelsea">${o.Score2}</div>`),c.innerHTML=`
                <div class="component-1">
                    <div class="d-flex justify-content-between ms-3 me-3 py-3">
                        <div class="live-wrapper">
                            <b class="live">${o.Status_Match}</b>
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
                                <img src="${o.Team1.IMGTeam}" alt="${o.Team1.NMTeam}" />
                            </div>
                            <i class="vs">VS</i>
                            <div class="image-container">
                                <img src="${o.Team2.IMGTeam}" alt="${o.Team2.NMTeam}" />
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
                                <div class="chelsea">${o.Team1.NMTeam}</div>
                                ${p}
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="chelsea">${o.Team2.NMTeam}</div>
                                ${u}
                            </div>
                            <img class="group-item" alt="" src="/img/Line 244.png">
                        </div>
                    </div>
                    <div class="button-primary">
                        <b class="button">
                            <img alt="" src="/img/calendar_card.png"> 
                            ${H(o.time_start)}
                        </b>
                    </div>
                </div>
            `,c.addEventListener("click",()=>{window.location.href=`/match/${r}/${o.IDMatch}`}),c.style.cursor="pointer",n.appendChild(c)}):console.log("No events data available.");const e=n,i=document.querySelectorAll("#prevBtn")[m],a=document.querySelectorAll("#nextBtn")[m],l=100;let d=!1,v,g;e.addEventListener("mousedown",o=>{o.preventDefault(),d=!0,v=o.pageX,g=e.scrollLeft,document.body.style.cursor="grabbing"}),e.addEventListener("mousemove",o=>{if(!d)return;o.preventDefault();const c=o.pageX-v,p=g-c;p<=0?e.scrollLeft=0:p>=e.scrollWidth-e.clientWidth?e.scrollLeft=e.scrollWidth-e.clientWidth:e.scrollLeft=p}),e.addEventListener("mouseup",()=>{d=!1,document.body.style.cursor="default"}),e.addEventListener("mouseleave",()=>{d=!1,document.body.style.cursor="default"}),i.addEventListener("click",()=>{e.scrollBy({left:-l,behavior:"smooth"})}),a.addEventListener("click",()=>{e.scrollBy({left:l,behavior:"smooth"})})}function te(t){const s=parseInt(t.substring(0,4)),m=parseInt(t.substring(4,6))-1,r=parseInt(t.substring(6,8));return new Date(s,m,r)}function W(t=null){const s=t?te(t):new Date,m=document.getElementById("calendar-grid");m.innerHTML="";const r=document.getElementById("calendar-month"),n={month:"long",year:"numeric"};r.textContent=s.toLocaleDateString("en-US",n);const e=[];for(let i=-3;i<=3;i++){const a=new Date;a.setDate(s.getDate()+i),e.push(a)}e.forEach((i,a)=>{const l=document.createElement("div");l.classList.add("calendar-card");const d=i.getFullYear(),v=(i.getMonth()+1).toString().padStart(2,"0"),g=i.getDate().toString().padStart(2,"0"),o=`${d}${v}${g}`;a===3&&l.classList.add("active");const c=i.toLocaleDateString("en-US",{weekday:"short"}).toUpperCase(),p=i.getDate();l.innerHTML=`
        <span>${c}</span>
        <span class="fw-normal">${p}</span>
      `,l.addEventListener("click",()=>{document.querySelectorAll(".calendar-card").forEach(u=>u.classList.remove("active")),l.classList.add("active"),j(o)}),m.appendChild(l)})}function H(t){let s=t.toString(),m=s.substring(0,4),r=s.substring(4,6),n=s.substring(6,8),e=s.substring(8,10),i=s.substring(10,12);return`${n} ${["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"][parseInt(r)-1]} ${m} - ${e}.${i}`}function se(t){const s=new Date,m=new Date(t),r=Math.floor((s-m)/1e3),n={year:31536e3,month:2592e3,week:604800,day:86400,hour:3600,minute:60,second:1};for(let e in n){const i=Math.floor(r/n[e]);if(i>0)return`${i} ${e}${i!==1?"s":""} ago`}return"Just now"}function ne(){Swal.fire({title:"Informasi Pendaftaran",text:"Silahkan Daftar Melalui Yuksports. Anda akan diarahkan dalam 5 detik.",icon:"info",timer:5e3,timerProgressBar:!0,allowOutsideClick:!1,didOpen:()=>{Swal.showLoading()},willClose:()=>{window.location.href="https://yuksports.com/"}})}window.showSignupRedirectAlert=ne;
