<?php
header('Content-Type: application/json');

// Get the search query, creator, likes, categories, and show parameters from the URL
$query = isset($_GET['q']) ? $_GET['q'] : '';
$creator = isset($_GET['creator']) ? $_GET['creator'] : '';
$likes = isset($_GET['likes']) ? $_GET['likes'] : '';
$categories = isset($_GET['category']) ? $_GET['category'] : '';
$show = isset($_GET['show']) ? $_GET['show'] : '';

// Convert categories to an array if it's a comma-separated string
$categoryArray = $categories !== '' ? explode(',', $categories) : [];

// Initialize arrays to hold the search results, unique categories, and unique creators
$results = [];
$uniqueCategories = [];
$uniqueCreators = []; // Track unique creators

// Directory where your game folders are located
$directory = __DIR__ . '/games'; // Current directory, adjust if needed

// File containing the names of games that should not be listed
$notWorkingFile = $directory . '/not_working';

// Get the current date in YYYY-MM-DD format
$currentDate = date('Y-m-d');

// Calculate the date 5 days ago
$fiveDaysAgo = date('Y-m-d', strtotime('-5 days'));

// Function to get the image date from game data
function getImageDate($gameData) {
    return isset($gameData['date_added']) ? $gameData['date_added'] : '';
}

// Load the list of games to exclude
$excludedGames = [];
if (file_exists($notWorkingFile)) {
    $excludedGames = file($notWorkingFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

// Open the directory
if ($handle = opendir($directory)) {
    // Loop through each item in the directory
    while (false !== ($entry = readdir($handle))) {
        $fullPath = $directory . '/' . $entry;

        // Check if the item is a directory and not the special entries or excluded games
        if ($entry != "." && $entry != ".." && is_dir($fullPath) && !in_array($entry, $excludedGames)) {
            // Path to the game's JSON file
            $jsonFilePath = $fullPath . '/game.json';
            if (file_exists($jsonFilePath)) {
                // Read and decode the JSON file
                $jsonData = file_get_contents($jsonFilePath);
                $gameData = json_decode($jsonData, true);

                // Check if the game data is valid
                if ($gameData) {
                    // Collect unique categories
                    if (isset($gameData['categories'])) {
                        foreach ($gameData['categories'] as $category) {
                            if (!in_array($category, $uniqueCategories)) {
                                $uniqueCategories[] = $category;
                            }
                        }
                    }

                    // Collect unique creators
                    if (isset($gameData['creator']) && !in_array($gameData['creator'], $uniqueCreators)) {
                        $uniqueCreators[] = $gameData['creator'];
                    }

                    // Initialize a flag to determine if this game should be included in the results
                    $include = true;

                    // Normalize query, creator, and category inputs
                    $normalizedQuery = str_replace('_', ' ', $query);
                    $normalizedCreator = str_replace('_', ' ', $creator);

                    // Check if the name matches the query
                    if ($query !== '' && stripos(str_replace('_', ' ', $gameData['name']), $normalizedQuery) === false) {
                        $include = false;
                    }

                    // Check if the creator matches the creator filter
                    if ($normalizedCreator !== '' && (!isset($gameData['creator']) || stripos(str_replace('_', ' ', $gameData['creator']), $normalizedCreator) === false)) {
                        $include = false;
                    }
                    if (in_array('Popular', $categoryArray)) {
                        $likes = 'up';
                    }
                    
                    // Check if the categories match the category filter
                    if (!empty($categoryArray)) {
                        if (in_array('New', $categoryArray)) {
                            if (getImageDate($gameData) < $fiveDaysAgo) {
                                $include = false;
                            }
                        } elseif (in_array('Popular', $categoryArray)) {
                            // Include only if 'likes' is set
                            if (!isset($gameData['likes'])) {
                                $include = false;
                            }
                        } elseif (!isset($gameData['categories']) || !array_intersect($categoryArray, $gameData['categories'])) {
                            $include = false;
                        }
                    }
                    

                    // If the game matches all criteria, include it in the results
                    if ($include) {
                        // Get the image date from the game data
                        $imageDate = getImageDate($gameData);

                        // Construct the image path based on the date added
                        if ($imageDate >= $fiveDaysAgo) {
                            $gameData['thumbnail'] = '/files/images/icons/new/' . $entry . '.jpg';
                        } else {
                            $gameData['thumbnail'] = '/files/images/icons/' . $entry . '.jpg';
                        }

                        // Add the folder name to the game data
                        $gameData['location'] = $entry;

                        // Add the game data to the results array
                        $results[] = $gameData;
                    }
                }
            }
        }
    }
    // Close the directory handle
    closedir($handle);
}

// Output unique categories if the show parameter is set to "categories"
if ($show === 'categories') {
    echo json_encode(array_values($uniqueCategories));
    exit; // Exit after outputting categories to avoid additional processing
}

// Output unique creators if the show parameter is set to "creators"
if ($show === 'creators') {
    echo json_encode(array_values($uniqueCreators));
    exit; // Exit after outputting creators to avoid additional processing
}

// Sort the results based on likes
if ($likes === 'up') {
    usort($results, function($a, $b) {
        $likesA = isset($a['likes']) ? (int)$a['likes'] : 0;
        $likesB = isset($b['likes']) ? (int)$b['likes'] : 0;
        return $likesB <=> $likesA; // Descending
    });
} elseif ($likes === 'down') {
    usort($results, function($a, $b) {
        $likesA = isset($a['likes']) ? (int)$a['likes'] : 0;
        $likesB = isset($b['likes']) ? (int)$b['likes'] : 0;
        return $likesA <=> $likesB; // Ascending
    });
} else {
    // If no likes sorting, sort alphabetically
    usort($results, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
}


// Encode the results as JSON and output it
echo json_encode($results);
?>
