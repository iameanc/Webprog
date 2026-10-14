<?php require 'db.php'; require 'header.php'; $logged=!empty($_SESSION['user']); ?>
<div class="bar"><select id="city">
<option value="14.2117,121.1653">Calamba</option><option value="14.5995,120.9842">Manila</option>
<option value="14.6760,121.0437">Quezon City</option><option value="13.7565,121.0583">Batangas City</option><option value="10.3157,123.8854">Cebu</option><option value="7.1907,125.4553">Davao</option></select></div>
<section class="hero"><div><div id="desc">Loading...</div><div class="temperature"><h1 id="temp">--</h1><span id="weather-icon" role="img" aria-label="Current weather">⛅</span></div><small id="rain"></small></div>
<div class="advice">Should I bring an umbrella?<br><b id="verdict">...</b><br><small id="why"></small></div></section>
<div class="row" id="hours"></div><div class="row" id="days"></div>

<h2>Community reports</h2>
<?php if($logged): ?><form id="rf" class="card"><input name="location" placeholder="Where? (e.g. Crossing, Calamba)" required maxlength="120">
<select name="severity"><option value="Light">Light rain</option><option value="Heavy">Heavy rain</option><option value="Flooded">Flooded</option></select>
<input name="notes" placeholder="Notes (optional)" maxlength="255"><button>Post report</button></form>
<?php else: ?><p><a href="login.php">Log in</a> to post or confirm reports.</p><?php endif; ?>
<div class="bar"><select id="sev"><option value="">All severities</option><option value="Light">Light rain</option><option value="Heavy">Heavy rain</option><option value="Flooded">Flooded</option></select></div>
<div id="feed" class="grid"></div></main>
<script src="app.js"></script></body></html>
