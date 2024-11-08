window.onload = function() {
    setTimeout(function() {
        document.getElementById('worry-btn').click(); // Simulates the button click
    }, 2000); // Adjust delay as needed
};
document.addEventListener('DOMContentLoaded', () => {
    const userId = localStorage.getItem('username'); // Assuming user ID is stored as 'username'

const Address=localStorage.getItem('userAdress');
console.log("address is -------a-----",Address);
document.getElementById('address').value=Address;


    if (userId) {
        fetchCartItems(userId);
    } else {
        console.log('User is not logged in. Redirecting to login.');
        // Redirect to login page
        // location.href = 'login.html'; // Uncomment if needed
    }
});

function fetchCartItems(userId) {
    fetch(`fetch_cart.php?userId=${userId}`) // Adjust this URL according to your API
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok.");
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error("Error fetching cart items:", data.error);
            } else {
                displayCartItems(data.items);
                displayProducts(data.items);
            }
        })
        .catch(error => console.error("There was a problem with the fetch operation:", error));
}

function displayCartItems(items) {
    const productListContainer = document.getElementById('product-list');
    productListContainer.innerHTML = ''; // Clear existing content

    let subtotal = 0; // Initialize subtotal

    if (items.length === 0) {
        productListContainer.innerHTML = '<p>Your cart is empty.</p>';
    } else {
        items.forEach(product => {
            const productSection = document.createElement('div');
            productSection.className = 'product-section';

            // Build product images
            const productImages = document.createElement('div');
            productImages.className = 'product-images';
            productImages.innerHTML = `
                <img src="${product.image}" alt="${product.name}">
                
            `;
            productSection.appendChild(productImages);

            // Build product info
            const productInfo = document.createElement('div');
            productInfo.className = 'product-info';
            productInfo.innerHTML = `
                <h2>${product.name}</h2>
                <p class="price">$${parseFloat(product.price).toFixed(2)}</p>
                <label>
                    <input type="checkbox" onchange="updateSubtotal()">
                    Select
                </label>
                <div class="size-options" id="size-options-${product.id}"></div>
                <div class="color-options" id="color-options-${product.id}"></div>
                <div class="product-description">
                    <h3>Product Description</h3>
                    <p>${product.description}</p>
                </div>
                <div class="size-guide">
                    <h3>Size Guide</h3>
                    <table border="1" id="size-guide-table-${product.id}"></table>
                </div>
                <div class="reviews">
                    <h3>Customer Reviews</h3>
                    <div id="customer-reviews-${product.id}"></div>
                </div>
            `;
            productSection.appendChild(productInfo);

            // Calculate subtotal for this product
            const itemSubtotal = parseFloat(product.price) * (product.quantity || 1); // Default quantity to 1 if undefined
            subtotal += itemSubtotal; // Add to subtotal

            // Append the complete product section to the list
            productListContainer.appendChild(productSection);
        });

        // Update the subtotal display initially
        updateSubtotal();
    }
}

function updateSubtotal() {
    const productListContainer = document.getElementById('product-list');
    let subtotal = 0;

    // Select all checkboxes
    const checkboxes = productListContainer.querySelectorAll('input[type="checkbox"]');

    checkboxes.forEach((checkbox, index) => {
        if (checkbox.checked) {
            const priceElement = productListContainer.children[index].querySelector('.price');
            const priceValue = parseFloat(priceElement.textContent.replace('$', ''));
            subtotal += priceValue;
        }
    });

    // Update the subtotal and total displays
    document.getElementById('subtotal').innerText = `$${subtotal.toFixed(2)}`;
    document.getElementById('total').innerText = `$${(subtotal + 5).toFixed(2)}`; // Adding $5 shipping
}

function openModal() {
    document.getElementById("checkoutModal").style.display = "block";
}

function closeModal() {
    document.getElementById("checkoutModal").style.display = "none";
}

// Event listener to close modal on form submission (optional)
document.getElementById('checkoutForm').addEventListener('submit', (e) => {
    e.preventDefault(); // Prevent actual submission for now
    alert("Purchase completed!"); // Placeholder for completion message
    closeModal(); // Close modal after submission
});

function removeItem(itemId) {
    console.log('Removing item:', itemId);
    // Implement the logic to remove item from cart
    // After removing, you might want to call fetchCartItems again to refresh the cart display
}
function displayProducts(data) {
const products = JSON.parse(data); // Parse JSON string
const productList = document.getElementById('product-list');
productList.innerHTML = ''; // Clear previous content

products.items.forEach(item => {
    // Create product card
    const productCard = document.createElement('div');
    productCard.classList.add('product-card'); // Add a class for styling
    
    // Create image element
    const productImage = document.createElement('img');
    productImage.src = item.image; // Set the image source
    productImage.alt = item.name; // Set alt text
    productImage.classList.add('product-image'); // Add a class for styling

    // Create name element
    const productName = document.createElement('h3');
    productName.textContent = item.name;

    // Create price element
    const productPrice = document.createElement('p');
    productPrice.textContent = `$${item.price}`; // Display price with dollar sign

    // Append elements to the product card
    productCard.appendChild(productImage);
    productCard.appendChild(productName);
    productCard.appendChild(productPrice);

    // Append product card to the product list
    productList.appendChild(productCard);
});
}

// Call the function to display products
displayProducts(productData);