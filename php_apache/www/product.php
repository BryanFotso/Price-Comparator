<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = getenv('MYSQL_HOST') ?? null;
$username = getenv('MYSQL_USER') ?? null;
$password = getenv('MYSQL_PASSWORD') ?? null;
$dbname = getenv('MYSQL_DATABASE') ?? null;

if (!$servername || !$username || !$password || !$dbname) {
    die("Erreur : Une ou plusieurs variables d'environnement sont manquantes.");
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM watch WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $product['brand'] . ' ' . $product['model']; ?> - Détails</title>
    <style>
        .product-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        .product-image {
            width: 100%;
            max-width: 500px;
            height: auto;
        }
        .product-info {
            padding: 20px;
        }
        .price {
            font-size: 24px;
            color: #2c3e50;
            margin: 20px 0;
        }
        .buy-button {
            background-color: #3498db;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        .buy-button:hover {
            background-color: #2980b9;
        }
        .back-button {
            display: inline-block;
            margin: 20px;
            padding: 10px 20px;
            background-color: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .specs {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-button">← Retour à la liste</a>
    
    <div class="product-container">
        <div class="product-image-container">
            <img src="<?php echo $product['image_url'] !== null ? htmlspecialchars($product['image_url']) : ''; ?>" 
                 alt="<?php echo $product['model'] !== null ? htmlspecialchars($product['model']) : ''; ?>" 
                 class="product-image">
        </div>
        
        <div class="product-info">
            <h1><?php echo htmlspecialchars(($product['brand'] ?? '') . ' ' . ($product['model'] ?? '')); ?></h1>
            
            <div class="price">
                <?php echo htmlspecialchars(($product['price'] ?? '') . ' ' . ($product['currency'] ?? '')); ?>
            </div>
            
            <div class="specs">
                <h3>Caractéristiques</h3>
                <p><strong>Dimensions:</strong> <?php echo $product['dimensions'] !== null ? htmlspecialchars($product['dimensions']) : 'Non spécifié'; ?></p>
                <p><strong>Description:</strong> <?php echo $product['description'] !== null ? nl2br(htmlspecialchars($product['description'])) : 'Aucune description disponible'; ?></p>
                <p><strong>Disponible chez:</strong> <?php echo $product['stores'] !== null ? htmlspecialchars($product['stores']) : 'Non spécifié'; ?></p>
            </div>
            
            <a href="<?php echo $product['source'] !== null ? htmlspecialchars($product['source']) : '#'; ?>" 
               class="buy-button" 
               target="_blank">
                Acheter chez <?php echo $product['stores'] !== null ? htmlspecialchars($product['stores']) : 'le vendeur'; ?>
            </a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?> 