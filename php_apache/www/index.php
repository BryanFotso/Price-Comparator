<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = getenv('MYSQL_HOST') ?? null;
$username = getenv('MYSQL_USER') ?? null;
$password = getenv('MYSQL_PASSWORD') ?? null;
$dbname = getenv('MYSQL_DATABASE')  ?? null;

if (!$servername || !$username || !$password || !$dbname) {
    die("Erreur : Une ou plusieurs variables d'environnement sont manquantes.");
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$store_filter = isset($_GET['store']) ? $_GET['store'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : '';
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : '';

$query = "SELECT * FROM watch WHERE 1=1";
if ($store_filter) {
    $query .= " AND stores = ?";
}
if ($min_price) {
    $query .= " AND CAST(REPLACE(REPLACE(REPLACE(price, '€', ''), ' ', ''), ',', '.') AS DECIMAL(10,2)) >= ?";
}
if ($max_price) {
    $query .= " AND CAST(REPLACE(REPLACE(REPLACE(price, '€', ''), ' ', ''), ',', '.') AS DECIMAL(10,2)) <= ?";
}
if ($brand_filter) {
    $query .= " AND brand = ?";
}

if ($sort_order == 'asc') {
    $query .= " ORDER BY CAST(REPLACE(REPLACE(REPLACE(TRIM(price), '€', ''), ' ', ''), ',', '.') AS DECIMAL(10,2)) ASC";
} else {
    $query .= " ORDER BY CAST(REPLACE(REPLACE(REPLACE(TRIM(price), '€', ''), ' ', ''), ',', '.') AS DECIMAL(10,2)) DESC";
}

$stmt = $conn->prepare($query);

if ($stmt) {
    $types = '';
    $params = array();
    
    if ($store_filter) {
        $types .= 's';
        $params[] = $store_filter;
    }
    if ($min_price) {
        $types .= 'd';
        $params[] = floatval($min_price);
    }
    if ($max_price) {
        $types .= 'd';
        $params[] = floatval($max_price);
    }
    if ($brand_filter) {
        $types .= 's';
        $params[] = $brand_filter;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Erreur de préparation de la requête");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Comparateur de Prix de Montres</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f5f5f5;
        }
        
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .filters {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .filters form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }
        
        select, input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            min-width: 150px;
        }
        
        .sort-buttons {
            display: flex;
            gap: 10px;
        }
        
        .sort-buttons label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
        }
        
        .sort-buttons input[type="radio"] {
            display: none;
        }
        
        .sort-buttons label:hover {
            border-color: #3498db;
        }
        
        .sort-buttons input[type="radio"]:checked + span {
            color: #3498db;
            font-weight: bold;
        }
        
        select {
            background-color: white;
            cursor: pointer;
        }
        
        select:hover {
            border-color: #3498db;
        }
        
        button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            background-color: #f8f9fa;
            padding: 10px;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-info h3 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .price {
            font-size: 18px;
            color: #2c3e50;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .store {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .description {
            margin: 10px 0;
            color: #34495e;
            font-size: 14px;
            line-height: 1.4;
            height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .view-details {
            display: block;
            background-color: #3498db;
            color: white;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            margin-top: 10px;
            border-radius: 4px;
        }
        
        .view-details:hover {
            background-color: #2980b9;
        }
        
        .no-products {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comparateur de Prix de Montres</h1>
    </div>

    <div class="filters">
        <form method="GET">
            <select name="store">
                <option value="">Toutes les boutiques</option>
                <?php
                $stores = $conn->query("SELECT DISTINCT stores FROM watch");
                while($store = $stores->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($store['stores']) . "'" . 
                         ($store['stores'] == $store_filter ? " selected" : "") . ">" . 
                         htmlspecialchars($store['stores']) . "</option>";
                }
                ?>
            </select>
            
            <select name="brand">
                <option value="">Toutes les marques</option>
                <?php
                $brands = $conn->query("SELECT DISTINCT brand FROM watch");
                while($brand = $brands->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($brand['brand']) . "'" . 
                         ($brand['brand'] == $brand_filter ? " selected" : "") . ">" . 
                         htmlspecialchars($brand['brand']) . "</option>";
                }
                ?>
            </select>
            
            <input type="number" name="min_price" placeholder="Prix minimum" value="<?php echo $min_price !== null ? htmlspecialchars($min_price) : ''; ?>">
            <input type="number" name="max_price" placeholder="Prix maximum" value="<?php echo $max_price !== null ? htmlspecialchars($max_price) : ''; ?>">
            
            <div class="sort-buttons">
                <label>
                    <input type="radio" name="sort" value="asc" <?php echo $sort_order === 'asc' ? 'checked' : ''; ?>>
                    <span>Prix ↑</span>
                </label>
                <label>
                    <input type="radio" name="sort" value="desc" <?php echo $sort_order !== 'asc' ? 'checked' : ''; ?>>
                    <span>Prix ↓</span>
                </label>
            </div>
            
            <button type="submit">Filtrer</button>
            <?php if ($store_filter || $min_price || $max_price || $brand_filter): ?>
                <a href="index.php" style="color: #7f8c8d; text-decoration: none;">Réinitialiser les filtres</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="product-grid">
        <?php
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='product-card'>";
                echo "<img src='" . ($row['image_url'] !== null ? htmlspecialchars($row['image_url']) : '') . "' alt='" . ($row['model'] !== null ? htmlspecialchars($row['model']) : '') . "'>";
                echo "<div class='product-info'>";
                echo "<h3>" . htmlspecialchars(($row['brand'] ?? '') . ' ' . ($row['model'] ?? '')) . "</h3>";
                echo "<div class='price'>" . htmlspecialchars(($row['price'] ?? '') . ' ' . ($row['currency'] ?? '')) . "</div>";
                echo "<div class='store'>Vendu par " . ($row['stores'] !== null ? htmlspecialchars($row['stores']) : 'Non spécifié') . "</div>";
                if ($row['description'] !== null) {
                    echo "<div class='description'>" . htmlspecialchars(substr($row['description'], 0, 100)) . "...</div>";
                }
                echo "<a href='product.php?id=" . $row['id'] . "' class='view-details'>Voir les détails</a>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<div class='no-products'>Aucun produit trouvé</div>";
        }
        ?>
    </div>
</body>
</html>

<?php
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>