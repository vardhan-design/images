document.getElementById('imageInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.src = e.target.result;
            console.log(imagePreview.src);
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
function openAuthPopup() {
    const popup = document.getElementById('auth-popup');
    const popupContent = document.querySelector('.popup-content');
    


    if (popup) {
        popup.style.display = 'block';

        popupContent.classList.add('show');
        resetAuthForm();
    } else {
        console.error("Popup element not found");
    }
}

function closePopup() {
    console.log("close popup");
    const popup = document.getElementById('auth-popup');
    const popupContent = document.querySelector('.popup-content');
    const loginPasswordField = document.getElementById('auth-login-password');
    loginPasswordField.style.display = 'block';
    loginPasswordField.required = true;
    document.getElementById('auth-username').style.display='block';
    document.getElementById('auth-username').reuired=true;

    const loginPasswordLabel = document.getElementById('loginlabel').style.display='block';
    
    if (popup) {
        popup.style.display = 'none';
        popupContent.classList.remove('show');
    }
}

function resetAuthForm() {
    isInSignupMode = false;
    document.getElementById('popup-title').innerText = 'Log In';
    document.getElementById('auth-button').innerText = 'Log In';
    document.getElementById('switch-to-signup').style.display = 'block';
    document.getElementById('forgot-button').style.display = 'block';

    // Hide signup fields
    document.getElementById('signup-fields').style.display = 'none';
    document.getElementById('auth-signup-password').required = false;
    document.getElementById('auth-name').required = false;
    document.getElementById('auth-email').required = false;
    document.getElementById('auth-signup-username').reuired=false;

    // Show login password field and set as required
    const loginPasswordField = document.getElementById('auth-login-password');
    loginPasswordField.style.display = 'block';
    loginPasswordField.required = true;

    // Show login password label, if it exists
    const loginPasswordLabel = document.getElementById('auth-login-passwordLabel');
    if (loginPasswordLabel) {
        loginPasswordLabel.style.display = 'block';
    }
}

function Signout()
{
    console.log("signed out");
    const loginLink = document.getElementById('login-link');
    loginLink.onclick = function() { openAuthPopup(); return false; }; ; // Remove click functionality
    loginLink.style.color = 'gray'; // Change text color to gray
    loginLink.style.pointerEvents = 'auto'; // Disable pointer events
    localStorage.setItem('username',null);
    disableprofile();

    closeProfilePopup();
}

function switchToSignup() {
    isInSignupMode = true;
    document.getElementById('popup-title').textContent = "Sign Up";
    document.getElementById('auth-button').textContent = "Sign Up";
    document.getElementById('signup-fields').style.display = "block";
    document.getElementById('auth-username').style.display='none';
    document.getElementById('auth-username').reuired=false;
     document.getElementById('loginlabel').style.display='none';

    // Hide login password field and set required fields for signup
    const loginPasswordField = document.getElementById('auth-login-password');
    loginPasswordField.style.display = 'none';
    loginPasswordField.required = false;

    const loginPasswordLabel = document.getElementById('auth-login-passwordLabel');
    if (loginPasswordLabel) {
        loginPasswordLabel.style.display = 'none';
    }

    document.getElementById('auth-email').required = true;
    document.getElementById('auth-name').required = true;
    document.getElementById('auth-signup-password').required = true;
    document.getElementById('auth-signup-username').required = true;
}
function showForgotPassword() {
    alert("Forgot password functionality to be implemented."); // Placeholder for forgot password action
}

async function handleAuth(event) {
    
    event.preventDefault(); // Prevent the default form submission
    let username = null;
    let password = null;
    if (isInSignupMode) {
            username = document.getElementById('auth-signup-username').value;
            password = document.getElementById('auth-signup-password').value;
    }else
    {
        username = document.getElementById('auth-username').value;
        password = document.getElementById('auth-login-password').value;

    }

            // Validate username (email or mobile number)
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const isEmail = emailPattern.test(username);
            const isMobile = /^\d{10}$/.test(username); // Assuming a mobile number format of 10 digits

            if (isEmail || isMobile) {
                // Add your authentication logic here

    let requestBody;

    if (isInSignupMode) {
        
        const mobilenumber = document.getElementById('auth-signup-username').value;
        const name = document.getElementById('auth-name').value;
        const email = document.getElementById('auth-email').value;
        const signupPassword = document.getElementById('auth-signup-password').value;
        requestBody = { mobilenumber, password: signupPassword, name, email };
    } else {
        requestBody = { username, password};
    }

    const response = await fetch(isInSignupMode ? 'Signup.php' : 'AuthenticateLogin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestBody)
    });

    const result = await response.json();

    if (result.success) {
        isUserSignedIn = !isInSignupMode;
         // User is signed in after login

        // Store login status and user data in localStorage
        localStorage.setItem('isUserSignedIn', true);
        localStorage.setItem('username', result.user_id); // Adjust based on the response data
        localStorage.setItem('userToken', result.token); // Save a token if available
        if(!isInSignupMode){
        disableLoginLink();
    }
        closePopup(); // Close the popup
        alert(isInSignupMode ? 'Signup successful! Please log in.' : 'Login successful!' + localStorage.getItem('username')+localStorage.getItem('isUserSignedIn'));
        const loginPasswordField = document.getElementById('auth-login-password');
    loginPasswordField.style.display = 'none';
    loginPasswordField.required = false;
    Enableeprofile();


    const loginPasswordLabel = document.getElementById('auth-login-passwordLabel');
    } else {
        
        alert(result.message);
    }
     
    // Simulate successful authentication
        
            } else {
                
                alert("Please enter a valid email or mobile number.");
            }
}

