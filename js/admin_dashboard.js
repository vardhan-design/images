document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('customer-table').style.display = 'none';
    document.getElementById('ptable').style.display = 'none';
    document.getElementById('name_of_list').innerText = 'Admin Page';
    
});

// Fetch all products for admin
async function fetchProductDetails() {
    try {
        const response = await fetch('fetchproductforadmin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ category: 0 }),
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        producttable(data);
        
        
    } catch (error) {
        console.error('Error fetching product details:', error.message);
    }
    document.getElementById('ptable').style.display = 'block';
     document.getElementById('name_of_list').innerText = "ProductList";
    
    document.getElementById('customer-table').style.display = 'none';
}
async function userorders(userid)
{
    const response = await fetch('ordersfromuser.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ userId: userid }),
    });

    if (!response.ok) {
        throw new Error('Network response was not ok');
    }

    const data = await response.json();
    populateCustomerTable(data.user)
    producttable(data);
    document.getElementById('customer-table').style.display = 'block';
    document.getElementById('ptable').style.display = 'block';
    document.getElementById('name_of_list').innerText = "ProductList";
   
   

}

function producttable(data)
{
    // Clear any existing rows
    const productTableBody = document.getElementById('product-table-body');
     document.getElementById('productForm').style.display='none';
    productTableBody.innerHTML = '';

    // Check if the data contains products
    if (data.success && data.products && data.products.length > 0) {
        data.products.forEach(product => {
            // Create a new row for each product
            const row = document.createElement('tr');
            row.setAttribute('data-id', product.id);

            row.innerHTML = `
                <td class="product-item">
                    <img src="${product.image_url}" alt="${product.name}" class="product-image">
                    <span class="product-name">${product.name}</span>
                </td>
                <td class="product-category">${product.category}</td>
                <td class="product-price">${product.price}</td>
                <td class="product-quantity">${product.inventory_quantity}</td>
                <td>${product.reviews.length > 0 ? product.reviews.map(review => `
                    <div class="review">
                        <p>Rating: ${review.rating} ⭐</p>
                        <p>${review.text}</p>
                    </div>
                `).join('') : '<p>No reviews available.</p>'}</td>
                <td class="product-status">${product.status}</td>
                <td><button class="editproduct" onclick="openEditModal(this)">Edit</button></td>
            `;
            productTableBody.appendChild(row);
        });
    } else {
        productTableBody.innerHTML = '<tr><td colspan="6">No products found.</td></tr>';
    }
}
// Open edit modal and populate fields
function openEditModal(button) {
    const row = button.closest('tr');
    const productId = row.getAttribute('data-id');

    const productName = row.querySelector('.product-name')?.innerText || "N/A";
    const productCategory = row.querySelector('.product-category')?.innerText || "N/A";
    const productPrice = row.querySelector('.product-price')?.innerText || "N/A";
    const productQuantity = row.querySelector('.product-quantity')?.innerText || "N/A";
    document.getElementById('product-id').value=productId;

    document.getElementById('product-name').value = productName;
    document.getElementById('category').value = productCategory;
    document.getElementById('price').value = productPrice;
    document.getElementById('stock').value = productQuantity;

    document.getElementById('edit-product-modal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('edit-product-modal').style.display = 'none';
}

// Fetch all customers
async function fetchAllCustomers() {
    try {
        const response = await fetch('fetch_customer_details.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        populateCustomerTable(data.users);
    } catch (error) {
        console.error('Error fetching customers:', error.message);
    }
}


// Populate customer table
function populateCustomerTable(customers) {
    const customerTableBody = document.getElementById('customer-table-body');
    customerTableBody.innerHTML = '';

    const processedUserIds = new Set();

    customers.forEach(customer => {
        if (processedUserIds.has(customer.user_id)) return;
        processedUserIds.add(customer.user_id);

        const row = document.createElement('tr');
        row.setAttribute('data-customer-id', customer.user_id);
        row.innerHTML = `
            <td class="product-item">
                <img src="${customer.image}" alt="${customer.name}" class="product-image">
                <span class="product-name">${customer.name}</span>
            </td>
            <td>${customer.email}</td>
            <td>${customer.phone}</td>
            <td>${customer.address}</td>
            <td>${customer.registration_date}</td>
            <td><button onclick="userorders(${customer.user_id})">View Orders</button></td>
        `;
        
        customerTableBody.appendChild(row);
    });

    document.getElementById('customer-table').style.display = 'block';
    document.getElementById('ptable').style.display = 'none';
    document.getElementById('name_of_list').innerText = 'UsersList';
}

// Fetch customer details and orders
async function fetchCustomerDetails(customerId) {
    try {
        const response = await fetch('fetchCustomerDetails.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: customerId }),
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        if (data.success) {
            document.getElementById('customer-name').innerText = `Name: ${data.customer.name}`;
            document.getElementById('customer-email').innerText = `Email: ${data.customer.email}`;
            document.getElementById('customer-phone').innerText = `Phone: ${data.customer.phone}`;
            document.getElementById('customer-address').innerText = `Address: ${data.customer.address}`;
            document.getElementById('customer-registration-date').innerText = `Registration Date: ${data.customer.registration_date}`;

            const orderDetailsBody = document.getElementById('order-details-body');
            orderDetailsBody.innerHTML = '';
            data.orders.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${order.order_id}</td>
                    <td>${order.date}</td>
                    <td>${order.total_amount}</td>
                    <td>${order.status}</td>
                `;
                orderDetailsBody.appendChild(row);
            });

            document.getElementById('customer-details-modal').style.display = 'block';
        } else {
            console.error('Failed to fetch customer details:', data.message);
        }
    } catch (error) {
        console.error('Error fetching customer details:', error.message);
    }
}

function closeCustomerDetailsModal() {
    document.getElementById('customer-details-modal').style.display = 'none';
}

function logout() {
    window.location.href = 'login.html';
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        document.querySelector('.toggle-btn').innerHTML = '&#x2794;';
    } else {
        sidebar.classList.add('active');
        document.querySelector('.toggle-btn').innerHTML = '&#x2190;';
    }
}
async function getorderchart(){
fetch('fetch_analytics_data.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const productNames = data.data.map(item => item.product_name);
                    const orderedQuantities = data.data.map(item => item.total_ordered_quantity);

                    // Render the bar chart
                    const ctx = document.getElementById('analyticsChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: productNames,
                            datasets: [{
                                label: 'Total Ordered Quantity',
                                data: orderedQuantities,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            }
                        }
                    });
                } else {
                    console.error('Failed to fetch analytics data');
                }
            })
            .catch(error => console.error('Error fetching data:', error));
        }

// Placeholder functions
function filterProducts() {
    alert("Filter functionality to be implemented.");
}

function viewAllProducts() {
    alert("View all products functionality to be implemented.");
}

function addProduct() {
    alert("Add product functionality to be implemented.");

    document.getElementById('productForm').style.display='block';
    document.getElementById('ptable').style.display = 'none';
    
}
document.getElementById('imageInput').addEventListener('change', function(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('previewContainer');

    // Clear any existing previews
    previewContainer.innerHTML = '';

    // Limit selection to 4 images
    if (files.length > 4) {
        alert("Please select only up to 4 images.");
        return;
    }

    // Loop through each selected file and create an image preview
    Array.from(files).slice(0, 4).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
document.getElementById('CreateimageInput').addEventListener('change', function(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('createpreviewContainer');

    // Clear any existing previews
    previewContainer.innerHTML = '';

    // Limit selection to 4 images
    if (files.length > 4) {
        alert("Please select only up to 4 images.");
        return;
    }

    // Loop through each selected file and create an image preview
    Array.from(files).slice(0, 4).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
function updateProduct() {
   
    
    // Get images from the file input
    const files = Array.from(document.getElementById('imageInput').files); 
    
    
    // Assign up to 4 images to formData
    files.sort((a, b) => {
        if (a.name.includes('Main')) return -1;
        if (b.name.includes('Main')) return 1;
        if (a.name.includes('model')) return -1;
        if (b.name.includes('model')) return 1;
        if (a.name.includes('modelback')) return -1;
        if (b.name.includes('modelback')) return 1;
        return 0; // No specific keyword, move to the end
    });
    console.log(files);
    const formData = {
        product_id: document.getElementById('product-id').value,
        product_name: document.getElementById('product-name').value,
        category_name: document.getElementById('category').value,
        price: parseFloat(document.getElementById('price').value),
        quantity: parseInt(document.getElementById('stock').value),
        image_url: files[0].name,
        image2: files[1].name,
        image3:files[2].name,
        image4:files[3].name
    };

    fetch('AdminUpdateProductDetails.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            //fetchProductDetails(); // Refresh the product list if needed
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
function AddingProduct() {
    event.preventDefault();
    
    // Get images from the file input
    const files = Array.from(document.getElementById('CreateimageInput').files); 
    
    
    // Assign up to 4 images to formData
    files.sort((a, b) => {
        if (a.name.includes('Main')) return -1;
        if (b.name.includes('Main')) return 1;
        if (a.name.includes('model')) return -1;
        if (b.name.includes('model')) return 1;
        if (a.name.includes('modelback')) return -1;
        if (b.name.includes('modelback')) return 1;
        return 0; // No specific keyword, move to the end
    });
    console.log(files);
    const formData = {
        //product_id: document.getElementById('Addproduct-id').value,
        product_name: document.getElementById('Addproduct-name').value,
        brand:document.getElementById('Addbrand').value,
        description:document.getElementById('Adddescription').value,
        fabriccare: document.getElementById('Addfabric').value,
        sizeoption: document.getElementById('Addsize').value,
        coloroption: document.getElementById('Addcolor').value,
        price: document.getElementById('Addprice').value,
        stock: document.getElementById('Addstock').value,
        mfgdate: document.getElementById('Addcreated-at').value,
        category_name: document.getElementById('Addcategory').value,
        image_url: files[0].name,
        image2: files[1].name,
        image3:files[2].name,
        image4:files[3].name
    };

    fetch('add_products.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            //fetchProductDetails(); // Refresh the product list if needed
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
function categorie()
{
    document.getElementById('categories').style.display='block';
}
// Function to add a new category to the dropdown and to the database
function addCategory() {
    
    
    const newCategory = document.getElementById('new-category').value;
    const categoryDropdownsample = document.getElementById('categoryselect');
    const addcategory=document.getElementById('Addcategory');
    modifylistvalue(categoryDropdownsample,newCategory);
    modifylistvalue(addcategory,newCategory);
        
   
    if (newCategory.trim() !== "") {
        // Send the new category to the backend to store in the database
        fetch('add_category.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ category_name: newCategory })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Category added successfully!');
                document.getElementById('new-category').value = ''; // Clear the input field
            } else {
                alert('Error adding category');
            }
        })
        .catch(error => console.error('Error:', error));
    } else {
        alert('Please enter a valid category name.');
    }
}

function modifylistvalue(select,value)
{
    if (value.trim() !== "") {
        // Add the new color to the dropdown list
        const newOption = document.createElement('option');
        newOption.value = value;
        newOption.text = value;
        select.appendChild(newOption);
    }
}

// Function to add a new color to the dropdown and to the database
function addColor() {
    const newColor = document.getElementById('new-color').value;
    const colorDropdown = document.getElementById('color');

    if (newColor.trim() !== "") {
        // Add the new color to the dropdown list
        const newOption = document.createElement('option');
        newOption.value = newColor;
        newOption.text = newColor;
        colorDropdown.appendChild(newOption);
}
}

// Function to add a new size to the dropdown and to the database
function addSize() {
    const newSize = document.getElementById('new-size').value;
    const sizeDropdown = document.getElementById('size');

    if (newSize.trim() !== "") {
        // Add the new size to the dropdown list
        const newOption = document.createElement('option');
        newOption.value = newSize;
        newOption.text = newSize;
        sizeDropdown.appendChild(newOption);
    }
}
function updateColorPreview() {
    const colorDropdown = document.getElementById('Addcolor');
    const colorPreview = document.getElementById('colorPreview');

    // Get the selected color value
    const selectedColor = colorDropdown.value;

    // Update the preview box color
    colorPreview.style.backgroundColor = selectedColor;
}

// Call once on page load to set default preview
updateColorPreview();



