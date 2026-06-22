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

// ================= WHATSAPP CHECKOUT =================

if(checkoutBtn){

checkoutBtn.addEventListener("click",()=>{

if(cart.length===0){
alert("Your cart is empty.");
return;
}

let total=0;
let message="🧸 Hello WsToys,%0A%0AI want to place an order.%0A%0A🛒 Products:%0A%0A";

cart.forEach(item=>{
let subtotal=item.price*item.qty;
total+=subtotal;

message+=`${item.name}%0AQty: ${item.qty}%0APrice: Rs.${item.price}%0ASubtotal: Rs.${subtotal}%0A%0A`;
});

message+=`💰 Total Bill: Rs.${total}%0A%0APlease confirm my order.`;

// WhatsApp open
window.open(`https://wa.me/923293048299?text=${message}`,"_blank");

// ✅ CLEAR CART AFTER ORDER
cart = [];
saveCart();
updateCart();

});

}
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
