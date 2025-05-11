// Function to get the selected category from the category divs
function getSelectedCategory() {
    let selectedCategory = document.querySelector('.category-item.selected');
    return selectedCategory ? selectedCategory.dataset.value : '';
}

// Function to get the creator from the URL query parameters
function getCreatorFromQuery() {
    const params = new URLSearchParams(window.location.search);
    return params.get('creator') || '';
}

// Redirect to creator.php if the URL contains the creator parameter
function handleRedirect() {
    const creator = getCreatorFromQuery();
    const currentPath = window.location.pathname;
    const pathSegments = currentPath.split('/').filter(Boolean);

    // Check if we're not already on creator.php
    if (pathSegments.length > 1) {
        const lastSegment = pathSegments[pathSegments.length - 1];
        
        if (pathSegments[pathSegments.length - 2] === 'creator' && creator) {
            // Redirect to creator.php if it’s a creator
            window.location.href = `creator.php?creator=${encodeURIComponent(creator)}`;
        }
    }
}

// Function to fetch and display categories
function loadCategories() {
    const container = document.getElementById('categories-container');

    if (container) {
        fetch('search.php?show=categories')
            .then(response => response.json())
            .then(categories => {
                container.innerHTML = ''; // Clear existing categories

                const allDiv = document.createElement('a'); // Create an anchor element
                allDiv.className = 'category-item';
                allDiv.dataset.value = ''; // No value for 'All'
                allDiv.textContent = 'All Games';
                allDiv.href = '/'; // Set the href to the home page URL
                allDiv.style.display = 'block'; // Ensure it behaves like a block element
                container.appendChild(allDiv);

                const newdiv = document.createElement('a'); // Create an anchor element
                newdiv.className = 'category-item';
                newdiv.dataset.value = ''; // No value for 'All'
                newdiv.textContent = 'New Games';
                newdiv.href = '/category.php?category=New'; // Set the href to the home page URL
                newdiv.style.display = 'block'; // Ensure it behaves like a block element
                container.appendChild(newdiv);
                

                // Create and append categories
                categories.forEach(category => {
                    const div = document.createElement('div');
                    div.className = 'category-item';
                    div.dataset.value = category; // Store category value
                
                    // Create and append image
                    const img = document.createElement('img');
                    img.alt = "Game Category"
                    img.style.width = 'auto'; // Adjust as needed
                    img.style.height = '25px';
                    img.setAttribute('width', 'auto');
                    img.setAttribute('height', '200');
                    img.style.marginRight = '10px'; // Space between image and text
                
                    // Set image source and handle fallback
                    img.src = `/files/images/categories/${category}.png`; // Try PNG first
                    img.onerror = () => {
                        img.src = `/files/images/categories/error_404.png`; // Fallback if PNG fails
                    };
                    div.appendChild(img);
                
                    // Create and append text
                    const text = document.createElement('span');
                    text.textContent = category;
                    div.appendChild(text);
                
                    // Create a link element
                    const link = document.createElement('a');
                    link.href = `/category.php?category=${encodeURIComponent(category)}`; // Link to the category page
                    link.appendChild(div); // Append the category div to the link
                
                    // Append the link to the container
                    container.appendChild(link);
                });
                
            })
            .catch(error => console.error('Error fetching categories:', error));
    } else {
        console.log('Categories container not found.');
    }
}

// Function to perform the search and update the game grid or redirect
function performSearch() {
    const searchQuery = document.getElementById('search').value.trim();
    
    // Get the path segments
    const pathSegments = window.location.pathname.split('/').filter(Boolean);
    const timestamp = Math.floor(Date.now() / 1000);
    let queryString = `search.php?t=${timestamp}&q=${encodeURIComponent(searchQuery)}`;
    // Determine if the last path segment is a category or a creator
    if (pathSegments.length > 1) {
        const lastSegment = pathSegments[pathSegments.length - 1];
        
        if (pathSegments[pathSegments.length - 2] === 'category') {
            // If it's a category, append it to the query string
            queryString += `&category=${encodeURIComponent(lastSegment)}`;
        } else if (pathSegments[pathSegments.length - 2] === 'creator') {
            // If it's a creator, append it to the query string
            queryString += `&creator=${encodeURIComponent(lastSegment)}`;
        }
    }

    // Create a new XMLHttpRequest
    var xhr = new XMLHttpRequest();
    xhr.open('GET', queryString, true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            var games = JSON.parse(xhr.responseText);
            var gameGrid = document.getElementById('game-grid');
    
            if (gameGrid) {
                gameGrid.innerHTML = '';
            
                if (games.length > 0) {
                    games.forEach(function(game, index) {
                        // Insert ad every 4th item
                        if (index % 0 === 0) {
                            var adBox = document.createElement('div');
                            adBox.className = 'game-item advertisement';
                            
                            // Create iframe for ad
                            var iframe = document.createElement('iframe');
                            iframe.width = "200";
                            iframe.height = "200";
                            iframe.style.border = "0";
                            iframe.style.overflow = "hidden";
                            iframe.referrerPolicy = "no-referrer-when-downgrade";
                            iframe.sandbox = "allow-scripts allow-same-origin";
                            iframe.srcdoc = ``;
                            
                            // Create ad title
                            var adTitle = document.createElement('h2');
                            adTitle.textContent = "Advertisement";
                            
                            adBox.appendChild(iframe);
                            adBox.appendChild(adTitle);
                            gameGrid.appendChild(adBox);
                        }
            
                        // Create game item
                        var gameItem = document.createElement('a');
                        gameItem.className = 'game-item';
                        gameItem.href = 'play.php?game=' + encodeURIComponent(game.location);
            
                        // Create and append image
                        var image = document.createElement('img');
                        image.dataset.src = game.thumbnail;
                        image.alt = "Game Thumbnail";
                        image.setAttribute('width', '200');
                        image.setAttribute('height', '200');
                        image.style.maxWidth = '200px';
                        image.style.height = '200px';
                        image.src = "/files/images/ui/placeholder-icon.jpg"; // Optional placeholder image
                        observer.observe(image);
            
                        // Create and append title
                        var title = document.createElement('h2');
                        title.textContent = game.name;
                        
                        gameItem.appendChild(image);
                        gameItem.appendChild(title);
                        
                        // Append game item to the grid
                        gameGrid.appendChild(gameItem);
                    });
                } else {
                    gameGrid.innerHTML = '<p>No games found.</p>';
                }
            }
            
             else {
                console.error('Game grid not found.');
            }
        } else {
            console.error('Failed to fetch game data:', xhr.status, xhr.statusText);
        }
    };
    
    xhr.onerror = function() {
        console.error('Request failed');
    };
    
    xhr.send();
    
    // Intersection Observer for Lazy Loading
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src; // Load the actual image
                img.removeAttribute('data-src'); // Cleanup
                observer.unobserve(img);
            }
        });
    });
}    

// Function to handle category click
function handleCategoryClick(event) {
    const categoryItem = event.target.closest('.category-item');

    if (categoryItem) {
        // Deselect all categories
        document.querySelectorAll('.category-item').forEach(item => item.classList.remove('selected'));

        // Select the clicked category
        categoryItem.classList.add('selected');

        // Perform search after category selection changes
        performSearch();
    }
}

// Attach event listeners to trigger search on input change and category selection
document.getElementById('search').addEventListener('input', performSearch);

// Attach event listener to category items using event delegation
document.addEventListener('click', handleCategoryClick);

// Load categories on page load
window.onload = function() {
    // loadCategories();
    handleRedirect(); // Check and handle redirection on page load
};

// Initial search to display games if needed
performSearch();
