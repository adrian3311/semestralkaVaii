<?php

/**
 * Home page view
 *
 * Purpose:
 * - Render the main landing page with a hero carousel, information about the cafe,
 *   featured specialties, a map preview and a newsletter signup (demo client-side behavior).
 *
 * Available variables:
 * - $link: \Framework\Support\LinkGenerator for building asset/route URLs
 *
 * Notes:
 * - Carousel behavior is implemented in the small script below and styles are
 *   kept in `public/css/inlined-styles.css`.
 * - Newsletter and contact components are demo-only and use localStorage for persistence.
 */

/** @var \Framework\Support\LinkGenerator $link */
?>

<div class="container-fluid">
    <!-- CAROUSEL SECTION
         - Displays a set of large featured images with captions.
         - Navigation buttons (prev/next) and dots allow manual control.
         - The carousel automatically advances on a timer and pauses on hover.
    -->

    <!-- carousel styles moved to public/css/inlined-styles.css -->

    <div class="carousel" id="mainCarousel" aria-roledescription="carousel">
        <div class="carousel-track" data-index="0">
            <div class="carousel-slide">
                <img src="/images/1764690793_the-cafe-area.jpg" alt="Slide 1">
                <div class="carousel-caption"><strong>Welcome to Arch Cafe</strong><div>Enjoy our special coffee and cozy atmosphere.</div></div>
            </div>
            <div class="carousel-slide">
                <img src="/images/1764691113_chocolate-cake.jpg" alt="Slide 2">
                <div class="carousel-caption"><strong>Seasonal Specials</strong><div>Try our chef recommended dishes this week.</div></div>
            </div>
            <div class="carousel-slide">
                <img src="/images/1764690736_caption.jpg" alt="Slide 3">
                <div class="carousel-caption"><strong>Events & Gatherings</strong><div>Book your table for private events.</div></div>
            </div>
        </div>

        <!-- carousel navigation controls: previous / next -->
        <div class="carousel-nav" aria-hidden="false">
            <button class="prev" aria-label="Previous slide">‹</button>
            <button class="next" aria-label="Next slide">›</button>
        </div>
        <!-- dots container: populated by JS to indicate slides and allow direct navigation -->
        <div class="carousel-dots" role="tablist" aria-label="Slide dots"></div>
    </div>

    <!-- CAROUSEL SCRIPT
         - Initializes the carousel, creates dot controls, and manages auto-advance timer.
         - Pauses rotation on mouse enter and resumes on mouse leave.
    -->
    <script>
        (function(){
            const carousel = document.getElementById('mainCarousel');
            if (!carousel) return;
            const track = carousel.querySelector('.carousel-track');
            const slides = Array.from(track.children);
            const prevBtn = carousel.querySelector('.prev');
            const nextBtn = carousel.querySelector('.next');
            const dotsContainer = carousel.querySelector('.carousel-dots');
            let index = 0;
            const slideCount = slides.length;
            const intervalMs = 4000;
            let timer = null;

            // create dots for each slide and attach click handlers
            slides.forEach((s, i)=>{
                const d = document.createElement('button');
                d.className = 'carousel-dot' + (i===0? ' active':'');
                d.setAttribute('data-index', String(i));
                d.setAttribute('aria-label', 'Go to slide ' + (i+1));
                d.addEventListener('click', ()=> goTo(i));
                dotsContainer.appendChild(d);
            });

            function update() {
                track.style.transform = 'translateX(' + (-index * 100) + '%)';
                const dots = dotsContainer.children;
                for (let i=0;i<dots.length;i++) dots[i].classList.toggle('active', i===index);
            }

            function goTo(i) {
                index = (i + slideCount) % slideCount;
                update();
                restartTimer();
            }
            function next(){ goTo(index+1); }
            function prev(){ goTo(index-1); }

            prevBtn.addEventListener('click', prev);
            nextBtn.addEventListener('click', next);

            function startTimer(){ timer = setInterval(next, intervalMs); }
            function stopTimer(){ if (timer) { clearInterval(timer); timer = null; } }
            function restartTimer(){ stopTimer(); startTimer(); }

            carousel.addEventListener('mouseenter', stopTimer);
            carousel.addEventListener('mouseleave', startTimer);

            // init
            update();
            startTimer();
        })();
    </script>

    <!-- About & Specialties section
         - Left column: about text, opening hours, contact info
         - Right column: specialties grid with images and short captions
    -->
    <div class="container mt-5 mb-5">
        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-6">
                <h3>About Arch Cafe</h3>
                <p>Arch Cafe is a cozy spot where good coffee meets great company. We roast our beans locally and prepare dishes from fresh, seasonal ingredients — perfect for work, meetups, or relaxed weekends.</p>

                <h5 class="mt-3">Opening hours</h5>

                <!-- Compact list version to save vertical space (plain text entries, no spans) -->
                <ul class="list-unstyled opening-hours-list mb-3" aria-label="Opening hours">
                    <li>Sunday - <strong>Closed</strong></li>
                    <li>Monday - <strong>Closed</strong></li>
                    <li>Tuesday - <strong>9:30 AM - 3:30 PM</strong></li>
                    <li>Wednesday - <strong>9:30 AM - 3:30 PM</strong></li>
                    <li>Thursday - <strong>9:30 AM - 3:30 PM</strong></li>
                    <li>Friday  -  <strong>Closed</strong></li>
                    <li>Saturday - <strong>9:30 AM - 3:30 PM</strong></li>
                </ul>

                <h5 class="mt-3">Contact</h5>
                <p class="mb-1">Phone: <a href="tel:+421900000000">+421 900 000 000</a></p>
                <p>Email: <a href="mailto:info@archcafe.example">info@archcafe.example</a></p>
            </div>

            <div class="col-12 col-lg-6">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h3 class="m-0">Our specialties</h3>
                    <?php try { $contactUrl = $link->url('home.contact'); } catch (\Throwable $_) { $contactUrl = '?c=home&a=contact'; } ?>
                    <a href="<?= $contactUrl ?>" class="btn btn-warning btn-sm">Reserve a table</a>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="card">
                            <img loading="lazy" src="/images/1765022995_capucino.jpg" class="card-img-top" alt="Coffee" style="height:110px;object-fit:cover;">
                            <div class="card-body p-2">
                                <strong>Cappuccino</strong>
                                <div class="text-muted small">Perfectly frothed milk & espresso</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <img loading="lazy" src="/images/1765022984_tiramisu.jpg" class="card-img-top" alt="Dessert" style="height:110px;object-fit:cover;">
                            <div class="card-body p-2">
                                <strong>Tiramisu</strong>
                                <div class="text-muted small">House-made, creamy layers</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <img loading="lazy" src="/images/1765023189_salad.jpg" class="card-img-top" alt="Salad" style="height:110px;object-fit:cover;">
                            <div class="card-body p-2">
                                <strong>Seasonal Salad</strong>
                                <div class="text-muted small">Fresh local produce</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <img loading="lazy" src="/images/logo.png" class="card-img-top" alt="Logo" style="height:110px;object-fit:cover;">
                            <div class="card-body p-2">
                                <strong>Daily Specials</strong>
                                <div class="text-muted small">Ask our barista for today's pick</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map & Newsletter -->
    <div class="container mb-5">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-md-7">
                <h4>Find us</h4>
                <div class="card">
                    <img src="/images/imgMap.png" alt="Map" class="img-fluid" style="height:320px;object-fit:cover;">
                    <div class="card-body">
                        <p class="mb-0">We are conveniently located in the city center — come by for a coffee or reserve a table.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-5">
                <h4>Join our newsletter</h4>
                <p>Get updates about specials and events (no spam).</p>
                <form id="newsletterForm" class="mb-2">
                    <div class="mb-2">
                        <input id="newsletterEmail" name="email" type="email" class="form-control" placeholder="Your email" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">Subscribe</button>
                        <button id="newsletterLater" type="button" class="btn btn-outline-secondary">Maybe later</button>
                    </div>
                </form>
                <div id="newsletterMessage" style="display:none;" class="alert alert-success">Thanks — you've been subscribed (demo).</div>
                <script>
                    (function(){
                        const form = document.getElementById('newsletterForm');
                        const emailInput = document.getElementById('newsletterEmail');
                        const msg = document.getElementById('newsletterMessage');
                        const later = document.getElementById('newsletterLater');
                        later.addEventListener('click', ()=>{ emailInput.value=''; msg.style.display='none'; });
                        form.addEventListener('submit', function(e){
                            e.preventDefault();
                            const email = emailInput.value.trim();
                            if (!email || !/.+@.+\..+/.test(email)) { alert('Please enter a valid email'); return; }
                            // client-side demo: store in localStorage and show message
                            try { localStorage.setItem('newsletter_email', email); } catch(e){}
                            msg.style.display = 'block';
                            form.style.display = 'none';
                        });
                    })();
                </script>
            </div>
        </div>
    </div>

    </div> <!-- end container-fluid -->
