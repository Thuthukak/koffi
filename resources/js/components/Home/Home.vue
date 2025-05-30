<template>
  <Navbar class="navbar" />
  <div>
    <!-- Hero Section -->
    <section class="hero" id="home">
      <div class="hero-content">
        <h1>KOFI BARBER SHOP</h1>
        <p>Where Style Meets Culture - Premium Cuts for the Community</p>
        <button class="cta-button"><a style="text-decoration: none; color: #fff;" href="/bookings">Book Your Cut</a></button>
        <!-- <button class="cta-button" @click="openBookingModal">Book Your Cut</button> -->
      </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
      <div class="container">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">Excellence in every cut, style, and experience</p>
        
        <div class="services-grid">
          <div 
            v-for="service in services" 
            :key="service.id" 
            class="service-card"
          >
            <div class="service-icon">
              <i :class="service.icon"></i>
            </div>
            <h3>{{ service.title }}</h3>
            <p>{{ service.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats" ref="statsSection">
      <div class="container">
        <div class="stats-grid">
          <div 
            v-for="stat in stats" 
            :key="stat.id" 
            class="stat-item"
          >
            <h3>{{ stat.animatedValue }}{{ stat.suffix }}</h3>
            <p>{{ stat.label }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
      <div class="container">
        <div class="about-content">
          <div class="about-text">
            <h2 class="fw-bold">About Kofi Cuts</h2>
            <p>Founded in 2008, Kofi Barber Shop has been serving the community with pride, skill, and dedication. We're more than just a barbershop - we're a cornerstone of the neighborhood where culture, conversation, and craftsmanship come together.</p>
            <p>Our team of master barbers brings decades of combined experience, specializing in cuts that celebrate and enhance the natural beauty of our clients. From classic styles to the latest trends, we ensure every customer leaves looking and feeling their absolute best.</p>
            <p>We believe in building relationships, not just cutting hair. Step into our shop and become part of the family.</p>
          </div>
          <div class="about-image">
            <i class="fas fa-crown"></i>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
      <div class="container">
        <h2 class="section-title">Visit Us Today</h2>
        <p class="section-subtitle">Located in the heart of the community</p>
        
        <div class="contact-grid">
          <div class="contact-info">
            <h3 style="color: #2b7fff; margin-bottom: 1.5rem;">Get In Touch</h3>
            
            <div 
              v-for="contact in contactInfo" 
              :key="contact.id" 
              class="contact-item"
            >
              <i :class="contact.icon"></i>
              <div v-html="contact.content"></div>
            </div>
          </div>
          
          <div class="hours">
            <h3>Business Hours</h3>
            
            <div 
              v-for="hour in businessHours" 
              :key="hour.id" 
              class="hours-item"
            >
              <span>{{ hour.day }}</span>
              <span>{{ hour.time }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Booking Modal -->
    <div v-if="showBookingModal" class="modal-overlay" @click="closeBookingModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Book Your Appointment</h3>
          <button class="close-btn" @click="closeBookingModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>Ready to look your best? Contact us to schedule your appointment!</p>
          <div class="booking-options">
            <a href="tel:011-123-2887" class="booking-btn">
              <i class="fas fa-phone"></i>
              Call (555) 123-CUTS
            </a>
            <a href="mailto:info@kofi.co.za" class="booking-btn">
              <i class="fas fa-envelope"></i>
              Email Us
            </a>
          </div>
          <p class="booking-note">Or visit us in person at 123 Norkem Park, Kempton Park</p>
        </div>
      </div>
    </div>
  </div>
  <Footer />
</template>

<script>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import Navbar from './Navbar.vue'
import Footer from './Footer.vue'

export default {
  name: 'KofiBarbershopHomepage',
  components: {
    Navbar,
    Footer
  },
  setup() {
    // Reactive data
    const isMenuOpen = ref(false)
    const showBookingModal = ref(false)
    const statsSection = ref(null)
    const statsAnimated = ref(false)

    // Services data
    const services = reactive([
      {
        id: 1,
        icon: 'fas fa-cut',
        title: 'Classic Cuts',
        description: 'Traditional and modern haircuts tailored to your style. From fades to line-ups, we\'ve got you covered.'
      },
      {
        id: 2,
        icon: 'fas fa-magic',
        title: 'Beard Styling',
        description: 'Professional beard trimming and styling to complement your look and enhance your features.'
      },
      {
        id: 3,
        icon: 'fas fa-spa',
        title: 'Hot Towel Treatment',
        description: 'Relaxing hot towel service for the ultimate grooming experience and smooth finish.'
      },
      {
        id: 4,
        icon: 'fas fa-child',
        title: 'Kids Cuts',
        description: 'Patient and skilled service for our younger clients in a fun, welcoming environment.'
      },
      {
        id: 5,
        icon: 'fas fa-star',
        title: 'VIP Package',
        description: 'Complete grooming experience including cut, style, beard trim, and hot towel treatment.'
      },
      {
        id: 6,
        icon: 'fas fa-calendar-alt',
        title: 'Group Events',
        description: 'Special packages for weddings, parties, and group bookings. Let us make your event memorable.'
      }
    ])

    // Stats data
    const stats = reactive([
      { id: 1, value: 5000, animatedValue: 0, suffix: '+', label: 'Happy Customers' },
      { id: 2, value: 15, animatedValue: 0, suffix: '+', label: 'Years Experience' },
      { id: 3, value: 50, animatedValue: 0, suffix: '+', label: '5-Star Reviews' },
      { id: 4, value: 24, animatedValue: 24, suffix: '/7', label: 'Online Booking' }
    ])

    // Contact info
    const contactInfo = reactive([
      {
        id: 1,
        icon: 'fas fa-map-marker-alt',
        content: '<strong>Address</strong><br>123 Norkem Park<br>Kempton Park'
      },
      {
        id: 2,
        icon: 'fas fa-phone',
        content: '<strong>Phone</strong><br>011-123-2887'
      },
      {
        id: 3,
        icon: 'fas fa-envelope',
        content: '<strong>Email</strong><br>info@kofi.co.za'
      },
      {
        id: 4,
        icon: 'fas fa-calendar-check',
        content: '<strong>Book Online</strong><br>Available 24/7'
      }
    ])

    // Business hours
    const businessHours = reactive([
      { id: 1, day: 'Monday ', time: '10:00 AM - 7:00 PM' },
      { id: 2, day: 'Tuesday', time: '10:00 AM - 7:00 PM' },
      { id: 3, day: 'Wednesday', time: '10:00 AM - 7:00 PM' },
      { id: 4, day: 'Thursday', time: '10:00 AM - 7:00 PM' },
      { id: 5, day: 'Friday', time: '9:00 AM - 8:00 PM' },
      { id: 6, day: 'Saturday', time: '8:00 AM - 6:00 PM' },
      { id: 7, day: 'Sunday', time: '12:00 PM - 5:00 PM' }
    ])

    // Social links
    const socialLinks = reactive([
      { id: 1, icon: 'fab fa-instagram', url: '#' },
      { id: 2, icon: 'fab fa-facebook', url: '#' },
      { id: 3, icon: 'fab fa-twitter', url: '#' },
      { id: 4, icon: 'fab fa-tiktok', url: '#' }
    ])

    // Methods
    const toggleMenu = () => {
      isMenuOpen.value = !isMenuOpen.value
    }

    const closeMenu = () => {
      isMenuOpen.value = false
    }

    const openBookingModal = () => {
      showBookingModal.value = true
    }

    const closeBookingModal = () => {
      showBookingModal.value = false
    }

    const animateStats = () => {
      if (statsAnimated.value) return
      
      stats.forEach(stat => {
        if (stat.id === 4) return // Skip 24/7 as it doesn't need animation
        
        let current = 0
        const increment = stat.value / 50
        const timer = setInterval(() => {
          current += increment
          if (current >= stat.value) {
            stat.animatedValue = stat.value
            clearInterval(timer)
          } else {
            stat.animatedValue = Math.floor(current)
          }
        }, 30)
      })
      
      statsAnimated.value = true
    }

    const handleScroll = () => {
      // Navbar scroll effect
      const navbar = document.querySelector('.navbar')
      if (window.scrollY > 100) {
        navbar.style.background = 'rgba(0, 0, 0, 0.98)'
      } else {
        navbar.style.background = 'rgba(0, 0, 0, 0.95)'
      }

      // Stats animation trigger
      if (statsSection.value && !statsAnimated.value) {
        const rect = statsSection.value.getBoundingClientRect()
        if (rect.top < window.innerHeight && rect.bottom > 0) {
          animateStats()
        }
      }
    }

    const handleSmoothScroll = (e) => {
      const href = e.target.getAttribute('href')
      if (href && href.startsWith('#')) {
        e.preventDefault()
        const target = document.querySelector(href)
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          })
        }
      }
    }

    // Lifecycle hooks
    onMounted(() => {
      window.addEventListener('scroll', handleScroll)
      
      // Add smooth scrolling to all anchor links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', handleSmoothScroll)
      })
    })

    onUnmounted(() => {
      window.removeEventListener('scroll', handleScroll)
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.removeEventListener('click', handleSmoothScroll)
      })
    })

    return {
      // Reactive data
      isMenuOpen,
      showBookingModal,
      statsSection,
      services,
      stats,
      contactInfo,
      businessHours,
      socialLinks,
      
      // Methods
      toggleMenu,
      closeMenu,
      openBookingModal,
      closeBookingModal
    }
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Arial', sans-serif;
  line-height: 1.6;
  color: #333;
  overflow-x: hidden;
}

