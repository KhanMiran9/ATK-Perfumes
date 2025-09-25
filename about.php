<?php
require_once 'includes/config.php';
require_once 'includes/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | LuxePerfume</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="about-hero">
        <div class="container">
            <div class="about-hero-content">
                <h1>About LuxePerfume</h1>
                <p>Crafting unforgettable fragrances since 2010</p>
            </div>
        </div>
    </section>

    <section class="about-story">
        <div class="container">
            <div class="story-content">
                <div class="story-text">
                    <h2>Our Story</h2>
                    <p>Founded in 2010, LuxePerfume began as a small boutique perfumery with a passion for creating exceptional scents that tell a story. Our journey started in a quaint studio in Paris, where our master perfumers combined traditional techniques with innovative approaches.</p>
                    <p>Today, we've grown into a globally recognized brand, but our commitment to quality, craftsmanship, and authenticity remains unchanged. Each fragrance is still meticulously crafted by hand, using only the finest ingredients from around the world.</p>
                </div>
                <div class="story-image">
                    <img src="assets/images/about-story.jpg" alt="Our perfumery studio">
                </div>
            </div>
        </div>
    </section>

    <section class="about-values">
        <div class="container">
            <h2>Our Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">🌿</div>
                    <h3>Sustainability</h3>
                    <p>We're committed to ethical sourcing and environmentally responsible practices throughout our production process.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">✨</div>
                    <h3>Quality</h3>
                    <p>Every ingredient is carefully selected and every bottle is crafted with precision and attention to detail.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🎨</div>
                    <h3>Creativity</h3>
                    <p>We believe in pushing boundaries and creating unique, innovative fragrances that stand out.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h3>Community</h3>
                    <p>We support local artisans and maintain fair trade practices with our partners worldwide.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-team">
        <div class="container">
            <h2>Meet Our Master Perfumers</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="assets/images/team-1.jpg" alt="Isabelle Laurent">
                    <h3>Isabelle Laurent</h3>
                    <p>Head Perfumer</p>
                    <p>With over 25 years of experience, Isabelle brings French elegance to every creation.</p>
                </div>
                <div class="team-member">
                    <img src="assets/images/team-2.jpg" alt="Marco Ricci">
                    <h3>Marco Ricci</h3>
                    <p>Senior Perfumer</p>
                    <p>Marco's Italian heritage influences his warm, passionate approach to fragrance design.</p>
                </div>
                <div class="team-member">
                    <img src="assets/images/team-3.jpg" alt="Sophie Chen">
                    <h3>Sophie Chen</h3>
                    <p>Creative Director</p>
                    <p>Sophie blends Eastern and Western influences to create truly unique scent experiences.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-process">
        <div class="container">
            <h2>Our Craftsmanship Process</h2>
            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h3>Inspiration</h3>
                    <p>Each fragrance begins with a story, a memory, or an emotion that we want to capture.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h3>Sourcing</h3>
                    <p>We carefully select the finest natural ingredients from trusted partners around the world.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h3>Creation</h3>
                    <p>Our perfumers blend notes with precision, often taking months to perfect a single fragrance.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">4</div>
                    <h3>Testing</h3>
                    <p>Every formulation undergoes rigorous testing to ensure longevity, sillage, and quality.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">5</div>
                    <h3>Bottling</h3>
                    <p>Each bottle is hand-filled and inspected to meet our exacting standards.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-commitment">
        <div class="container">
            <div class="commitment-content">
                <h2>Our Commitment to Excellence</h2>
                <p>At LuxePerfume, we believe that a great fragrance should not only smell beautiful but also tell a story and evoke emotions. That's why we pour our passion into every bottle, ensuring that each scent is as unique and special as the person who wears it.</p>
                <p>We invite you to explore our collection and discover the perfect fragrance that speaks to you.</p>
                <a href="shop.php" class="btn btn-primary">Explore Our Collection</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>