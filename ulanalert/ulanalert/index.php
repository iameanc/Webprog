<?php require 'db.php'; require 'header.php'; $logged=!empty($_SESSION['user']); ?>
<header class="weather-heading"><h1>Weather in <span id="place">Calamba, Laguna</span></h1><div class="city-picker"><label for="city">Location</label><select id="city">
<option value="14.2117,121.1653">Calamba</option><option value="14.5995,120.9842">Manila</option>
<option value="14.6760,121.0437">Quezon City</option><option value="10.3157,123.8854">Cebu</option><option value="7.1907,125.4553">Davao</option></select></div></header>
<section class="advice"><span class="umbrella" aria-hidden="true">☂</span><div><strong>Should I bring an umbrella? <span id="verdict">Checking forecast...</span></strong><small id="why"></small></div></section>
<section class="forecast-panel">
 <div class="current-weather"><strong id="temp">--°C</strong><span class="weather-icon" id="weather-icon" aria-hidden="true">☁</span><span id="desc">Loading...</span></div>
 <small class="rain-summary" id="rain"></small>
 <h2>Next hours</h2><div class="row" id="hours"></div>
 <h2>7-day forecast</h2><div class="row" id="days"></div>
</section>

<h2>Community reports</h2>
<?php if($logged): ?><form id="rf" class="card"><input name="location" placeholder="Where? (e.g. Crossing, Calamba)" required maxlength="120">
<select name="severity"><option value="Light">Light rain</option><option value="Heavy">Heavy rain</option><option value="Flooded">Flooded</option></select>
<input name="notes" placeholder="Notes (optional)" maxlength="255"><button>Post report</button></form>
<?php else: ?><p><a href="login.php">Log in</a> to post or confirm reports.</p><?php endif; ?>
<div class="bar"><select id="sev"><option value="">All severities</option><option value="Light">Light rain</option><option value="Heavy">Heavy rain</option><option value="Flooded">Flooded</option></select></div>
<div id="feed" class="grid"></div></main>
<script>
const $=id=>document.getElementById(id);
const WMO=c=>c==0?'Clear':c<4?'Partly cloudy':c<50?'Foggy':c<60?'Drizzle':c<70?'Rainy':c<80?'Snow/ice':c<90?'Rain showers':'Thunderstorm';
async function forecast(){
 const [la,lo]=$('city').value.split(',');
 const u=`https://api.open-meteo.com/v1/forecast?latitude=${la}&longitude=${lo}&current=temperature_2m,precipitation,weather_code&hourly=precipitation_probability,precipitation&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max&timezone=Asia%2FManila&forecast_days=7`;
 try{
  const d=await (await fetch(u)).json(), cur=d.current;
  const city=$('city').options[$('city').selectedIndex].text;
  $('place').textContent=city==='Calamba'?'Calamba, Laguna':city;
  $('temp').textContent=Math.round(cur.temperature_2m)+'°C'; $('desc').textContent=WMO(cur.weather_code);
  $('weather-icon').textContent=cur.weather_code===0?'☀':cur.weather_code<4?'🌤':cur.weather_code<60?'☁':'🌧';
  const start=Math.max(0,d.hourly.time.findIndex(t=>t>=cur.time.slice(0,13)+':00'));
  const p=d.hourly.precipitation_probability.slice(start,start+6), mm=d.hourly.precipitation.slice(start,start+6);
  const maxP=Math.max(...p), sum=mm.reduce((a,b)=>a+b,0);
    $('rain').textContent=`Next 6 hours: up to ${maxP}% chance of rain, about ${sum.toFixed(1)} mm expected.`;
    let v,w; if(maxP>=60||sum>=2){v='Yes, bring an umbrella.';w='Rain is likely in the next few hours.'} else if(maxP>=30){v='Maybe; a compact umbrella could help.';w='There is some chance of showers.'} else {v='No umbrella needed.';w='Rain is unlikely in the next few hours.'}
    $('verdict').textContent=v; $('why').textContent=w;
    $('hours').innerHTML=p.map((x,i)=>`<div class="forecast-item hourly-item"><span>${new Date(d.hourly.time[start+i]).toLocaleTimeString('en',{hour:'numeric',hour12:true})}</span><span class="item-icon">${x>=50?'🌧':'🌤'}</span><b>${x}%</b><small>${mm[i]} mm</small></div>`).join('');
    $('days').innerHTML=d.daily.time.map((t,i)=>`<div class="forecast-item daily-item"><span>${new Date(t).toLocaleDateString('en',{weekday:'short',day:'numeric'})}</span><span class="item-icon">${d.daily.weather_code[i]>=60?'🌧':'🌤'}</span><b>${Math.round(d.daily.temperature_2m_max[i])}°/${Math.round(d.daily.temperature_2m_min[i])}°</b><small>${d.daily.precipitation_probability_max[i]}% rain</small></div>`).join('');
 }catch(e){$('desc').textContent='Could not load forecast. Check your internet connection.'}
}
async function feed(){$('feed').innerHTML=await (await fetch('feed.php?severity='+$('sev').value)).text()}
async function act(url,id){await fetch(url,{method:'POST',body:new URLSearchParams({id})});feed()}
$('city').onchange=forecast; $('sev').onchange=feed;
if($('rf'))$('rf').onsubmit=async e=>{e.preventDefault();await fetch('submit.php',{method:'POST',body:new FormData(e.target)});e.target.reset();feed()};
forecast();feed();setInterval(feed,30000);
</script></body></html>
