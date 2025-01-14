<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparateur de Prix Rolex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="logo-container">
                <img src="images rolex/logo rolex.png" alt="Rolex Logo">
            </div>
        </div>
    </div>

    <div class="container">
        <div class="search-container">
            <h1 class="text-white mb-4">Comparateur de Prix Rolex</h1>
            <form action="search.php" method="GET" class="d-flex justify-content-center align-items-center gap-3">
                <input type="text" name="query" class="form-control search-input flex-grow-1" 
                       placeholder="Rechercher une montre Rolex (ex: Submariner, GMT-Master II, Daytona...)">
                <button class="btn search-btn" type="submit">
                    <i class="fas fa-search me-2"></i>Rechercher
                </button>
            </form>
        </div>

        <div class="history-section">
            <h2 class="section-title">
                <i class="fas fa-history me-2"></i>
                L'Histoire de Rolex
            </h2>
            <p>Fondée en 1905 par Hans Wilsdorf, Rolex est devenue synonyme d'excellence horlogère et de prestige. L'entreprise a révolutionné l'industrie horlogère avec des innovations comme le premier boîtier étanche (Oyster) en 1926 et le premier mécanisme à remontage automatique avec rotor central (Perpetual) en 1931.</p>
            <p>Aujourd'hui, Rolex continue d'incarner l'excellence, la précision et l'innovation dans l'horlogerie de luxe, produisant certaines des montres les plus prestigieuses au monde.</p>
        </div>

        <h2 class="section-title text-center">
            <i class="fas fa-watch me-2"></i>
            Nos Montres Phares
        </h2>
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php
                $flagship_watches = [
                    ['Datejust', 'image rolex Datejust.png'],
                    ['Submariner', 'image rolex Submariner.webp'],
                    ['GMT-Master II', 'image rolex GMT-Master II.webp'],
                    ['Daytona', 'image rolex Cosmograph Daytona.webp'],
                    ['Day-Date', 'image role Day date.webp'],
                    ['Yacht-Master', 'image rolex Yatch-Master.png']
                ];

                foreach ($flagship_watches as $watch) {
                    echo '<div class="swiper-slide">
                        <a href="search.php?query=' . urlencode($watch[0]) . '" style="text-decoration: none;">
                            <img src="images rolex/' . $watch[1] . '" alt="' . $watch[0] . '">
                            <div class="slide-content">
                                <h3>' . $watch[0] . '</h3>
                            </div>
                        </a>
                    </div>';
                }
                ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.swiper', {
            effect: 'coverflow',
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: 'auto',
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: true,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    </script>
</body>
</html> 