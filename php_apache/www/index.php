<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Récupérer les variables d'environnement
$servername = getenv('MYSQL_HOST') ?? null;
$username = getenv('MYSQL_USER') ?? null;
$password = getenv('MYSQL_PASSWORD') ?? null;
$dbname = getenv('MYSQL_DATABASE')  ?? null;

// Vérifier que toutes les variables nécessaires sont définies
if (!$servername || !$username || !$password || !$dbname) {
    die("Erreur : Une ou plusieurs variables d'environnement sont manquantes.");
}


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter values
$store_filter = isset($_GET['store']) ? $_GET['store'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : '';

// Build query
$query = "SELECT * FROM watch WHERE 1=1";
if ($store_filter) {
    $query .= " AND stores = '$store_filter'";
}
if ($min_price) {
    $query .= " AND CAST(REPLACE(REPLACE(price, '$', ''), ',', '') AS DECIMAL(10,2)) >= $min_price";
}
if ($max_price) {
    $query .= " AND CAST(REPLACE(REPLACE(price, '$', ''), ',', '') AS DECIMAL(10,2)) <= $max_price";
}
if ($brand_filter) {
    $query .= " AND brand = '$brand_filter'";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Watch Price Comparator</title>
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .product-card img {
            max-width: 200px;
            height: auto;
        }
        .filters {
            padding: 20px;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="filters">
        <form method="GET">
            <select name="store">
                <option value="">All Stores</option>
                <?php
                $stores = $conn->query("SELECT DISTINCT stores FROM watch");
                while($store = $stores->fetch_assoc()) {
                    echo "<option value='" . $store['stores'] . "'" . 
                         ($store['stores'] == $store_filter ? " selected" : "") . ">" . 
                         $store['stores'] . "</option>";
                }
                ?>
            </select>
            
            <select name="brand">
                <option value="">All Brands</option>
                <?php
                $brands = $conn->query("SELECT DISTINCT brand FROM watch");
                while($brand = $brands->fetch_assoc()) {
                    echo "<option value='" . $brand['brand'] . "'" . 
                         ($brand['brand'] == $brand_filter ? " selected" : "") . ">" . 
                         $brand['brand'] . "</option>";
                }
                ?>
            </select>
            
            <input type="number" name="min_price" placeholder="Min Price" value="<?php echo $min_price; ?>">
            <input type="number" name="max_price" placeholder="Max Price" value="<?php echo $max_price; ?>">
            
            <button type="submit">Filter</button>
        </form>
    </div>

    <div class="product-grid">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='product-card'>";
                echo "<img src='" . $row['image_url'] . "' alt='" . $row['model'] . "'>";
                echo "<h3>" . $row['brand'] . " " . $row['model'] . "</h3>";
                echo "<p>Price: " . $row['price'] . " " . $row['currency'] . "</p>";
                echo "<p>Store: " . $row['stores'] . "</p>";
                echo "<a href='product.php?id=" . $row['id'] . "'><button>View Details</button></a>";
                echo "</div>";
            }
        } else {
            echo "No products found";
        }
        ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>