function disableLoginLink() {
    const loginLink = document.getElementById('login-link');
    loginLink.onclick = null; // Remove click functionality
    loginLink.style.color = 'gray'; // Change text color to gray
    loginLink.style.pointerEvents = 'none'; // Disable pointer events
}
function openProfilePopup() {
    const profilePopup = document.getElementById('profile-popup');
    if (profilePopup) {
        profilePopup.style.display = 'block';
        console.log("enterd function");
        
        profilePopup.classList.add('show');
        const userId = localStorage.getItem('username'); // Retrieve user ID from localStorage

            
                // User is signed in; fetch their data
                console.log('User is logged in:', userId);
                fetchUserData(userId);
            
    }
}

function closeProfilePopup() {
    console.log("closeup");
    const profilePopup = document.getElementById('profile-popup');
    if (profilePopup) {
        profilePopup.style.display = 'none';
    }
    
}

// Ensure to close the popups on clicking outside (if needed)
window.onclick = function(event) {
    const authPopup = document.getElementById('auth-popup');
    const profilePopup = document.getElementById('profile-popup');
    if (event.target === authPopup) {
        closePopup();
    }
    if (event.target === profilePopup) {
        closeProfilePopup();
    }
};
function fetchUserData(userId) {
    console.log("Fetching data for user ID:", userId);

    fetch(`fetch_user.php?id=${userId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok.");
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error("Error:", data.error);
            } else {
                document.getElementById('profile-username').innerText = data.name; // Use "name" as per the PHP response
                document.getElementById('profile-email').innerText = data.email;
                const profileAvatar = document.getElementById('profile-avatar');
                profileAvatar.src = data.image;
                document.getElementById('profile-contact').innerText = data.contact;
                document.getElementById('profile-Adress').innerText = data.address;
                

            }
        })
        .catch(err => console.error("Fetch error:", err));
}

function openModal() {
        const profilename = document.getElementById('profile-username').innerText;
        console.log(profilename);
        const profileemail = document.getElementById('profile-email').innerText;
        const profileimage = document.getElementById('profile-avatar').innerText;
        const profileAddress = document.getElementById('profile-Adress').innerText;
        const profilecontact = document.getElementById('profile-contact').innerText;
        
    
        // Set current address to the input fields
        document.getElementById('name').value = profilename; 
        document.getElementById('email').value = profileemail; 
        document.getElementById('adress').value = profileAddress;  
        document.getElementById('contact').value = profilecontact; 
        document.getElementById('myModal').style.display = "block"; // Show the modal
    }
    function closeModal() {
        document.getElementById('myModal').style.display = "none"; // Hide the modal
    }

    function saveAddress() {
        const profilenamesave = document.getElementById('name').value;
        const profileemailsave = document.getElementById('email').value;
        const profileAddresssave = document.getElementById('adress').value;
        const profilecontactsave = document.getElementById('contact').value;
        const profileimagesave = document.getElementById('imageInput').files[0];

        console.log(profileimagesave);
    
    
        console.log('Saving new details:');
        
        // Make an AJAX request to save the updated address to the database
        const userId = localStorage.getItem('username'); // Retrieve user ID from localStorage
       fetch('update_address.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: userId,
            name: profilenamesave,
            email: profileemailsave,
            adress: profileAddresssave,
            contact:profilecontactsave,
            image:profileimagesave.name,
        }),
    })
    .then(response => {
        console.log('Response:', response); // Log the raw response
        return response.json();
    })
    .then(data => {
        if (data.success) {
    localStorage.setItem('userAdress', data.address);
    console.log(data.address);
            alert('Address updated successfully!');

            document.getElementById('profile-username').innerText = data.name; // Use "name" as per the PHP response
            document.getElementById('profile-email').innerText = data.email;
            document.getElementById('profile-Adress').innerText = data.address;
            document.getElementById('profile-contact').innerText = data.contact;
            const profileAvatar = document.getElementById('profile-avatar');
            profileAvatar.src = data.image;
        } else {
            alert('Failed to update address: ' + data.error);
        }
    })
    .catch(err => console.error('Error saving address:', err));
    
    
        // Close the modal after saving
        closeModal();
    }
    function getImageSrc() {
        const imageFile = document.getElementById('image').files[0]; // Get the file
        const reader = new FileReader();
    
        reader.onload = function(event) {
            const imageSrc = event.target.result; // This is the data URL
            console.log("Image Source (data URL):", imageSrc);
    
            // Optionally, display the image in an <img> element
            const img = document.getElementById('imagePreview');
            img.src = imageSrc;
            img.style.display = 'block';
        };
    
        if (imageFile) {
            reader.readAsDataURL(imageFile); // Convert to data URL
        } else {
            console.log("No file selected.");
        }
    }
    function uploadImage() {
        const formData = new FormData();
        const fileInput = document.getElementById('imageInput');
        const profileimagesave = document.getElementById('imageInput').files[0];

        console.log(profileimagesave);
        
        if (fileInput.files.length > 0) {
            formData.append("image", fileInput.files[0]);
            
            fetch('upload_image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Image uploaded successfully!");
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => {
                console.error("Upload error:", error);
                alert("Failed to upload image.");
            });
        } else {
            alert("Please select an image.");
        }
    }
    function toggleFavorite(icon, title, price) {
        
        const productId = icon.getAttribute('data-product-id'); // Get product ID
        const productTitle = title; // Get product title from parameter
        const productPrice = price; // Get product price from parameter
        let userid=localStorage.getItem('username');
        if(userid==='null')
            {
                 alert("Please login to select favourite");
                 return;
                 
            }
        if (productId) {
           let wishlistdata= addToWishlist(productId,icon);
            
            //addToWishlist(icon,data.message,data.count);
            }
    }
    function toggleCart(icon, title, price) {

        const productId = icon.getAttribute('data-product-id'); // Get product ID
        const productTitle = title; // Get product title from parameter
        const productPrice = price; // Get product price from parameter
        let userid=localStorage.getItem('username');
        if(userid==='null')
            {
                 alert("Please login to select cart");
                 return;
                 
            }
        if (productId) {
            updateCartQuantity(productId);
            //addToCart(icon,data.message,data.count);
        }
    }
    let cartCount = 0; // Initialize cart count
    let wishlistCount = 0; // Initialize wishlist count

    function addToCart(icon,message,count) {
        console.log(message);
        if(message==='Product added to wishlist'){
            console.log(message);
        //wishlistCount++; // Increment wishlist count
        document.getElementById('cart-notify').textContent = count;
        icon.classList.add("cart-filled");
    }else{
        //wishlistCount--;
        console.log(message);
        document.getElementById('cart-notify').textContent = count; // Update badge
        icon.classList.remove("cart-filled");
      
    }
}

    function filliconWishlist(icon,message,count) {
        console.log(message);
        if(message==='Product added to wishlist'){
            console.log(message);
         // Increment wishlist count
         icon.classList.add("filled");
        document.getElementById('wishlist-notify').textContent = count;
        
    }else{
        
        console.log(message);
        document.getElementById('wishlist-notify').textContent = count; // Update badge
        //window.location.href = 'shopping_cart.html?type=wishlist'; // Redirect to wishlist page
        icon.classList.remove("filled");
    }
}

document.addEventListener('DOMContentLoaded', Refreshs);
async function Refreshs() 
{
    fetchProductDetails(2);
    fetchProductDetails(3);
}
async function fetchProductDetails(categorytype) {
    try {
        const response =  await fetch('fetch_products.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ category: categorytype }),
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        console.log(data.products);
        const processedResponse = processProductData(data);
        renderProductGrid(processedResponse.products,categorytype);
    } catch (error) {
        console.error('Error fetching products:', error);
    }
}

// Function to render the product grid
function renderProductGrid(products,categorytype) {
    let productGrid=null;
    console.log(categorytype);
    if(categorytype==2){
        const productGrid = document.getElementById('product-grid1');
        productGrid.innerHTML = ''; // Clear any existing content
        console.log("hhjkjkkjkh",products.length);
        
        disableprofile();
        if (Array.isArray(products) && products.length > 0) {
        products.forEach(product => {
            console.log(product);
            // Create product card container
            const productCard = document.createElement('div');
            productCard.classList.add('product-card');
    
            // Set inner HTML for product card
            productCard.innerHTML = `
                <div class="image-container">
                    <img src="${product.image_url}" alt="${product.name}" class="image1">
                    <img src="${product.image2}" alt="${product.name}" class="image2">
                </div>
                <h3>${product.name}</h3>
                <p>$${product.price}</p>
                <div class="icon-container">
                    <span class="icon favorite-icon" onclick="toggleFavorite(this);" data-product-id="${product.product_id}">♡</span>
                    <span class="icon cart-icon" onclick="toggleCart(this);" data-product-id="${product.product_id}">🛒</span>
                </div>
            `;
            productCard.onclick = function() {
                if (!event.target.closest('.icon')) {
                    this.classList.toggle('selected');
                    window.location.href = `backupofproduct.html?id=${product.product_id}`;
                }
            };
    
            // Append product card to grid
            productGrid.appendChild(productCard);
        });
    }
        
    }else if (categorytype==3)
    {
        const productGrid = document.getElementById('product-grid2');
        productGrid.innerHTML = ''; // Clear any existing content
        productGrid.style.display = 'grid';
        productGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
        productGrid.style.gap = '20px';

        console.log("hhjkjkkjkh",products.length);
        if (Array.isArray(products) && products.length > 0) {
        products.forEach(product => {
            console.log(product);
            // Create product card container
            const productCard = document.createElement('div');
            productCard.classList.add('product-card');
    
            // Set inner HTML for product card
            productCard.innerHTML = `
                <div class="image-container">
                    <img src="${product.image_url}" alt="${product.name}" class="image1">
                    <img src="${product.image2}" alt="${product.name}" class="image2">
                </div>
                

                <h3>${product.name}</h3>
                <p>$${product.price}</p>
                <div class="icon-container">
                    <span class="icon favorite-icon" onclick="toggleFavorite(this);" data-product-id="${product.product_id}">♡</span>
                    <span class="icon cart-icon" onclick="toggleCart(this);" data-product-id="${product.product_id}">🛒</span>
                </div>
            `;
            const imagecontainer = document.getElementById('image-container');
            productCard.onclick = function() {
                this.classList.toggle('selected');
                window.location.href = `backupofproduct.html?id=${product.product_id}`;
            };
    
            // Append product card to grid
            productGrid.appendChild(productCard);
        });
    }
    }

    
 
}
function disableprofile()
{
    let userid=localStorage.getItem('username');
    if(userid==='null')
        {
             document.getElementById('profile-link').style.display='none';
        }
}
function Enableeprofile()
{
    let userid=localStorage.getItem('username');
    if(userid !='null')
        {
             document.getElementById('profile-link').style.display='block';
        }
}
document.addEventListener('DOMContentLoaded', function() {
    const productGrid = document.getElementById('productGrid'); // Ensure this ID exists
    products.forEach(product => {
        const productCard = document.createElement('div');
        productCard.classList.add('product-card');

        productCard.innerHTML = `
            <div class="image-container">
                <img src="${product.image_url}" alt="${product.name}" class="image1">
                <img src="${product.image2}" alt="${product.name}" class="image2">
            </div>
            <h3>${product.name}</h3>
            <p>$${product.price}</p>
            <div class="icon-container">
                <span class="icon favorite-icon" onclick="toggleFavorite(this);" data-product-id="${product.product_id}">♡</span>
                <span class="icon cart-icon" onclick="toggleCart(this);" data-product-id="${product.product_id}">🛒</span>
            </div>
        `;

        productCard.onclick = function() {
            this.classList.toggle('selected');
            window.location.href = `backupofproduct.html?id=${product.product_id}`;
        };

        // Append product card to grid
        productGrid.appendChild(productCard);
    });
});

function processProductData(data) {
    if (!data.success) {
      console.error('Failed to fetch products');
      return;
    }
  
    const processedProducts = {};
  
    // Process each product
    data.products.forEach(product => {
      // If the product already exists, accumulate the rating and quantity
      if (processedProducts[product.product_id]) {
        processedProducts[product.product_id].ratings.push(product.rating);
        processedProducts[product.product_id].quantity += product.quantity;
      } else {
        // Otherwise, initialize a new product entry
        processedProducts[product.product_id] = {
          ...product,
          ratings: [product.rating], // Start with the first rating
        };
      }
    });
  
    // Now calculate the average rating for each product
    Object.keys(processedProducts).forEach(productId => {
      const product = processedProducts[productId];
      const totalRatings = product.ratings.reduce((sum, rating) => sum + rating, 0);
      const avgRating = totalRatings / product.ratings.length;
  
      // Update product with the calculated average rating
      product.average_rating = avgRating.toFixed(2); // Fix to 2 decimal places
  
      // Remove ratings array after processing
      delete product.ratings;
    });
  
    // Return the processed products in the exact structure like PHP
    return {
      success: true,
      products: Object.values(processedProducts)
    };
  }
  function updateCartQuantity(productId) {
    let quantity=1;
    
    fetch('UpdateCartAndQuantity.php', {  // PHP script to update cart
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `product_id=${productId}&quantity=${quantity}&user_id=${localStorage.getItem('username')},`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optionally update the cart on the page
            alert('Cart updated successfully');
            loadCart(); // Reload cart data to reflect the changes
        } else {
            alert('Failed to update cart');
        }
    });
}

function addToWishlist(productId,icon) {
    let wishlistdata=null;
   
    fetch('UpdateWishlist.php', {
        method: 'POST',
        headers: {
         'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `product_id=${productId}&user_id=${localStorage.getItem('username')},`
    })
    .then(response => response.json())
    .then(data => {
        wishlistdata=data;
        if (data.success) {
            alert("Product added to your wishlist!");
            filliconWishlist(icon,wishlistdata.message,wishlistdata.count);
        } else {
            alert("Failed to add product to wishlist: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error adding product to wishlist: ", error);
        alert("An error occurred. Please try again.");
    });
    return wishlistdata;
}
function removeFromWishlist(productId) {
    
    if (!userId) {
        alert("You must be logged in to remove items from the wishlist.");
        return;
    }

    const data = new FormData();
    data.append('product_id', productId);

    fetch('remove_wishlist.php', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Product removed from your wishlist!");
            // Optionally update the UI to reflect the change
            document.getElementById(`wishlist-product-${productId}`).remove();
        } else {
            alert("Failed to remove product from wishlist: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error removing product from wishlist: ", error);
        alert("An error occurred. Please try again.");
    });
}
function deletefromcart(productId)
{
    fetch('DeletefromCart.php', {  // PHP script to update cart
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `product_id=${productId}&user_id=${localStorage.getItem('username')},`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optionally update the cart on the page
            alert('Removed from successfully');
            loadCart(); // Reload cart data to reflect the changes
        } else {
            alert('Failed to update cart');
        }
    });
}

  

