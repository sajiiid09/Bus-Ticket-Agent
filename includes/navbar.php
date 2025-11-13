<?php
/**
 * Navbar Component - Fixed top navigation with modal triggers
 */
?>
<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="main-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <span class="brand-text">BusTicket</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Pick Bus</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#operators">Bus Operators</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search_result.php#route-map">Routes Map</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mailto:support@busticket.bd">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
