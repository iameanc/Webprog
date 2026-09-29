<?php require 'db.php'; require 'header.php';
$menu=$pdo->query('SELECT * FROM menu_items WHERE available=1 ORDER BY category,name')->fetchAll(); ?>
<div class="layout"><div class="grid">
<?php foreach($menu as $m): ?><div class="card"><b><?= e($m['name']) ?></b><span class="badge"><?= e($m['category']) ?></span>
<span>₱<?= number_format($m['price'],2) ?></span>
<button onclick="add(<?= $m['id'] ?>,'<?= e(addslashes($m['name'])) ?>',<?= $m['price'] ?>)">Add</button></div><?php endforeach; ?>
</div><aside class="card"><b>Your cart</b><div id="cart"></div><b id="total">Total: ₱0.00</b>
<input id="pickup" type="time" required><button onclick="checkout()">Place order</button><small id="msg"></small></aside></div></main>
<script>
let cart={};
function add(id,name,price){cart[id]=cart[id]||{name,price,qty:0};cart[id].qty++;draw()}
function chg(id,d){cart[id].qty+=d;if(cart[id].qty<=0)delete cart[id];draw()}
function draw(){let t=0,h='';for(const id in cart){const c=cart[id];t+=c.qty*c.price;
 h+=`<div>${c.name} x${c.qty} <button onclick="chg(${id},-1)">-</button><button onclick="chg(${id},1)">+</button></div>`}
 cart_.innerHTML=h||'<small>Empty</small>';total.textContent='Total: ₱'+t.toFixed(2)}
const cart_=document.getElementById('cart');draw();
async function checkout(){
 const items=Object.keys(cart).map(id=>({id:+id,qty:cart[id].qty}));
 if(!items.length||!pickup.value){msg.textContent='Add items and set a pickup time.';return}
 const r=await fetch('checkout.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({items,pickup:pickup.value})});
 const d=await r.json();msg.textContent=d.ok?'Order #'+d.order_id+' placed!':d.error;if(d.ok){cart={};draw()}}
</script></body></html>
