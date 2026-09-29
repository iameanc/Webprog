<?php require 'db.php'; require 'header.php'; ?>
<div class="bar"><input id="q" placeholder="Search items..."><select id="st"><option value="">All</option><option>lost</option><option>found</option></select></div>
<div id="grid" class="grid"></div></main>
<script>
const q=document.getElementById('q'),st=document.getElementById('st'),grid=document.getElementById('grid');
let t;async function load(){const r=await fetch('search.php?q='+encodeURIComponent(q.value)+'&status='+st.value);grid.innerHTML=await r.text();}
q.oninput=()=>{clearTimeout(t);t=setTimeout(load,250)};st.onchange=load;load();
</script></body></html>
