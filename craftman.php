<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATK Perfumes | Craftsmanship Process</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gold: #d4af37;
            --silver: #c0c0c0;
            --black: #0a0a0a;
            --dark-gray: #1a1a1a;
            --light-gray: #f5f5f5;
            --white: #ffffff;
            --muted: #8a8580;
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --radius: 8px;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.2);
            --gradient-gold: linear-gradient(45deg, #a66d30, #ffe58e 50%, #e0b057 100%);
            --gradient-silver: linear-gradient(45deg, #7f7f7f, #d9d9d9 50%, #a6a6a6 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--black);
            line-height: 1.6;
            background-color: var(--white);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Cinzel', serif;
            font-weight: 600;
            line-height: 1.2;
        }

        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section {
            padding: 6rem 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--gradient-gold);
        }

        .section-subtitle {
            color: var(--muted);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Modern Process Section */
        .modern-process {
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
            position: relative;
            overflow: hidden;
        }

        .process-timeline {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }

        .process-timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 4px;
            background: var(--gradient-gold);
            transform: translateX(-50%);
            z-index: 1;
        }

        .process-step {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 100px;
            position: relative;
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .process-step.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .process-step:nth-child(odd) {
            flex-direction: row;
        }

        .process-step:nth-child(even) {
            flex-direction: row-reverse;
        }

        .step-content {
            flex: 1;
            padding: 40px;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            position: relative;
            z-index: 2;
            transition: var(--transition);
        }

        .process-step:nth-child(odd) .step-content {
            margin-right: 40px;
            transform: translateX(-100px);
        }

        .process-step:nth-child(even) .step-content {
            margin-left: 40px;
            transform: translateX(100px);
        }

        .process-step.visible:nth-child(odd) .step-content {
            transform: translateX(0);
        }

        .process-step.visible:nth-child(even) .step-content {
            transform: translateX(0);
        }

        .step-content:hover {
            transform: translateY(-10px) !important;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .step-number {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 60px;
            background: var(--gradient-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cinzel', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--black);
            box-shadow: var(--shadow);
            z-index: 3;
        }

        .step-title {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--black);
            position: relative;
            padding-bottom: 10px;
        }

        .step-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--gradient-gold);
        }

        .step-description {
            color: var(--muted);
            line-height: 1.7;
        }

        .step-icon {
            flex: 0 0 200px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
        }

        .step-icon-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .step-icon-inner:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-gold);
            opacity: 0.1;
        }

        .step-icon i {
            font-size: 4rem;
            color: var(--gold);
        }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            .process-timeline::before {
                left: 30px;
            }

            .process-step {
                flex-direction: row !important;
                margin-bottom: 60px;
            }

            .step-content {
                margin-left: 80px !important;
                margin-right: 0 !important;
                transform: translateX(0) !important;
            }

            .process-step.visible .step-content {
                transform: translateX(0) !important;
            }

            .step-icon {
                position: absolute;
                left: 0;
                flex: 0 0 60px;
                height: 60px;
                z-index: 3;
            }

            .step-icon-inner {
                width: 60px;
                height: 60px;
            }

            .step-icon i {
                font-size: 1.5rem;
            }

            .step-number {
                top: -15px;
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
        }

        @media (max-width: 768px) {
            .section {
                padding: 4rem 0;
            }

            .section-title {
                font-size: 2rem;
            }

            .step-content {
                padding: 30px 20px;
            }

            .step-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 1.5rem;
            }

            .step-content {
                margin-left: 60px !important;
            }

            .step-icon {
                flex: 0 0 50px;
            }

            .step-icon-inner {
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Process Section -->
    <section class="section modern-process" id="process">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Craftsmanship Process</h2>
                <p class="section-subtitle">Each fragrance undergoes a meticulous creation process that takes months to perfect.</p>
            </div>
            
            <div class="process-timeline">
                <!-- Step 1 -->
                <div class="process-step">
                    <div class="step-content">
                        <div class="step-number">01</div>
                        <h3 class="step-title">Inspiration</h3>
                        <p class="step-description">Each fragrance begins with a story, a memory, or an emotion that we want to capture. Our creative team draws inspiration from art, nature, and personal experiences to conceptualize unique scent profiles.</p>
                    </div>
                    <div class="step-icon">
                        <div class="step-icon-inner">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="process-step">
                    <div class="step-content">
                        <div class="step-number">02</div>
                        <h3 class="step-title">Sourcing</h3>
                        <p class="step-description">We carefully select the finest natural ingredients from trusted partners around the world. From Bulgarian rose petals to Madagascan vanilla, every component is ethically sourced and rigorously tested for purity.</p>
                    </div>
                    <div class="step-icon">
                        <div class="step-icon-inner">
                            <i class="fas fa-globe-americas"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="process-step">
                    <div class="step-content">
                        <div class="step-number">03</div>
                        <h3 class="step-title">Creation</h3>
                        <p class="step-description">Our master perfumers blend notes with precision, often taking months to perfect a single fragrance. Using both traditional techniques and modern technology, we create complex scent architectures.</p>
                    </div>
                    <div class="step-icon">
                        <div class="step-icon-inner">
                            <i class="fas fa-flask"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Step 4 -->
                <div class="process-step">
                    <div class="step-content">
                        <div class="step-number">04</div>
                        <h3 class="step-title">Aging</h3>
                        <p class="step-description">The blended fragrance is aged in temperature-controlled environments to allow the notes to marry and develop complexity. This crucial step can take from several weeks to several months.</p>
                    </div>
                    <div class="step-icon">
                        <div class="step-icon-inner">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="process-step">
                    <div class="step-content">
                        <div class="step-number">05</div>
                        <h3 class="step-title">Bottling</h3>
                        <p class="step-description">Each perfume is hand-bottled in our atelier using custom-designed glassware. Our attention to detail extends to every aspect, from the precision of the fill level to the finishing of the cap.</p>
                    </div>
                    <div class="step-icon">
                        <div class="step-icon-inner">
                            <i class="fas fa-wine-bottle"></i>
                        </div>
                    </div>
                </div>

                <!-- Step 6 -->
                <div class="process-step">
                    <div class="step-content">
                        <div class="step-number">06</div>
                        <h3 class="step-title">Quality Control</h3>
                        <p class="step-description">Every batch undergoes rigorous quality control testing to ensure consistency, longevity, and safety. Only after passing our strict standards does a fragrance receive the ATK Perfumes seal of approval.</p>
                    </div>
                    <div class="step-icon">
                        <div class="step-icon-inner">
                            <i class="fas fa-award"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Scroll animation for process steps
        document.addEventListener('DOMContentLoaded', function() {
            const processSteps = document.querySelectorAll('.process-step');
            
            // Create Intersection Observer to detect when elements are in view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.3, // Trigger when 30% of element is visible
                rootMargin: '0px 0px -50px 0px' // Adjust trigger point
            });
            
            // Observe each process step
            processSteps.forEach(step => {
                observer.observe(step);
            });
            
            // Add hover effects for desktop
            if (window.innerWidth > 992) {
                processSteps.forEach(step => {
                    step.addEventListener('mouseenter', function() {
                        this.querySelector('.step-content').style.transform = 'translateY(-10px)';
                    });
                    
                    step.addEventListener('mouseleave', function() {
                        this.querySelector('.step-content').style.transform = 'translateY(0)';
                    });
                });
            }
        });
    </script>
</body>
</html>