/* Navigation */
.navbar {
  position: fixed;
  top: 0;
  width: 100%;
  backdrop-filter: blur(10px);
  z-index: 1000;
  transition: all 0.3s ease;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 2rem;
}

.logo {
  font-size: 1.8rem;
  font-weight: bold;
  color: #2b7fff;
  text-decoration: none;
}

.nav-menu {
  display: flex;
  list-style: none;
  gap: 2rem;
}

.nav-link {
  color: white;
  text-decoration: none;
  transition: color 0.3s ease;
}

.nav-link:hover {
  color: #2b7fff;
}

.menu-toggle {
  display: none;
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
}

/* Hero Section */
.hero {
  height: 100vh;
  background: linear-gradient(135deg, #131313 0%, #2d2d2d 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: white;
  position: relative;
  overflow: hidden;
}

.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url(/public/assets/images/home_banner.png) repeat;
  opacity: 0.5;
}

.hero-content {
  position: relative;
  z-index: 2;
  max-width: 800px;
  padding: 0 2rem;
}

.hero h1 {
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: bold;
  margin-bottom: 1rem;
  background: linear-gradient(45deg, #ffffff, #ffffff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}


.hero p {
  font-size: 1.3rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.cta-button {
  display: inline-block;
  background: linear-gradient(45deg, #2b7fff, #0c6bfa);
  color: #000;
  padding: 1rem 2.5rem;
  border: none;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: bold;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;

}

.cta-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(207, 223, 254, 0.6);
}

/* Services Section */
.services {
  padding: 5rem 0;
  background: #f8f9fa;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.section-title {
  text-align: center;
  font-size: 2.5rem;
  font-weight: bold;
  margin-bottom: 1rem;
  color: #333;
}

.section-subtitle {
  text-align: center;
  font-size: 1.2rem;
  color: #666;
  margin-bottom: 3rem;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-top: 3rem;
}

.service-card {
  background: white;
  padding: 2rem;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  border-top: 4px solid #2b7fff;
}

.service-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.service-icon {
  font-size: 3rem;
  color: #2b7fff;
  margin-bottom: 1rem;
}

.service-card h3 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: #333;
}

.service-card p {
  color: #666;
  line-height: 1.6;
}

/* About Section */
.about {
  padding: 5rem 0;
  background: #1a1a1a;
  color: white;
}

.about-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: center;
}

.about-text h2 {
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
  color: #ffffff;
}

.about-text p {
  font-size: 1.1rem;
  line-height: 1.8;
  margin-bottom: 1.5rem;
  opacity: 0.9;
}

.about-image {
  background-image: url(/public/assets/images/hero.jpg);
  position: relative;
  height: 400px;
  border-radius: 15px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
    background-position:center center;
  background-repeat: no-repeat;
  background-size: cover;
}

.about-image i {
  font-size: 5rem;
  color: #1a1a1a;
}

/* Stats Section */
.stats {
  padding: 3rem 0;
  background: #2b7fff;
  color: #1a1a1a;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  text-align: center;
}

.stat-item h3 {
  font-size: 2.5rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.stat-item p {
  font-size: 1.1rem;
  font-weight: 500;
}

/* Contact Section */
.contact {
  padding: 5rem 0;
  background: #f8f9fa;
}

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
}

.contact-info {
  background: white;
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.contact-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 1.5rem;
}

.contact-item i {
  font-size: 1.5rem;
  color: #2b7fff;
  margin-right: 1rem;
  width: 30px;
  margin-top: 0.25rem;
}

.hours {
  background: #1a1a1a;
  color: white;
  padding: 2.5rem;
  border-radius: 15px;
}

.hours h3 {
  color: #2b7fff;
  margin-bottom: 1.5rem;
  font-size: 1.8rem;
}

.hours-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #333;
}

