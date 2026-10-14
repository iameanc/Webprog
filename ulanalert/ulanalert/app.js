const $=id=>document.getElementById(id);
const WMO=c=>c==0?'Clear':c<4?'Partly cloudy':c<50?'Foggy':c<60?'Drizzle':c<70?'Rainy':c<80?'Snow/ice':c<90?'Rain showers':'Thunderstorm';
const weatherIcon=c=>c===0?'☀️':c<=3?'⛅':c===45||c===48?'🌫️':c<=57?'🌦️':c<=67||c>=80&&c<=82?'🌧️':c<=77||c===85||c===86?'🌨️':c>=95?'⛈️':'☁️';
async function forecast(){
 const [la,lo]=$('city').value.split(',');
 const u=`https://api.open-meteo.com/v1/forecast?latitude=${la}&longitude=${lo}&current=temperature_2m,precipitation,weather_code&hourly=precipitation_probability,precipitation,weather_code&daily=weather_code,temperature_2m_max,precipitation_probability_max&timezone=Asia%2FManila&forecast_days=5`;
 try{
  const d=await (await fetch(u)).json(), cur=d.current;
  $('temp').textContent=Math.round(cur.temperature_2m)+'°C'; $('desc').textContent=WMO(cur.weather_code);
  $('weather-icon').textContent=weatherIcon(cur.weather_code); $('weather-icon').setAttribute('aria-label',WMO(cur.weather_code));
  const start=Math.max(0,d.hourly.time.findIndex(t=>t>=cur.time.slice(0,13)+':00'));
  const p=d.hourly.precipitation_probability.slice(start,start+6), mm=d.hourly.precipitation.slice(start,start+6), codes=d.hourly.weather_code.slice(start,start+6);
  const maxP=Math.max(...p), sum=mm.reduce((a,b)=>a+b,0);
  $('rain').textContent=`Rain chance (next 6h): ${maxP}% · ${sum.toFixed(1)} mm`;
  let v,w; if(maxP>=60||sum>=2){v='YES, bring one';w='High chance of rain soon.'} else if(maxP>=30){v='MAYBE';w='Some chance of showers. A foldable one helps.'} else {v='NO';w='Rain is unlikely in the next hours.'}
  $('verdict').textContent=v; $('why').textContent=w;
  $('hours').innerHTML=p.map((x,i)=>`<div class="card"><span>${new Date(d.hourly.time[start+i]).getHours()}:00</span><span class="forecast-icon" role="img" aria-label="${WMO(codes[i])}">${weatherIcon(codes[i])}</span><b>${x}%</b><small>${mm[i].toFixed(1)} mm</small></div>`).join('');
  $('days').innerHTML=d.daily.time.map((t,i)=>`<div class="card"><span>${new Date(t).toLocaleDateString('en',{weekday:'short'})}</span><span class="forecast-icon" role="img" aria-label="${WMO(d.daily.weather_code[i])}">${weatherIcon(d.daily.weather_code[i])}</span><b>${Math.round(d.daily.temperature_2m_max[i])}°</b><small>${d.daily.precipitation_probability_max[i]}% rain</small></div>`).join('');
 }catch(e){$('desc').textContent='Could not load forecast. Check your internet connection.'}
}
async function feed(){$('feed').innerHTML=await (await fetch('feed.php?severity='+$('sev').value)).text()}
async function act(url,id){await fetch(url,{method:'POST',body:new URLSearchParams({id})});feed()}
$('city').onchange=forecast; $('sev').onchange=feed;
if($('rf'))$('rf').onsubmit=async e=>{e.preventDefault();await fetch('submit.php',{method:'POST',body:new FormData(e.target)});e.target.reset();feed()};
forecast();feed();setInterval(feed,30000);
