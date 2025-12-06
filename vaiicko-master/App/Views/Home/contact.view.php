<?php

/**
 * Contact page view
 *
 * Purpose:
 * - Render contact information (address, phone, GPS), opening hours and social links.
 * - Show a map preview image and a simple contact form (client-side demo behavior).
 *
 * Expected variables:
 * - $link: \Framework\Support\LinkGenerator used to build asset and route URLs
 *
 * Notes:
 * - The contact form uses localStorage for demo persistence; in production this
 *   should submit to a secure server endpoint and include server-side validation.
 */

/** @var \Framework\Support\LinkGenerator $link */
?>

<div class="container">
    <!-- Page header: title and short description -->
    <div class="row my-4">
        <div class="col-12 text-center">
            <div class="p-4 rounded-3" style="background:linear-gradient(135deg,#f8f9fa, #e9ecef);">
                <h1 class="mb-1">Contact Arch Cafe</h1>
                <p class="text-muted mb-0">We'd love to hear from you — questions, reservations or feedback.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left column: visit information, contact details, opening hours and social links -->
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <!-- Visit us block: address, phone, GPS coordinates -->
                    <h4 class="card-title">Visit us</h4>
                    <p class="card-text mb-1"><strong>Address</strong>: Little Vauxhall, Kresen Kernow, Redruth TR15 1AS, England</p>
                    <p class="card-text mb-1"><strong>Tel.</strong>: <a href="tel:+447463764174">+44 7463 764174</a></p>
                    <p class="card-text"><strong>GPS</strong>: 50.234997, -5.234382</p>

                    <hr>

                    <!-- Opening hours block: list schedule -->
                    <h5>Opening hours</h5>
                    <ul class="list-unstyled mb-3">
                        <li>Sunday - <strong>Closed</strong></li>
                        <li>Monday - <strong>Closed</strong></li>
                        <li>Tuesday - <strong>9:30 AM - 3:30 PM</strong></li>
                        <li>Wednesday - <strong>9:30 AM - 3:30 PM</strong></li>
                        <li>Thursday - <strong>9:30 AM - 3:30 PM</strong></li>
                        <li>Friday  -  <strong>Closed</strong></li>
                        <li>Saturday - <strong>9:30 AM - 3:30 PM</strong></li>
                    </ul>

                    <!-- Social and contact links: quick actions to follow or contact -->
                    <h5 class="mb-2">Follow us</h5>
                    <div class="d-flex gap-2 mb-3">
                        <!-- External socials open in a new tab with security rel attributes -->
                        <a class="btn btn-outline-primary btn-sm" href="https://www.facebook.com/p/Arch-Cafe-at-Kresen-Kernow-100091795280068" target="_blank" rel="noopener noreferrer" aria-label="Facebook">📘 Facebook</a>
                        <a class="btn btn-outline-danger btn-sm" href="https://www.instagram.com/michal.liba/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">📸 Instagram</a>
                        <!-- Quick email link opens user's mail client -->
                        <a class="btn btn-outline-secondary btn-sm" href="mailto:info@archcafe.example" aria-label="Email">✉️ Email us</a>
                    </div>

                    <div class="mt-3 text-muted small">Parking available nearby. Wheelchair accessible entrance.</div>
                </div>
            </div>
        </div>

        <!-- Right column: map preview and contact form -->
        <div class="col-12 col-lg-7">
            <div class="row g-3">
                <!-- Map preview card: responsive image used as a quick visual map -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body p-0" style="overflow:hidden;height:360px;">
                            <!-- Using an image inside a responsive container for a quick map preview -->
                            <img src="/images/imgMap.png" alt="Map" style="width:100%;height:100%;object-fit:cover;display:block;" />
                        </div>
                    </div>
                </div>

                <!-- Contact form card: demo client-side behavior storing messages in localStorage -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Send us a message</h5>
                            <p class="text-muted">Use the form below and we'll get back to you within 24 hours.</p>

                            <!-- The form is client-side only for demo: in production submit to server -->
                            <form id="contactForm" novalidate>
                                <div class="row g-2">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="name">Your name</label>
                                        <input id="name" name="name" type="text" class="form-control" placeholder="Jane Doe" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="email">Email</label>
                                        <input id="email" name="email" type="email" class="form-control" placeholder="you@example.com" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="message">Message</label>
                                        <textarea id="message" name="message" class="form-control" rows="4" placeholder="How can we help?" required></textarea>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex align-items-center">
                                    <button type="submit" class="btn btn-warning me-3">Send message</button>
                                    <div id="contactStatus" class="text-success small" style="display:none;">Message saved (demo)</div>
                                </div>
                            </form>

                            <script>
                                (function(){
                                    // Contact form client-side handler (demo only)
                                    // - Validates fields on submit
                                    // - Stores messages in localStorage as a demo persistence layer
                                    // - Shows a temporary success notice
                                    const form = document.getElementById('contactForm');
                                    const status = document.getElementById('contactStatus');
                                    form.addEventListener('submit', function(e){
                                        e.preventDefault();
                                        // simple client-side validation
                                        const name = document.getElementById('name').value.trim();
                                        const email = document.getElementById('email').value.trim();
                                        const message = document.getElementById('message').value.trim();
                                        if (!name || !email || !message) { alert('Please fill in all fields'); return; }
                                        if (!/.+@.+\..+/.test(email)) { alert('Please enter a valid email'); return; }
                                        // demo behaviour: store message in localStorage and show success
                                        try { const messages = JSON.parse(localStorage.getItem('contact_messages')||'[]'); messages.unshift({name, email, message, date:(new Date()).toISOString()}); localStorage.setItem('contact_messages', JSON.stringify(messages)); } catch (err) {}
                                        form.reset();
                                        status.style.display = 'inline-block';
                                        setTimeout(()=> status.style.display = 'none', 4000);
                                    });
                                })();
                            </script>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Page footer note: inviting text -->
    <div class="row mt-4">
        <div class="col text-center text-muted small">
            <em>Visit us for great coffee and friendly service — we look forward to welcoming you.</em>
        </div>
    </div>
</div>
