<template>
<!-- Footer -->
  <footer class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
          <h5 class="fw-bold mb-3">Kofi Barbers</h5>
          <p>Experience the best barber services in the city</p>
          <div class="d-flex gap-3 mt-4">
            <a href="https://web.facebook.com" class="text-white"><i class="fab fa-facebook-f"></i></a>
            <!-- <a href="#" class="text-white"><i class="fab fa-twitter"></i></a> -->
            <a href="https://www.instagram.com" class="text-white"><i class="fab fa-instagram"></i></a>
            <!-- <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a> -->
          </div>
        </div>
        <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
          <h6 class="fw-bold mb-3">Services</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Standard haircuts</a></li>
            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Beard Trim</a></li>
            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Deluxe Haircut & Beard Trim</a></li>
            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Kids Haircut</a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
          <h6 class="fw-bold mb-3">Company</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">About</a></li>
            <li class="mb-2"><a href="/#portfolio" class="text-white-50 text-decoration-none">Portfolio</a></li>
            <!-- <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Blog</a></li>
            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Careers</a></li> -->
          </ul>
        </div>
        <div class="col-lg-4 col-md-4">
          <h6 class="fw-bold mb-3">Contact</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i> 123 Norkem Park, Kempton Park</li>
            <li class="mb-2"><a href="tel:+27728514752"><i class="fas fa-phone me-2 text-primary"></i><span class="text-white">+27 72 851 4752</span></a></li>
            <li class="mb-2"><a href="mailto:info@kofi.co.za"><i class="fas fa-envelope me-2 text-primary"></i><span class="text-white">info@kofi.co.za</span></a></li>
          </ul>
        </div>
      </div>
      <hr class="my-4 bg-white-50">
      <div class="row">
        <div class="col-md-12 text-center">
          <p class="small text-white-50 mb-0">&copy; {{ currentYear }} Kofi Barbers Powered by BAKILI. All rights reserved.</p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "Footer",
  data() {
    return {
      currentYear: new Date().getFullYear(),
    };
  },
};

</script>

<style scoped>
:root {
  --primary: #005e91;
  --secondary: #4f46e5;
  --accent: #e71c25;
  --light: #f9fafb;
  --dark: #111827;
}
.bg-shape-primary {
  background-color: #383838 !important;
}
.bg-orange-500 {
  background-color: #FD7E14 !important;
}
a{
  text-decoration: none !important;

}
body {
  font-family: 'Inter', sans-serif;
  color: #111827;
  background-color: #f9fafb;
  line-height: 1.7;
}

.navbar {
  padding: 1rem 0;
  background-color: rgba(255, 255, 255, 0.95);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.hero-section {
  background: linear-gradient(135deg, #005e91 0%, #008fdc 100%);
  color: white;
  padding: 6rem 0;
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  pointer-events: none;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.service-card {
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  border: none;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.service-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.service-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.team-member {
  position: relative;
  overflow: hidden;
  border-radius: 16px;
}

.team-member img {
  transition: all 0.5s ease;
}

.team-member:hover img {
  transform: scale(1.05);
}

.team-info {
  background: rgba(255, 255, 255, 0.95);
  padding: 1rem;
  border-radius: 8px;
  position: relative;
  margin-top: -40px;
  z-index: 2;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.section-title {
  position: relative;
  display: inline-block;
  margin-bottom: 2rem;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 0;
  width: 40px;
  height: 3px;
  background-color: #e71c25;
}

.journey-line {
  position: relative;
}

.journey-line::before {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  left: 20px;
  width: 2px;
  background-color: #005e91;
  opacity: 0.3;
}

.journey-item {
  position: relative;
  padding-left: 50px;
  margin-bottom: 2rem;
}

.journey-item::before {
  content: '';
  position: absolute;
  left: 14px;
  top: 8px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background-color: #005e91;
}

.accent-bg {
  background-color: rgba(99, 102, 241, 0.05);
}

.cta-section {
  background: linear-gradient(135deg, #005e91 0%, #008fdc 100%);
  color: white;
  border-radius: 16px;
  overflow: hidden;
  position: relative;
}

.cta-section::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  pointer-events: none;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.btn-primary {
  background-color: #6366f1;
  border-color: #6366f1;
  padding: 0.75rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background-color: #4f46e5;
  border-color: #4f46e5;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-outline-light {
  border-width: 2px;
  font-weight: 600;
  padding: 0.75rem 2rem;
  border-radius: 8px;
}

footer {
  background-color: #111827;
  color: #f9fafb;
}
</style>