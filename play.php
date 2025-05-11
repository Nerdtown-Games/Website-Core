<?php
// Assuming you have already set up the game data in the previous file
$folderName = isset($_GET['game']) ? trim($_GET['game']) : '';
if (!empty($folderName) && !preg_match('/^play\/[a-zA-Z0-9_\-]+$/', $_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '.php') !== false) {
    header("Location: /play/$folderName", true, 301); // Permanent redirect
    exit();
}

// Basic validation to prevent directory traversal
if (preg_match('/[^a-zA-Z0-9_\-]/', $folderName)) {
    echo renderErrorPage("Game not found");
    exit();
}

$gameFilePath = "games/$folderName/index.html";
$gameDataPath = "games/$folderName/game.json";

if (!file_exists($gameDataPath)) {
    echo renderErrorPage("Game not found");
    exit();
}

$gameData = json_decode(file_get_contents($gameDataPath), true);

if (!$gameData) {
    echo renderErrorPage("Game not found");
    exit();
}

// Prepare game data for display
$name = htmlspecialchars($gameData['name']);
$creator = !empty($gameData['creator']) ? htmlspecialchars($gameData['creator']) : 'Unknown';
$description = !empty($gameData['description']) 
    ? htmlspecialchars($gameData['description'], ENT_QUOTES, 'UTF-8') 
    : 'No description available.';

// Revert ' to a real apostrophe
$description = str_replace("'", "'", $description);

// Likes and Dislikes Data
$likes = isset($gameData['likes']) ? (int)$gameData['likes'] : 0;
$dislikes = isset($gameData['dislikes']) ? (int)$gameData['dislikes'] : 0;
$totalVotes = $likes + $dislikes;
$likesPercentage = $totalVotes > 0 ? ($likes / $totalVotes) * 100 : 0;
$dislikesPercentage = $totalVotes > 0 ? ($dislikes / $totalVotes) * 100 : 0;

// Truncate the description for meta tags to 120 characters and append '...' if necessary
$metaDescription = strlen($description) > 120 ? substr($description, 0, 120) . '...' : $description;

// Format the date to DD/MM/YYYY
$date_added = !empty($gameData['date_added']) 
    ? (new DateTime($gameData['date_added']))->format('d/m/Y') 
    : 'Date Unavailable';

$imageURL = "/files/images/icons/" . urlencode($folderName) . ".jpg";

// Function to render error page
function renderErrorPage($message) {
    return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="stylesheet" href="/files/css/error.css">
</head>
<body>
    <div class="container">
        <div class="error-box">
            <h1>Error 404</h1>
            <h2>' . htmlspecialchars($message) . '</h2>
            <p>Please check the game name or try again later.</p>
            <a href="/" class="home-button">Go to Homepage</a>
        </div>
    </div>
</body>
</html>';
}

// Normal page rendering continues here...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/files/css/index.css">
    <link rel="stylesheet" href="/files/css/mouse.css">
    <link rel="stylesheet" href="/files/css/play.css">
    <base href="/">
    <title>Play <?php echo $name; ?></title>
</head>
<body>

<div class="container">
    <div class="home-button">
        <a href="/"><img id="home-icon" draggable="false" src="/files/images/ui/home.png" alt="Home"></a>
    </div>

    <div id="iframe-container">
        <div id="controls">
            <a id="share" class="controls" onclick="copyToClipboard()" draggable="false">
                <img class="controls" draggable="false" src="<?= file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/files/images/ui/share.txt'); ?>" alt="Share" style="height: 24px; width: 24px;">
            </a>
            <a id="fullscreen" class="controls" draggable="false">
                <img class="controls" draggable="false" src="<?= file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/files/images/ui/fullscreen.txt'); ?>" alt="Fullscreen">
            </a>
            <a id="minimize" class="controls" draggable="false">
                <img class="controls" draggable="false" src="<?= file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/files/images/ui/minimize.txt'); ?>" alt="Minimize">
            </a>
        </div>

        <div id="loading">Loading...</div>
        <iframe src="<?php echo $gameFilePath; ?>" frameborder="0"></iframe>
    </div>
    <div id="notification" class="notification" style="display:none;"></div>
    
    <div class="creator-details">
        <div class="creator-name"><?php echo $name; ?></div>
        <div>by <a class="creator-studio" href="/creator/<?php echo urlencode(str_replace(' ', '_', $creator)); ?>"><?php echo htmlspecialchars($creator); ?></a></div>
        <div>Date Added: <?php echo $date_added; ?></div>
        <div class="game-description">
            <p><p><?php echo nl2br(html_entity_decode(str_replace('\\n', "\n", $description), ENT_QUOTES, 'UTF-8')); ?></p>
            </p>
            </p>
        </div>
    </div>




</div>


<script>
        document.addEventListener('DOMContentLoaded', function () {
            const fullscreenButton = document.getElementById('fullscreen');
            const minimizeButton = document.getElementById('minimize');
            const iframeContainer = document.getElementById('iframe-container');
            const container = document.querySelector('.container');
            const loadingIndicator = document.getElementById('loading');
            const iframe = iframeContainer.querySelector('iframe');

            // Show loading indicator when the iframe is loading
            iframe.addEventListener('load', function() {
                loadingIndicator.style.display = 'none';
                // Focus the iframe when it is loaded
                iframe.contentWindow.focus();
            });

            fullscreenButton.addEventListener('click', function () {
                iframeContainer.classList.add('fullscreen-mode');
                container.classList.add('fullscreen-mode');

                minimizeButton.style.display = 'block';
                fullscreenButton.style.display = 'none';

                // Focus the iframe when entering fullscreen
                iframe.contentWindow.focus();
            });

            minimizeButton.addEventListener('click', function () {
                iframeContainer.classList.remove('fullscreen-mode');
                container.classList.remove('fullscreen-mode');

                minimizeButton.style.display = 'none';
                fullscreenButton.style.display = 'block';
            });
        });

    function copyToClipboard() {
        var text = window.location.href;
        navigator.clipboard.writeText(text).then(function() {
            showNotification('URL copied to clipboard!');
        }).catch(function() {
            showNotification('Failed to copy URL.');
        });
    }

    function showNotification(message) {
        var notification = document.getElementById('notification');
        notification.textContent = message;
        notification.style.display = 'block';
        setTimeout(function() {
            notification.style.display = 'none';
        }, 3000);
    }
</script>
</body>
</html>