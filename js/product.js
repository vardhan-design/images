
        let selectedPrice=500;
        let lastOpenedDropdown = null;
        let selectedsubcategoryvalue=null;
        let currentropdown=null;
        function toggleDropdown(id, button) {
            console.log(toggleDropdown);
            const dropdown = document.getElementById(id);
            const isVisible = dropdown.classList.contains('show-up');
            currentropdown=dropdown;
            // Close any open dropdowns
            document.querySelectorAll('.dropdown-content').forEach(content => content.classList.remove('show-up'));
            document.querySelectorAll('.filter-item button').forEach(btn => btn.classList.remove('active'));
            // If not currently visible, show this one
            console.log("isvisible value",isVisible);
            if (!isVisible) {
                dropdown.classList.add('show-up');
                
                button.classList.add('active');
                console.log("last open dropdown",lastOpenedDropdown);

                
                if(lastOpenedDropdown!=null)
                    {
                        adjustcategoryafteranyoptionselected(dropdown.offsetHeight);
                    }
                    let boundryof=document.getElementById('navbar').getBoundingClientRect();
                    dropdown.style.top=`${boundryof.bottom}px`;
                    
                    adjustClearButtonPosition(boundryof.bottom); // Reset position if dropdown is closed

            }
            else 
            {
                if(lastOpenedDropdown!=null)
                {
                    adjustcategoryafteranyoptionselected(dropdown.offsetHeight);
                }
                //dropdown.style.top=`${175}px`;
                let boundryof=document.getElementById('navbar').getBoundingClientRect();
                adjustClearButtonPosition(boundryof.bottom); 
            }
            
        }
        function adjustcategoryafteranyoptionselected(height)
        {
            console.log("adjustcategoryafteranyoptionselected");
            console.log(height);
            const categoryElements=document.getElementById('dropdown-contentforcategory');
            
    categoryElements.style.top = `${0 + height + 40}px`;
        }

    // function triggers when any sub category value is selected
        function addSelectedValue(value) {
            console.log("addSelectedValue");
            const selectedValuesDiv = document.getElementById('selectedValues');

    // Check if the value is already selected to avoid duplicates
    const existingSelection = Array.from(selectedValuesDiv.children).find(child => child.textContent.trim().startsWith(value));
    if (existingSelection) return;

    const selectedValueDiv = document.createElement('div');
    selectedValueDiv.classList.add('selected-value');
    selectedValueDiv.innerHTML = `${value} <span class="remove-btn" onclick="removeOption(this)">✖</span>`;
    selectedValuesDiv.appendChild(selectedValueDiv);

    // Show the selected values div if it's not already visible
    selectedValuesDiv.style.display = 'flex';
    // Show clear button if there are selected values
    const clearBtn = document.getElementById('clearBtn');
    clearBtn.style.display = 'flex';
}
//Updating the price value
        function updatePriceValue(value) {
            console.log("updatePriceValue");
            console.log()
            document.getElementById('priceValue').innerText = value;
            selectedPrice = value; // Update the selected price variable
            console.log(selectedPrice);
            updateSelectedValues(); // Call to update selected values display
        }
// setting the possition of clearbutton
        function adjustClearButtonPosition(dropdownHeight) {
            console.log("adjustClearButtonPosition");
            const selectedValues = document.getElementById('setselectsectionandclearsection');
            selectedValues.style.position='relative';
            if(currentropdown!=null&&currentropdown.getBoundingClientRect().bottom>0){
                selectedValues.style.top = `${currentropdown.getBoundingClientRect().bottom -70}px`;
                let productlist=document.getElementById('product-grid2');
                productlist.style.position='relative';
                productlist.style.top=`${150}px`;
                selectedValues.style.left=`${100}px`;

            }
            else{
                selectedValues.style.top = `${90}px`;
                let productlist=document.getElementById('product-grid2');
                productlist.style.position='relative';
                productlist.style.top=`${30}px`;

            }
        }