/* Footer */
.footer {
  background: #1a1a1a;
  color: white;
  text-align: center;
  padding: 2rem 0;
}

.social-links {
  margin-bottom: 1rem;
}

.social-links a {
  color: #2b7fff;
  font-size: 1.5rem;
  margin: 0 1rem;
  transition: color 0.3s ease;
  text-decoration: none;
}

.social-links a:hover {
  color: #0c6bfa;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-content {
  background: white;
  border-radius: 15px;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  color: #333;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #666;
  cursor: pointer;
  padding: 0.5rem;
}

.close-btn:hover {
  color: #333;
}

.modal-body {
  padding: 2rem;
  text-align: center;
}

.booking-options {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin: 2rem 0;
}

.booking-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background: linear-gradient(45deg, #2b7fff, #0c6bfa);
  color: #000;
  padding: 1rem;
  border-radius: 10px;
  text-decoration: none;
  font-weight: bold;
  transition: all 0.3s ease;
}

.booking-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
}

.booking-note {
  color: #666;
  font-size: 0.9rem;
  margin-top: 1rem;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .nav-menu {
    position: fixed;
    top: 70px;
    left: -100%;
    width: 100%;
    height: calc(100vh - 70px);
    background: rgba(0, 0, 0, 0.95);
    flex-direction: column;
    justify-content: flex-start;
    padding-top: 2rem;
    transition: left 0.3s ease;
  }

  .nav-menu.active {
    left: 0;
  }

  .menu-toggle {
    display: block;
  }

  .hero h1 {
    font-size: 2.5rem;
  }

  .hero p {
    font-size: 1.1rem;
  }

  .about-content {
    grid-template-columns: 1fr;
  }

  .contact-grid {
    grid-template-columns: 1fr;
  }

  .services-grid {
    grid-template-columns: 1fr;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .booking-options {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .nav-container {
    padding: 0 1rem;
  }

  .hero-content {
    padding: 0 1rem;
  }

  .container {
    padding: 0 1rem;
  }

  .section-title {
    font-size: 2rem;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>