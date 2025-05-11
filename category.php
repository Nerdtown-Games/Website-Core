<?php
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $path);
$category = end($segments);
$category = htmlspecialchars($category);
$category = str_replace('_', ' ', $category);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <title><?php echo $category; ?> Games</title>
    <link rel="stylesheet" href="/files/css/index.css">
    <link rel="stylesheet" href="/files/css/categories.css">
    <link rel="stylesheet" href="/files/css/mouse.css">
</head>
<body>

    <h1><?php echo $category; ?> Games</h1>
    <div class="search-box">
        <input type="text" id="search" placeholder="Search for <?php echo $category; ?> games" oninput="performSearch()">
    </div>
    <nav class="quick-nav">
            <a href="/" class="quick-link">All Games</a>
            <a href="/category/New" class="quick-link">New Games</a>
            <a href="/category/Popular" class="quick-link">Popular Games</a>
        </nav>
<div class="categories-container" id="categories-container"><a href="/category/Arcade"><div class="category-item" data-value="Arcade"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Arcade.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Arcade</span></div></a><a href="/category/Puzzle"><div class="category-item" data-value="Puzzle"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Puzzle.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Puzzle</span></div></a><a href="/category/Strategy"><div class="category-item" data-value="Strategy"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Strategy.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Strategy</span></div></a><a href="/category/Multiplayer"><div class="category-item" data-value="Multiplayer"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Multiplayer.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Multiplayer</span></div></a><a href="/category/Sports"><div class="category-item" data-value="Sports"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Sports.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Sports</span></div></a><a href="/category/Platformer"><div class="category-item" data-value="Platformer"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Platformer.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Platformer</span></div></a><a href="/category/IO"><div class="category-item" data-value="IO"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/IO.png" style="width: auto; height: 25px; margin-right: 10px;"><span>IO</span></div></a><a href="/category/Indie"><div class="category-item" data-value="Indie"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Indie.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Indie</span></div></a><a href="/category/Racing"><div class="category-item" data-value="Racing"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Racing.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Racing</span></div></a><a href="/category/Flash"><div class="category-item" data-value="Flash"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Flash.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Flash</span></div></a><a href="/category/Horror"><div class="category-item" data-value="Horror"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Horror.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Horror</span></div></a><a href="/category/Adventure"><div class="category-item" data-value="Adventure"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Adventure.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Adventure</span></div></a><a href="/category/Action"><div class="category-item" data-value="Action"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Action.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Action</span></div></a><a href="/category/Shooter"><div class="category-item" data-value="Shooter"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Shooter.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Shooter</span></div></a><a href="/category/Survival"><div class="category-item" data-value="Survival"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Survival.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Survival</span></div></a><a href="/category/Clicker"><div class="category-item" data-value="Clicker"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Clicker.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Clicker</span></div></a><a href="/category/Idle"><div class="category-item" data-value="Idle"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Idle.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Idle</span></div></a><a href="/category/Escape"><div class="category-item" data-value="Escape"><img alt="Game Category" width="auto" height="200" src="/files/images/categories/Escape.png" style="width: auto; height: 25px; margin-right: 10px;"><span>Escape</span></div></a></div>        <div class="game-grid" id="game-grid"></div>
    <div class="game-grid" id="game-grid">
    </div>
<script src="/files/js/search.js"></script>
<script>
function handleCategoryClick(event) {
    const categoryItem = event.target.closest('.category-item');
    if (categoryItem) {
        document.querySelectorAll('.category-item').forEach(item => item.classList.remove('selected'));
        categoryItem.classList.add('selected');
        const categoryValue = categoryItem.dataset.value;
        history.pushState(null, '', `/category/${encodeURIComponent(categoryValue)}`);
        performSearch();
    }
}
document.addEventListener('click', handleCategoryClick);
</script>
</body>
</html>
