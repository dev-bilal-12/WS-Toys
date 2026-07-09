// ================= MOBILE MENU =================

const menuBtn = document.querySelector(".menu-btn");

const navLinks = document.querySelector(".nav-links");

if(menuBtn){

menuBtn.addEventListener("click",()=>{

navLinks.classList.toggle("active");

});

}

// ================= CART ELEMENTS =================

const cartBtn = document.querySelector(".cart-btn");

const cartSidebar = document.querySelector(".cart-sidebar");

const closeCart = document.querySelector(".close-cart");

const cartItems = document.querySelector(".cart-items");

const cartCount = document.getElementById("cart-count");

const totalText = document.querySelector(".cart-footer span");

const checkoutBtn = document.querySelector(".checkout-btn");

// ================= LOCAL STORAGE =================

let cart = JSON.parse(

localStorage.getItem("wstoysCart")

) || [];

// ================= OPEN CART =================

if(cartBtn){

cartBtn.addEventListener("click",()=>{

cartSidebar.classList.add("active");

});

}

// ================= CLOSE CART =================

if(closeCart){

closeCart.addEventListener("click",()=>{

cartSidebar.classList.remove("active");

});

}

// ================= SAVE CART =================

function saveCart(){

localStorage.setItem(

"wstoysCart",

JSON.stringify(cart)

);

}

// ================= ADD TO CART =================

document.addEventListener("click", (e) => {

  const btn = e.target.closest(".product-card button");

  if (!btn) return;

  const card = btn.closest(".product-card");

  if (!card) return;

  const nameEl = card.querySelector("h3");
  const priceEl = card.querySelector("strong");

  if (!nameEl || !priceEl) return;

  const product = {
    id: card.dataset.id || nameEl.innerText,
    name: nameEl.innerText,
    price: parseInt(priceEl.innerText.replace(/[^\d]/g, "")),
    qty: 1
  };

  const existing = cart.find(i => i.id === product.id);

  if (existing) {
    existing.qty++;
  } else {
    cart.push(product);
  }

  saveCart();
updateCart();
});
// ================= UPDATE CART =================

function updateCart(){

if(!cartItems) return;

cartItems.innerHTML="";

let total=0;

let quantity=0;

cart.forEach((item,index)=>{

total += item.price * item.qty;

quantity += item.qty;

cartItems.innerHTML += `

<div class="cart-product">

<h4>${item.name}</h4>

<p>Rs.${item.price}</p>

<div class="qty-box">

<button onclick="decreaseQty(${index})">

-

</button>

<span>${item.qty}</span>

<button onclick="increaseQty(${index})">

+

</button>

</div>

<button onclick="removeItem(${index})">

Remove

</button>

</div>

<hr>

`;

});

if(cartCount){

cartCount.innerText = quantity;

}

if(totalText){

totalText.innerText = `Rs.${total}`;

}

saveCart();

}

updateCart();

// ================= QUANTITY =================

function increaseQty(index){

cart[index].qty++;

updateCart();

}

function decreaseQty(index){

if(cart[index].qty > 1){

cart[index].qty--;

}

updateCart();

}

function removeItem(index){

cart.splice(index,1);

updateCart();

}
// ================= FAQ DROPDOWN =================

document.querySelectorAll(".faq-item").forEach(item=>{

item.addEventListener("click",()=>{

item.classList.toggle("active");

});

});

// ================= COLLECTION FILTER =================

const applyFilter=document.getElementById("applyFilter");

if(applyFilter){

applyFilter.addEventListener("click",()=>{

const min=

Number(

document.getElementById("minPrice").value

)||0;

const max=

Number(

document.getElementById("maxPrice").value

)||999999;

const boys=

document.getElementById("boys").checked;

const girls=

document.getElementById("girls").checked;

document

.querySelectorAll(".product-card")

.forEach(card=>{

const price=

Number(card.dataset.price);

const category=

card.dataset.category;

let show=true;

if(price<min || price>max){

show=false;

}

if(boys || girls){

if(boys && category!=="boys"){

show=false;

}

if(girls && category!=="girls"){

show=false;

}

if(boys && girls){

show=true;

}

}

card.style.display=

show ? "flex":"none";

});

});

}

// ================= CHECKOUT MODAL =================