//Triggers when main category valus is clicked
   
//Triggers when main category valus is clicked
function toggleMainDropdown() {
    console.log("toggleMainDropdown");
            const categoryDropdown = document.getElementById('dropdown-contentforcategory');
            const isVisible = categoryDropdown.classList.contains('show-up');
            if(!isVisible)
            {
                let boundryof=document.getElementById('navbar').getBoundingClientRect();
                categoryDropdown.classList.add('show-up');
                adjustClearButtonPosition(boundryof.bottom);
                let rect=  categoryDropdown.getBoundingClientRect();
                let productlist=document.getElementById('product-grid2');
                productlist.style.top = `${boundryof.bottom}px`; // Position below the dropdown
                productlist.style.position='relative';
                console.log(productlist.getBoundingClientRect().top);
                productlist.style.left=`${rect.right}px`;
                console.log(rect.bottom);

                if(currentropdown!=null)
                {
                    currentropdown.style.left=`${rect.right}px`;
                }
            //adjustSelectedValuesPosition();
                
            }
            else
            {
                let productlist=document.getElementById('product-grid2');
                //productlist.style.top = `${boundryof.bottom}px`; // Position below the dropdown
                productlist.style.position='relative';
                productlist.style.left=`${20}px`;
                const selectedValuesDiv = document.getElementById('selectedValues'); 

                categoryDropdown.classList.remove('show-up');
                if(currentropdown!=null)
                {
                    productlist.style.top=`${100}px`;
                    currentropdown.style.left=`${0}px`;
                    selectedValuesDiv.style.position='relative';
                    selectedValuesDiv.style.left=`${0}px`;

                }else
                {
                    productlist.style.top=`${50}px`;
                }
              
            }
           //categoryDropdown.classList.toggle('show-up');
            lastOpenedDropdown = categoryDropdown; // Update the last opened dropdown

            let boundryof=document.getElementById('navbar').getBoundingClientRect();
            //dropdown.style.top=`${boundryof.bottom}px`;
             // Adjust selected values position based on last opened dropdown
        }
        



    function adjustSelectedValuesPosition() {
        const selectedValuesDiv = document.getElementById('selectedValues');
        const navbar = document.getElementById('navbar');
    
    
        if (lastOpenedDropdown!=null) {
        // Get the position and height of the last opened dropdown
        console.log("last dropdown not null");
        let boundryof=document.getElementById('navbar').getBoundingClientRect();
        const rect = lastOpenedDropdown.getBoundingClientRect();
        selectedValuesDiv.style.width = `calc(100vw - ${lastOpenedDropdown.getBoundingClientRect().right + window.scrollX}px)`;

        selectedValuesDiv.style.left = `${rect.right + window.scrollY + 10}px`; // Position below the dropdown
       // selectedValuesDiv.style.top= `${boundryof.bottom+ 10}px`;
        const clearBtn = document.getElementById('clearBtn');
        console.log(clearBtn);
        //selectedValuesDiv.style.display='block';

        
    }
}
    //TRIGGER TO DISPLAY sub category values
        function toggleSubcategory(id, categoryItem) {
            console.log("toggleSubcategory");
        const subcategoryList = document.getElementById(id);
        if (!subcategoryList) return; // Safeguard against null if the ID is incorrect

        // Hide all other subcategories
        document.querySelectorAll('.subcategory-list').forEach(list => {
            const otherIcon = list.previousElementSibling ? list.previousElementSibling.querySelector('.toggle-icon') : null;
            if (list !== subcategoryList) {
                list.classList.remove('show'); // Close other subcategories
                if (otherIcon) {
                    otherIcon.textContent = '+'; // Reset icon for closed subcategories
                    list.previousElementSibling.style.fontWeight = 'bold'; // Reset font for closed subcategories
                }
            }
        });

        // Toggle the clicked subcategory
        subcategoryList.classList.toggle('show'); // Toggle subcategory visibility

        // Get the toggle icon span for the clicked category
        const toggleIcon = categoryItem.querySelector('.toggle-icon');
        
        // Change icon and font weight for the clicked category
        if (subcategoryList.classList.contains('show')) {
            toggleIcon.textContent = '-';
            categoryItem.style.fontWeight = 'normal';
        } else {
            toggleIcon.textContent = '+';
            categoryItem.style.fontWeight = 'normal';
        }
        adjustSelectedValuesPosition();

    }
        // Function to update selected values
        function updateSelectedValues(valueToRemove) {
            console.log("updateSelectedValues");
            console.log('entered updatevalue');
    const selectedValuesDiv = document.getElementById('selectedValues');
    selectedValuesDiv.innerHTML = ''; // Clear current selections
    const color_options = document.querySelectorAll('.color-option:checked');
    const size_options = document.querySelectorAll('.size-option:checked');
    const clearBtn = document.getElementById('clearBtn');

    color_options.forEach(option => {
        let value=null;
        const optionValue = option.value.trim().toLowerCase();
        if(valueToRemove!=null)
        {
            value= valueToRemove.trim().toLowerCase();
        }
    
        if(optionValue!=value)
        {
        const selectedValueDiv = document.createElement('div');
        selectedValueDiv.classList.add('selected-value');
        selectedValueDiv.innerHTML = `${option.value} <span class="remove-btn" onclick="removeOption(this)">✖</span>`;
        selectedValuesDiv.appendChild(selectedValueDiv);
        }
    });
    size_options.forEach(option => {
        let value=null;
        const optionValue = option.value.trim().toLowerCase();
        if(valueToRemove!=null)
        {
            value= valueToRemove.trim().toLowerCase();
        }
    
        console.log(`Comparing option.value: '${optionValue}' with valueToRemove: '${value}'`);
    
        if (optionValue !== value) {
        const selectedValueDiv = document.createElement('div');
        selectedValueDiv.classList.add('selected-value');
        selectedValueDiv.innerHTML = `${option.value} <span class="remove-btn" onclick="removeOption(this)">✖</span>`;
        selectedValuesDiv.appendChild(selectedValueDiv);
            }
    });
            const priceValueDiv = document.createElement('div');
            priceValueDiv.classList.add('selected-value');
            let selectedPrice= document.getElementById('priceValue').innerText;
            
            console.log(selectedPrice);
            let value=null;
        const optionValue = selectedPrice.toString().trim().toLowerCase();
        if(valueToRemove!=null)
        {
            value= valueToRemove.trim().toLowerCase();
        }
    
        console.log(`Comparing option.value: '${optionValue}' with valueToRemove: '${value}'`);
    
        if (optionValue !== value) {
            priceValueDiv.innerHTML = `Price: ${selectedPrice} <span class="remove-btn" onclick="removePrice(this)">✖</span>`;
            console.log(priceValueDiv);
            selectedValuesDiv.appendChild(priceValueDiv);
            }
            const existingSelection = Array.from(selectedValuesDiv.children).find(child => child.textContent.trim().startsWith(selectedsubcategoryvalue));
        
            if (existingSelection) return;


         
        const selectedValueDiv = document.createElement('div');
        selectedValueDiv.classList.add('selected-value');
        selectedValueDiv.innerHTML = `${selectedsubcategoryvalue} <span class="remove-btn" onclick="removeOption(this)">✖</span>`;
        selectedValuesDiv.appendChild(selectedValueDiv);
        console.log(selectedsubcategoryvalue);

    // Show/hide selected values and clear button based on selected options
    if (size_options.length > 0 ||color_options.length > 0 || selectedPrice > 0||selectedsubcategoryvalue!=null) {
        selectedValuesDiv.style.display = 'flex'; // Show selected values
        clearBtn.style.display = 'flex'; // Show clear button
        console.log("contents now visible");
    } else {
        selectedValuesDiv.style.display = 'none'; // Hide selected values
        clearBtn.style.display = 'none'; // Hide clear button
    }
    filterProducts();
    adjustClearButtonPosition(selectedValuesDiv.getBoundingClientRect().bottom);
    
}
function addSelectedValue(value) {
    console.log("addSelectedValue");
    const selectedValuesDiv = document.getElementById('selectedValues');
    const categoryDropdown = document.getElementById('dropdown-contentforcategory');
    selectedsubcategoryvalue=value;

       updateSelectedValues(null);
}
function removePrice(event) {
    console.log(selectedPrice);

            selectedPrice = 500; // Reset the price to default
            document.getElementById('priceRange').value = 0; // Reset the range input
            const valueToRemove = event.target.parentElement.textContent.trim().slice(0, -1);
            console.log("Removing value:", valueToRemove);  // This will help you debug
        updateSelectedValues(valueToRemove.toString()); // Refresh selected values
        }

        // Function to remove a selected option
        function removeOption(element) {
            const valueToRemove = element.parentElement.textContent.trim().slice(0, -1);
            console.log("Removing value:", valueToRemove);  // This will help you debug
            const checkboxes = document.querySelectorAll(`input[type="checkbox"][value="${valueToRemove}"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = false; // Uncheck the checkbox
            });
            //updateFilters(itemType, itemValue);
            updateSelectedValues(valueToRemove); // Update the displayed selected values
        }
    
        // Function to clear all selections
        function clearSelections() {
            const checkboxes = document.querySelectorAll('.option');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false; // Uncheck all checkboxes
            });
            const selectedValuesDiv = document.getElementById('selectedValues');
            selectedValuesDiv.innerHTML = ''; // Clear current selections
            selectedValuesDiv.style.display='none';
           
        }
        function renderProductGrid(products)
        {
        const productGrid = document.getElementById('product-grid2');
        productGrid.innerHTML = ''; // Clear any existing content
        productGrid.style.display = 'grid';
        productGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
        productGrid.style.gap = '20px';
        

        console.log("hhjkjkkjkh",products.length);
        if (Array.isArray(products) && products.length > 0) {
            products.forEach(product => {
                const stars=convertRatingToStars(product.rating);
                const productCard = document.createElement('div'); // Create product card element
                productCard.classList.add('product-card');
            
                // Set inner HTML for product card
                productCard.innerHTML = `
                    <div class="image-container">
                        <img src="${product.image_url}" alt="${product.name}" class="image1">
                        <img src="${product.image2}" alt="${product.name}" class="image2">
                    </div>
                    <h3>${product.name}</h3>
                    <p>${stars}</p>
                    <p>$${product.price}</p>
                    
                    <div class="icon-container">
                        <span class="icon favorite-icon" onclick="toggleFavorite(event, this);" data-product-id="${product.product_id}">♡</span>
                        <span class="icon cart-icon" onclick="toggleCart(event, this);" data-product-id="${product.product_id}">🛒</span>
                    </div>
                `;
            
                // Click event for product card to redirect
                productCard.onclick = function() {
                    window.location.href = `backupofproduct.html?id=${product.product_id}`;
                };
            
                // Append product card to grid
                productGrid.appendChild(productCard);
            });
        }
    }
   
        
        function convertRatingToStars(rating) {
            rating = Math.max(1, Math.min(5, rating)); // Ensure rating is between 1 and 5
            let stars = '';
            for (let i = 0; i < rating; i++) {
                stars += '⭐'; // Append star symbol for each rating point
            }
            return stars;
        }
    

    function toggleFavorite(event,icon) {
        event.stopPropagation();
        const productId = icon.getAttribute('data-product-id'); // Get product ID
        const userid = localStorage.getItem('username');
        if(userid==='null')
            {
                 alert("Please login to select favourite");
                 return;
                 
            }
        if (productId) {
            addToWishlist(productId);
            //addToWishlist(icon,data.message,data.count);
            }
    }
    

    
    function toggleCart(event, icon) {
        event.stopPropagation(); // Prevent the click from bubbling up to the product card
        console.log("Toggle Cart Function Called"); // Debugging log
        const productId = icon.getAttribute('data-product-id'); // Get product ID
        const userid = localStorage.getItem('username');
        if (userid === 'null' || !userid) {
            alert("Please login to select cart");
            return;
        }
        if (productId) {
            updateCartQuantity(productId);
        }
    }
    window.onscroll = function() {
        // Use scrollTop as an alternative to pageYOffset
        const scrollPosition = document.documentElement.scrollTop || document.body.scrollTop;
    
    let navbarInitialTop=document.getElementById('navbar');
        if (scrollPosition > navbarInitialTop.offsetTop) {
            currentropdown.style.position='relative';
        
    } else {
        currentropdown.style.position = ''; // Reset position
    }
}

async function filterProducts() {
    // Get selected options
    //const color = Array.from(document.querySelectorAll('.option:checked'))
        //.map(checkbox => checkbox.value);
        const selectedCategories = ["Pants"];
    const selectedColors = Array.from(document.querySelectorAll('.color-option:checked'))
        .map(checkbox => checkbox.value);
    const selectedSizes = Array.from(document.querySelectorAll('.size-option:checked'))
        .map(checkbox => checkbox.value);
    const selectedSubcategory = selectedsubcategoryvalue;
    const maxPrice = selectedPrice;


    
        try {
            const response =  await fetch('filter_products.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                categories: selectedCategories,
                sizes: selectedSizes,
                colors: selectedColors,
                maxPrice: maxPrice }),
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');

        }

        const data = await response.json();
        if(!data.success)
        {  const productGrid = document.getElementById('product-grid2');
            productGrid.innerHTML = '<p>No Products are available with this filter</p>'; // Clear any existing content}
        }
        else{
        renderProductGrid(data.products);
        }
    } catch (error) {
        console.error('Error fetching product details:', error.message);

        productListContainer.innerHTML = '<p>Error fetching products. Please try again later.</p>';
    }
    displayProducts(data); // Display filtered products
}
// Attach event listener to the container that holds the selected items
document.getElementById('selected-values').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('close-btn')) {
        // Find the selected item (category, size, or color)
        const selectedItem = e.target.parentElement;

        // Get the type (category, size, or color) and value to remove
        const itemType = selectedItem.getAttribute('data-type');
        const itemValue = selectedItem.getAttribute('data-value');

        // Remove the selected item from the UI
        selectedItem.remove();

        // Call a function to update the filter (this will be based on your specific logic)
        updateFilters(itemType, itemValue);
    }
});

// Update filters based on the removed value
function updateFilters(type, value) {
    // Assuming filters are stored in an object
    if (type === 'category') {
        selectedCategories = selectedCategories.filter(item => item !== value);
    } else if (type === 'size') {
        selectedSizes = selectedSizes.filter(item => item !== value);
    } else if (type === 'color') {
        selectedColors = selectedColors.filter(item => item !== value);
    }

    // After updating the filter values, you can call your API to fetch the new results
    fetchFilteredProducts();
}

// Example function to update the product list after removing the filter
function fetchFilteredProducts() {
    // Your code to fetch products based on the updated filters
    console.log('Updated filters:', selectedCategories, selectedSizes, selectedColors);
    // Call the fetch or AJAX request to fetch products with the new filters
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

function addToWishlist(productId) {
   
    fetch('UpdateWishlist.php', {
        method: 'POST',
        headers: {
         'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `product_id=${productId}&user_id=${localStorage.getItem('username')},`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Product added to your wishlist!");
        } else {
            alert("Failed to add product to wishlist: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error adding product to wishlist: ", error);
        alert("An error occurred. Please try again.");
    });
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




    

    


