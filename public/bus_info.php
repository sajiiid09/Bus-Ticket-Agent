<?php
/**
 * Bus Info Modal - AJAX endpoint for loading bus details
 */
require_once __DIR__ . '/../app/repositories/BusRepository.php';

$bus_id = isset($_POST['bus_id']) ? intval($_POST['bus_id']) : 0;

if ($bus_id) {
    $busRepo = new BusRepository();
    $bus = $busRepo->getBusById($bus_id);
    
    if ($bus) {
        $images = explode(' ', $bus['image']);
        
        echo '<div class="bus-details">';
        echo '<h5 class="bus-title">' . htmlspecialchars($bus['bus_name']) . '</h5>';
        
        // Image carousel
        echo '<div id="busCarousel" class="carousel slide" data-ride="carousel" style="margin-bottom: 20px;">';
        echo '<ul class="carousel-indicators">';
        foreach ($images as $idx => $img) {
            echo '<li data-target="#busCarousel" data-slide-to="' . $idx . '" class="' . ($idx === 0 ? 'active' : '') . '"></li>';
        }
        echo '</ul>';
        
        echo '<div class="carousel-inner">';
        foreach ($images as $idx => $img) {
            echo '<div class="carousel-item ' . ($idx === 0 ? 'active' : '') . '">';
            echo '<img src="assets/img/busimages/' . htmlspecialchars(trim($img)) . '" style="width:100%; height:300px; object-fit:cover;">';
            echo '</div>';
        }
        echo '</div>';
        
        echo '<a class="carousel-control-prev" href="#busCarousel" role="button" data-slide="prev">';
        echo '<span class="carousel-control-prev-icon"></span></a>';
        echo '<a class="carousel-control-next" href="#busCarousel" role="button" data-slide="next">';
        echo '<span class="carousel-control-next-icon"></span></a>';
        echo '</div>';
        
        // Details
        echo '<div class="bus-info">';
        echo '<p><strong>Company:</strong> ' . htmlspecialchars($bus['company']) . '</p>';
        echo '<p><strong>Number of Buses:</strong> ' . $bus['no_bus'] . '</p>';
        echo '<p><strong>Routes:</strong> ' . htmlspecialchars($bus['routes']) . '</p>';
        echo '<div class="social-links">';
        echo '<a href="#" class="fa fa-facebook" style="margin-right: 10px;"></a>';
        echo '<a href="#" class="fa fa-twitter" style="margin-right: 10px;"></a>';
        echo '<a href="#" class="fa fa-linkedin"></a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
exit;
?>
