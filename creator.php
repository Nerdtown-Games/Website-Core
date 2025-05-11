<?php
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $path);
$creator = end($segments);
$creator = htmlspecialchars($creator);
$creator = str_replace('_', ' ', $creator);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <title>Games by <?php echo $creator; ?></title>
    <link rel="stylesheet" href="/files/css/index.css">
    <link rel="stylesheet" href="/files/css/categories.css">
    <link rel="stylesheet" href="/files/css/mouse.css">

</head>
<body>

    <h1>Games by <?php echo $creator; ?></h1>
    <div class="home-button">
        <a href="/"><img id="home-icon" draggable="false" src="/files/images/ui/home.png" alt="Home"></a>
    </div>
    <div class="search-box">
        <input type="text" id="search" placeholder="Search for games by <?php echo $creator; ?>" oninput="performSearch()">
    </div>
    <div class="creators-container" id="creators-container"></div>
    <div class="game-grid" id="game-grid"></div>
    <script>
        const creator = '<?php echo $creator; ?>';
        function performSearch() {
            const query = document.getElementById('search').value;
            loadGames(query, creator);
        }
        window.onload = function() {
            loadGames('', creator);
            loadCreators();
        };
    </script>
    <script src="/files/js/search.js"></script>
</body>
</html>
