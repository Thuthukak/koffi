<template>
    <Navbar />
    <div class="grooming">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Professional Grooming Services</h1>
                <p class="hero-subtitle">Discover our range of premium cuts and styles</p>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="services-container">
            <div class="container">
                <div v-if="loading" class="loading-state">
                    <div class="loading-spinner"></div>
                    <p>Loading services...</p>
                </div>

                <div v-else-if="error" class="error-state">
                    <p>{{ error }}</p>
                    <button @click="fetchServices" class="retry-btn">Try Again</button>
                </div>

                <div v-else class="services-grid">
                    <div 
                        v-for="service in services" 
                        :key="service.id" 
                        class="service-card"
                        @click="selectService(service)"
                    >
                        <div class="service-image">
                            <img 
                                :src="getServiceImage(service)"
                                :alt="service.name"
                                loading="lazy"
                            />
                            <div class="image-overlay">
                                <span class="view-details">View Details</span>
                            </div>
                        </div>
                        <div class="service-info">
                            <h3 class="service-name">{{ service.name }}</h3>
                            <p class="service-description">{{ service.description }}</p>
                            <div class="service-meta">
                                <span class="service-price">R{{ service.price }}</span>
                                <span class="service-duration">{{ service.duration }} min</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Modal -->
        <div v-if="selectedService" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <button class="modal-close" @click="closeModal">&times;</button>
                <div class="modal-image">
                    <img 
                        :src="selectedService ? getServiceImage(selectedService) : ''"
                        :alt="selectedService.name"
                    />
                </div>
                <div class="modal-info">
                    <h2>{{ selectedService.name }}</h2>
                    <p class="modal-description">{{ selectedService.description }}</p>
                    <div class="modal-meta">
                        <div class="meta-item">
                            <span class="meta-label">Price:</span>
                            <span class="meta-value">R{{ selectedService.price }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Duration:</span>
                            <span class="meta-value">{{ selectedService.duration }} minutes</span>
                        </div>
                    </div>
                    <a href="/bookings#join-queue-form" class="book-btn">Book Appointment</a>
                </div>
            </div>
        </div>
    </div>
    <Footer />
</template>

<script>
import Navbar from './Navbar.vue';
import Footer from './Footer.vue';
export default {
    name: "Grooming",
    components: {
        Navbar,
        Footer,
    },
    data() {
        return {
            services: [],
            loading: true,
            error: null,
            selectedService: null
        }
    },
    mounted() {
        this.fetchServices();
    },
    methods: {
        async fetchServices() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await fetch('/api/services');
                if (!response.ok) {
                    throw new Error('Failed to load services');
                }
                this.services = await response.json();
            } catch (err) {
                this.error = 'Unable to load services. Please try again.';
                console.error('Error fetching services:', err);
            } finally {
                this.loading = false;
            }
        },
        getServiceImage(service) {
            // Safety check for undefined service
            if (!service) {
                return 'https://images.unsplash.com/photo-1622286346003-c8b3a7a6e6c1?w=400&h=300&fit=crop';
            }
            
            // Use the actual photo from the database if available
            if (service.photo && service.photo.trim() !== '') {
                return service.photo;
            }
            
            // Fallback to placeholder images based on service name
            const imageMap = {
                'Standard Haircut': 'https://images.unsplash.com/photo-1622286346003-c8b3a7a6e6c1?w=400&h=300&fit=crop',
                'Beard Trim': 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=400&h=300&fit=crop',
                'Deluxe Haircut & Beard Trim': 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=400&h=300&fit=crop',
                'Kids Haircut': 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&h=300&fit=crop',
                'Hot Towel Shave': 'https://images.unsplash.com/photo-1559599238-1ba4942c6fe5?w=400&h=300&fit=crop',
                'High Fade': 'https://images.unsplash.com/photo-1605497788044-5a32c7078486?w=400&h=300&fit=crop',
                'Taper Fade': 'https://images.unsplash.com/photo-1622902046580-2b47f47f5471?w=400&h=300&fit=crop'
            };
            
            // Return service-specific placeholder or default placeholder
            return imageMap[service.name] || 'https://images.unsplash.com/photo-1622286346003-c8b3a7a6e6c1?w=400&h=300&fit=crop';
        },
        selectService(service) {
            this.selectedService = service;
        },
        closeModal() {
            this.selectedService = null;
        }
    }
}
</script>

<style scoped>
/* CSS Variables for consistent theming */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #2b7fff;
    --accent-color: #f39c12;
    --text-dark: #2c3e50;
    --text-light: #7f8c8d;
    --background-light: #f8f9fa;
    --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    --transition: all 0.3s ease;
}
a{
    text-decoration: none !important;
}
.grooming {
    min-height: 100vh;
    background: #f8f9fa;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
}

.hero-content {
    max-width: 600px;
    margin: 0 auto;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle {
    font-size: 1.25rem;
    opacity: 0.9;
    font-weight: 300;
}

/* Container */
.services-container {
    padding: 60px 20px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Loading and Error States */
.loading-state {
    text-align: center;
    padding: 60px 20px;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #2b7fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.error-state {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.retry-btn {
    background: #2b7fff;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    margin-top: 20px;
    transition: all 0.3s ease;
}

.retry-btn:hover {
    background: #0e69f1;
    transform: translateY(-2px);
}

/* Services Grid */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    margin-top: 20px;
}

/* Service Card */
.service-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.service-image {
    position: relative;
    overflow: hidden;
    height: 220px;
}

.service-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.service-card:hover .service-image img {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.service-card:hover .image-overlay {
    opacity: 1;
}

.view-details {
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.service-info {
    padding: 24px;
}

.service-name {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
}

.service-description {
    color: #7f8c8d;
    line-height: 1.6;
    margin-bottom: 16px;
}

.service-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.service-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2b7fff;
}

.service-duration {
    background: #2b7fff;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
}

.modal-close {
    position: absolute;
    top: 16px;
    right: 20px;
    background: none;
    border: none;
    font-size: 2rem;
    color: #7f8c8d;
    cursor: pointer;
    z-index: 10;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: rgba(0, 0, 0, 0.1);
    color: #2c3e50;
}

.modal-image {
    height: 300px;
    overflow: hidden;
}

.modal-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.modal-info {
    padding: 30px;
}

.modal-info h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 12px;
}

.modal-description {
    color: #7f8c8d;
    line-height: 1.6;
    margin-bottom: 24px;
    font-size: 1.1rem;
}

.modal-meta {
    display: grid;
    gap: 16px;
    margin-bottom: 30px;
}

.meta-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.meta-label {
    font-weight: 600;
    color: #2c3e50;
}

.meta-value {
    font-weight: 700;
    color: #2b7fff;
    font-size: 1.1rem;
}

.book-btn {
    width: 100%;
    background: #2b7fff;
    color: white;
    border: none;
    padding: 16px;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.book-btn:hover {
    background: #0e69f1;
    transform: translateY(-2px);
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.2rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .services-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .service-card {
        margin: 0 10px;
    }
    
    .modal-content {
        margin: 10px;
        max-width: calc(100% - 20px);
    }
    
    .modal-info {
        padding: 20px;
    }
    
    .modal-info h2 {
        font-size: 1.6rem;
    }
    
    .meta-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 60px 20px;
    }
    
    .services-container {
        padding: 40px 10px;
    }
    
    .service-image {
        height: 180px;
    }
    
    .service-info {
        padding: 20px;
    }
    
    .service-name {
        font-size: 1.2rem;
    }
    
    .service-meta {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }
}
</style>