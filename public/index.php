<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Find Your Dream Home</h1>
            <p>Search from thousands of real estate listings including apartments, houses, and commercial properties.
            </p>
        </div>
    </div>
</section>

<div class="container">
    <div class="search-container">
        <form id="search-form" class="search-form">
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="e.g. Kathmandu">
            </div>
            <div class="form-group">
                <label for="type">Property Type</label>
                <select id="type" name="type">
                    <option value="">All Types</option>
                    <option value="Apartment">Apartment</option>
                    <option value="House">House</option>
                    <option value="Land">Land</option>
                    <option value="Commercial">Commercial</option>
                </select>
            </div>
            <div class="form-group">
                <label for="max_price">Max Price</label>
                <input type="number" id="max_price" name="max_price" placeholder="Enter Amount">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary w-full">Search</button>
            </div>
        </form>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Featured Properties</h2>
        </div>

        <div id="property-list" class="property-grid">
            <!-- Properties will be loaded here via Ajax -->
            <div class="loader">Loading properties...</div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchForm = document.getElementById('search-form');
        const propertyList = document.getElementById('property-list');

        const fetchProperties = async (filters = {}) => {
            propertyList.innerHTML = '<div class="loader">Loading properties...</div>';

            const params = new URLSearchParams(filters);
            try {
                const response = await fetch(`/fsd_final/ajax/search_properties.php?${params.toString()}`);
                const data = await response.text();
                propertyList.innerHTML = data;
            } catch (error) {
                console.error('Error fetching properties:', error);
                propertyList.innerHTML = '<p>Error loading properties. Please try again.</p>';
            }
        };

        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(searchForm);
            const filters = Object.fromEntries(formData.entries());
            fetchProperties(filters);
        });

        // Initial load
        fetchProperties();
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>