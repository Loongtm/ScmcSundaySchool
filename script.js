const btnCart = document.querySelector('.btn-cart');
const cart = document.querySelector('.cart');
const btnClose = document.querySelector('#cart-close');

btnCart.addEventListener('click', () => {
    cart.classList.add('cart-active');
});

btnClose.addEventListener('click', () => {
    cart.classList.remove('cart-active');
});

document.addEventListener('DOMContentLoaded', function () {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', addCartItem);
    });

    loadContent(); // Load cart content when the page loads
});

function loadContent() {
    let btnRemove = document.querySelectorAll('.cart-remove');
    btnRemove.forEach((btn) => {
        btn.addEventListener('click', removeItem);
    });

    let qtyElements = document.querySelectorAll('.cart-quantity');
    qtyElements.forEach((Input) => {
        Input.addEventListener('change', changeQty);
    });

    updateTotal();
}

function removeItem() {
    if (confirm('Are you sure want to remove?')) {
        let title = this.parentElement.querySelector('.cart-food-title').innerHTML;
        itemList = itemList.filter(el => el.title != title);
        this.parentElement.remove();
        loadContent(); // Update total after removing an item
    }
}

function changeQty() {
    if (isNaN(this.value) || this.value < 1) {
        this.value = 1;
    }
    loadContent(); // Update total after changing quantity
}

let itemList = [];

function addCartItem(event) {
    const foodItem = event.target.closest('.food-items');
    const title = foodItem.querySelector('.food-title').textContent;
    const price = parseInt(foodItem.querySelector('.price').textContent.replace(' Points', '')); // 使用 parseInt 处理价格
    const imgSrc = foodItem.querySelector('.food-img').getAttribute('src');

    let newProduct = { title, price, imgSrc };

    itemList.push(newProduct);

    let newProductElement = createCartProduct(title, price, imgSrc);
    let element = document.createElement('div');
    element.innerHTML = newProductElement;
    let cartBasket = document.querySelector('.cart-content');
    cartBasket.append(element);
    loadContent(); // Reload content after adding an item to the cart
}

function createCartProduct(title, price, imgSrc) {
    return `
        <div class="cart-box">
            <img src="${imgSrc}" class="cart-img">
            <div class="detail-box">
                <div class="cart-food-title">${title}</div>
                <div class="price-box">
                    <div class="cart-price">${price} Points</div>
                    <div class="cart-amt">${price} Points</div>
                </div>
                <input type="number" value="1" class="cart-quantity">
            </div>
            <i class="fa fa-trash cart-remove"></i>
        </div>
    `;
}

function updateTotal() {
    const cartItems = document.querySelectorAll('.cart-box');
    const totalValue = document.querySelector('.total-price');

    let total = 0;
    cartItems.forEach(product => {
        let priceElement = product.querySelector('.cart-price');
        let price = parseInt(priceElement.innerHTML.replace(" Points", "")); // 使用 parseInt 处理价格
        let qty = parseInt(product.querySelector('.cart-quantity').value); // 确保数量为整数
        total += price * qty;
        product.querySelector('.cart-amt').innerHTML = (price * qty) + " Points"; // 显示总金额
    });
    totalValue.innerHTML = total + ' Points'; // 显示总金额

    const cartCount = document.querySelector('.cart-count');
    let count = itemList.length;
    cartCount.innerHTML = count;

    if (count === 0) {
        cartCount.style.display = 'none';
    } else {
        cartCount.style.display = 'block';
    }
}