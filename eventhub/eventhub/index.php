<?php require 'db.php'; require 'header.php'; ?>
<div class="bar"><input id="q" placeholder="Search events..."><input id="d" type="date"></div>
<div id="grid" class="grid"></div></main>
<script>
const q=document.getElementById('q'),d=document.getElementById('d'),grid=document.getElementById('grid');
let t;async function load(){grid.innerHTML=await (await fetch('search.php?q='+encodeURIComponent(q.value)+'&date='+d.value)).text()}
q.oninput=()=>{clearTimeout(t);t=setTimeout(load,250)};d.onchange=load;load();
</script></body></html>
