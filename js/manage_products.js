// manage_products.js

document.addEventListener("DOMContentLoaded", function() {
    loadProducts();
});

function loadProducts() {
    fetch('get_products.php') // Fetch existing products from the database
        .then(response => response.json())
        .then(products => {
            const productList = document.getElementById('product-list');
            productList.innerHTML = ''; // Clear existing list
            products.forEach(product => {
                const productItem = document.createElement('div');
                productItem.innerText = `${product.name} - $${product.price} - Qty: ${product.quantity}`;
                productList.appendChild(productItem);
            });
        })
        .catch(error => console.error('Error loading products:', error));
}

function addProduct(event) {
    event.preventDefault();

    const name = document.getElementById('product-name').value;
    const price = document.getElementById('product-price').value;
    const quantity = document.getElementById('product-quantity').value;

    fetch('add_product.php', { // PHP file to handle adding a new product
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ name, price, quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadProducts(); // Reload product list after adding a new product
            document.getElementById('product-form').reset(); // Clear the form
        } else {
            alert('Error adding product: ' + data.message);
        }
    })
    .catch(error => console.error('Error adding product:', error));
}
