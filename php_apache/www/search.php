<?php
$query = $_GET['query'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$store = $_GET['store'] ?? '';
$dimensions = $_GET['dimensions'] ?? '';

if (empty($query)) {
    header('Location: index.php');
    exit;
}

// Liste des modèles Rolex
$rolexModels = [
    "Day-Date",
    "1908",
    "Air-King",
    "Cosmograph Daytona",
    "Datejust",
    "Deepsea",
    "Explorer",
    "GMT-Master II",
    "Lady-Datejust",
    "Oyster Perpetual",
    "Sea-Dweller",
    "Sky-Dweller",
    "Submariner",
    "Yacht-Master"
];

// Fonction simple pour trouver le modèle dans la requête
function findModelInQuery($query, $models) {
    $query = strtolower($query);
    foreach ($models as $model) {
        if (strpos($query, strtolower($model)) !== false) {
            return $model;
        }
    }
    return null;
}

// Trouver le modèle dans la requête
$search_model = findModelInQuery($query, $rolexModels);

$servername = getenv('MYSQL_HOST') ?? null;
$username = getenv('MYSQL_USER') ?? null;
$password = getenv('MYSQL_PASSWORD') ?? null;
$dbname = getenv('MYSQL_DATABASE') ?? null;

$db = new mysqli($servername, $username, $password, $dbname);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Construction de la requête SQL
$sql = "WITH RankedWatches AS (
    SELECT *,
           ROW_NUMBER() OVER (PARTITION BY stores ORDER BY CAST(REPLACE(REPLACE(price, '€', ''), ' ', '') AS DECIMAL)) as rn
    FROM watch
    WHERE category = ?
    AND price IS NOT NULL 
    AND price != ''
    AND price REGEXP '^[0-9]'
";

$params = [$search_model];
$types = "s";

if (!empty($min_price)) {
    $sql .= " AND CAST(REPLACE(REPLACE(price, '€', ''), ' ', '') AS DECIMAL) >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if (!empty($max_price)) {
    $sql .= " AND CAST(REPLACE(REPLACE(price, '€', ''), ' ', '') AS DECIMAL) <= ?";
    $params[] = $max_price;
    $types .= "d";
}

if (!empty($store)) {
    $sql .= " AND stores = ?";
    $params[] = $store;
    $types .= "s";
}

if (!empty($dimensions)) {
    $sql .= " AND dimensions = ?";
    $params[] = $dimensions;
    $types .= "s";
}

$sql .= ") SELECT * FROM RankedWatches WHERE rn <= 5  ORDER BY stores, CAST(REPLACE(REPLACE(price, '€', ''), ' ', '') AS DECIMAL)";

$stmt = $db->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$watches = [];
$stores = [];
$dimensions_list = [];

while ($row = $result->fetch_assoc()) {
    if (!in_array($row['stores'], $stores)) {
        $stores[] = $row['stores'];
    }
    if (!in_array($row['dimensions'], $dimensions_list) && !empty($row['dimensions'])) {
        $dimensions_list[] = $row['dimensions'];
    }
    $watches[] = $row;
}

$stmt->close();
$db->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche - <?php echo htmlspecialchars($query); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/search.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="logo-container">
                <a href="index.php">
                    <img src="images rolex/logo rolex.png" alt="Rolex Logo">
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Zone de recherche -->
        <div class="search-container">
            <form action="search.php" method="GET" class="d-flex justify-content-center align-items-center gap-3">
                <input type="text" name="query" class="form-control search-input flex-grow-1" 
                       value="<?php echo htmlspecialchars($query); ?>" 
                       placeholder="Rechercher une montre Rolex...">
                <button class="btn search-btn" type="submit">
                    <i class="fas fa-search me-2"></i>Rechercher
                </button>
            </form>
        </div>

        <!-- Zone de filtres -->
        <div class="filters-container">
            <h4 class="filter-title">
                <i class="fas fa-filter"></i>
                Filtrer les résultats
            </h4>
            <form action="search.php" method="GET" class="row g-4">
                <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
                
                <div class="col-md-3">
                    <label class="form-label">Prix minimum</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                        <input type="number" name="min_price" class="form-control" 
                               value="<?php echo htmlspecialchars($min_price); ?>" 
                               placeholder="Min">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Prix maximum</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                        <input type="number" name="max_price" class="form-control" 
                               value="<?php echo htmlspecialchars($max_price); ?>" 
                               placeholder="Max">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Boutique</label>
                    <select name="store" class="form-select">
                        <option value="">Toutes les boutiques</option>
                        <?php foreach ($stores as $s): ?>
                            <option value="<?php echo htmlspecialchars($s); ?>" 
                                    <?php echo $store === $s ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Dimensions</label>
                    <select name="dimensions" class="form-select">
                        <option value="">Toutes les dimensions</option>
                        <?php foreach ($dimensions_list as $d): ?>
                            <option value="<?php echo htmlspecialchars($d); ?>" 
                                    <?php echo $dimensions === $d ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($d); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Appliquer les filtres
                    </button>
                </div>
            </form>
        </div>

        <!-- Résultats -->
        <h2 class="mb-4">Résultats pour "<?php echo htmlspecialchars($query); ?>"</h2>
        
        <?php if (empty($watches)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Aucune montre trouvée pour votre recherche.
            </div>
        <?php else: ?>
            <?php
            $current_store = '';
            foreach ($watches as $watch):
                if ($current_store !== $watch['stores']):
                    if ($current_store !== '') echo '</div>'; // Fermer la rangée précédente
                    $current_store = $watch['stores'];
                    echo '<h3 class="mt-4 mb-3">' . htmlspecialchars($watch['stores']) . '</h3>';
                    echo '<div class="row g-4">';
                endif;
            ?>
                <div class="col-md-4">
                    <div class="card result-card">
                        <div class="watch-image-container">
                            <?php if ($watch['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($watch['image_url']); ?>" 
                                     class="watch-image" 
                                     alt="<?php echo htmlspecialchars($watch['model']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($watch['model']); ?></h5>
                            <p class="price"><?php echo htmlspecialchars($watch['price']); ?></p>
                            <span class="store-badge"><?php echo htmlspecialchars($watch['stores']); ?></span>
                            <?php if ($watch['dimensions']): ?>
                                <p class="mt-2 mb-0">
                                    <i class="fas fa-ruler me-2"></i>
                                    Dimensions: <?php echo htmlspecialchars($watch['dimensions']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($watch['source']): ?>
                                <a href="<?php echo htmlspecialchars($watch['source']); ?>" 
                                   class="btn btn-outline-success mt-3" 
                                   target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    Voir sur le site
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php 
            endforeach;
            if (!empty($watches)) echo '</div>'; // Fermer la dernière rangée
            ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 