function showCheckoutModal(){
	if(cart.length===0){
		alert("Your cart is empty.");
		return;
	}
	const old=document.getElementById("checkoutModal");
	if(old) old.remove();
	let total=0;
	let itemsHtml='';
	cart.forEach((item,i)=>{
		const sub=item.price*item.qty;
		total+=sub;
		itemsHtml+=`<tr><td>${item.name}</td><td>${item.qty}</td><td>Rs.${item.price}</td><td>Rs.${sub}</td></tr>`;
	});
	const modal=document.createElement("div");
	modal.id="checkoutModal";
	modal.innerHTML=`
	<div class="checkout-overlay">
		<div class="checkout-modal">
			<div class="checkout-step" id="step1">
				<h2>🧾 Order Summary</h2>
				<table>
					<thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
					<tbody>${itemsHtml}</tbody>
					<tfoot><tr><td colspan="3"><strong>Total</strong></td><td><strong>Rs.${total}</strong></td></tr></tfoot>
				</table>
				<button class="btn" onclick="showStep2()">Proceed to Details</button>
				<button class="btn btn-secondary" onclick="closeCheckout()">Cancel</button>
			</div>
			<div class="checkout-step" id="step2" style="display:none">
				<h2>👤 Customer Details</h2>
				<p>Fill in your details to place the order</p>
				<form id="checkoutForm" onsubmit="submitOrder(event)">
					<input type="text" name="name" placeholder="Full Name *" required>
					<input type="tel" name="phone" placeholder="Phone Number *" required>
					<input type="email" name="email" placeholder="Email Address">
					<textarea name="address" placeholder="Delivery Address *" required></textarea>
					<textarea name="notes" placeholder="Extra Notes (optional)"></textarea>
					<button type="submit" class="btn">Confirm Order via WhatsApp</button>
					<button type="button" class="btn btn-secondary" onclick="showStep1()">Back to Bill</button>
				</form>
			</div>
		</div>
	</div>`;
	document.body.appendChild(modal);
}

function closeCheckout(){
	const m=document.getElementById("checkoutModal");
	if(m) m.remove();
}

function showStep1(){
	document.getElementById("step1").style.display="block";
	document.getElementById("step2").style.display="none";
}

function showStep2(){
	document.getElementById("step1").style.display="none";
	document.getElementById("step2").style.display="block";
}

function submitOrder(e){
	e.preventDefault();
	const f=document.getElementById("checkoutForm");
	const name=f.name.value.trim();
	const phone=f.phone.value.trim();
	const address=f.address.value.trim();
	if(!name||!phone||!address){
		alert("Please fill in all required fields (Name, Phone, Address)");
		return;
	}
	let total=0;
	let message="🧸 *NEW ORDER - WS Toys*%0A%0A";
	message+="━━━━━━━━━━━━━━━%0A";
	message+="*🛒 PRODUCTS:*%0A";
	message+="━━━━━━━━━━━━━━━%0A%0A";
	cart.forEach(item=>{
		const sub=item.price*item.qty;
		total+=sub;
		message+=`• ${item.name}%0A`;
		message+=`  Qty: ${item.qty} × Rs.${item.price} = Rs.${sub}%0A%0A`;
	});
	message+="━━━━━━━━━━━━━━━%0A";
	message+=`*💰 TOTAL: Rs.${total}*%0A`;
	message+="━━━━━━━━━━━━━━━%0A%0A";
	message+="*👤 CUSTOMER INFO:*%0A";
	message+=`• Name: ${name}%0A`;
	message+=`• Phone: ${phone}%0A`;
	message+=`• Email: ${f.email.value||"N/A"}%0A`;
	message+=`• Address: ${address}%0A`;
	message+=`• Notes: ${f.notes.value||"None"}%0A%0A`;
	message+="✅ Please confirm my order.";
	window.open(`https://wa.me/923293048299?text=${message}`,"_blank");
	try{
		fetch("backend/api/orders.php",{
			method:"POST",
			headers:{"Content-Type":"application/json"},
			body:JSON.stringify({
				customer_name:name,
				customer_phone:phone,
				customer_email:f.email.value,
				customer_address:address,
				extra_notes:f.notes.value,
				items:cart
			})
		}).catch(()=>{});
	}catch(e){}
	cart=[];
	saveCart();
	updateCart();
	closeCheckout();
	alert("Order sent via WhatsApp! We'll contact you soon.");
}

if(checkoutBtn){
	checkoutBtn.addEventListener("click",showCheckoutModal);
}

// ================= FILTER TOGGLE =================

function toggleFilter(){
	const popup=document.getElementById("filterPopup");
	if(popup) popup.classList.toggle("active");
}

// Close filter on overlay click
document.addEventListener("click",(e)=>{
	const popup=document.getElementById("filterPopup");
	if(popup&&popup.classList.contains("active")){
		if(e.target===popup) popup.classList.remove("active");
	}
});

// ================= CONTACT PAGE =================

function sendMessage(){

let message=

"👋 Hello WsToys,%0A%0A";

message +=

"I want to know more about your products.%0A";

window.open(

`https://wa.me/923293048299?text=${message}`,

"_blank"

);

}

// ================= EMPTY CART TEXT =================

function checkEmptyCart(){

if(!cartItems) return;

if(cart.length===0){

cartItems.innerHTML=`

<div style="text-align:center;padding:50px 10px;">

<h3>🛒 Empty Cart</h3>

<p>

No products added yet.

</p>

</div>

`;

}

}

checkEmptyCart();

// ================= OVERLAY CLOSE =================

document.addEventListener("click",(e)=>{

if(

cartSidebar &&

cartSidebar.classList.contains("active")

){

if(

!cartSidebar.contains(e.target)

&&

!cartBtn.contains(e.target)

){

cartSidebar.classList.remove("active");

}

}

});

// ================= ESC KEY =================

document.addEventListener("keydown",(e)=>{

if(e.key==="Escape"){

if(cartSidebar){

cartSidebar.classList.remove("active");

}

}

});
