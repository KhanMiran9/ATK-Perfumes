<style>
/* ---------- (unchanged structure, but animation now uses CSS variable --ticker-duration) ---------- */
.section-template--21750111207753__ss_scrolling_text_8_NbyhTP {
    border-top: solid #000000 0px;
    border-bottom: solid #000000 0px;
    /* margin-top: 27px;
    margin-bottom: 27px; */
    margin-left: 0rem;
    margin-right: 0rem;
    border-radius: 0px;
    overflow: hidden;
}

.section-template--21750111207753__ss_scrolling_text_8_NbyhTP-settings {
    margin: 0 auto;
    padding-top: 10px;
    padding-bottom: 10px;
    padding-left: 0rem;
    padding-right: 0rem;
}

.scrolling-scrolling-wrap-template--21750111207753__ss_scrolling_text_8_NbyhTP {
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: nowrap;
    flex-wrap: nowrap;
    background-attachment: scroll !important;
    transform-origin: center;
    overflow: hidden;
}

/* Use CSS variable for duration so JS can override dynamically */
.scrolling-scrolling-list-template--21750111207753__ss_scrolling_text_8_NbyhTP {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    align-items: center;
    white-space: nowrap;
    background-attachment: scroll !important;

    /* animation uses half-width loop (see keyframes) and duration is controlled by --ticker-duration */
    animation: tickertemplate21750111207753__ss_scrolling_text_8_NbyhTP var(--ticker-duration, 4s) linear infinite;
    flex-shrink: 0;
    width: max-content; /* make sure width is the content width */
}

/* ensure each direct child won't shrink */
.scrolling-scrolling-list-template--21750111207753__ss_scrolling_text_8_NbyhTP > * {
    flex: none;
}

.scrolling-scrolling-item-template--21750111207753__ss_scrolling_text_8_NbyhTP {
    background-attachment: scroll !important;
    flex-shrink: 0;
    text-decoration: none;
}

@media(min-width: 1024px) {
    .section-template--21750111207753__ss_scrolling_text_8_NbyhTP {
        /* margin-top: 36px;
        margin-bottom: 36px; */
        margin-left: 0rem;
        margin-right: 0rem;
        border-radius: 0px;
    }

    .section-template--21750111207753__ss_scrolling_text_8_NbyhTP-settings {
        padding: 0 5rem;
        padding-top: 16px;
        padding-bottom: 16px;
        padding-left: 0rem;
        padding-right: 0rem;
    }

    /* default on desktop if JS doesn't override */
    .scrolling-scrolling-list-template--21750111207753__ss_scrolling_text_8_NbyhTP {
        --ticker-duration: 40s;
    }
}

/* Keyframes move by HALF the total list width (works when you duplicate the contents) */
@keyframes tickertemplate21750111207753__ss_scrolling_text_8_NbyhTP {
    0% {
        transform: translateX(0%);
    }
    100% {
        transform: translateX(-50%);
    }
}

/* pause on hover */
.scrolling-scrolling-wrap-template--21750111207753__ss_scrolling_text_8_NbyhTP:hover
.scrolling-scrolling-list-template--21750111207753__ss_scrolling_text_8_NbyhTP {
    animation-play-state: paused;
}

/* fontfaces left as-is */
@font-face {
    font-family: "Bauer Bodoni";
    font-weight: 900;
    font-style: normal;
    font-display: swap;
    src: url("http://section.store/cdn/fonts/bauer_bodoni/bauerbodoni_n9.b3c139cf3849eea2ee0ea554263e28813f38a8db.woff2?h1=c2VjdGlvbi5zdG9yZQ&amp;h2=c2VjdGlvci1zdG9yZS1hcHAuYWNjb3VudC5teXNob3BpZnkuY29t&amp;hmac=e11bda0bd024eaedd9b5a0db08bfd03276a41f1e9d6e2cf1968ef7e83c09d4c9") format("woff2"),
        url("http://section.store/cdn/fonts/bauer_bodoni/bauerbodoni_n9.fc5385031e2d316cf5383d1b3ff6017b4758e476.woff?h1=c2VjdGlvbi5zdG9yZQ&amp;h2=c2VjdGlvci1zdG9yZS1hcHAuYWNjb3VudC5teXNob3BpZnkuY29t&amp;hmac=14afafc05667bf3fb993431111f85d7f5bb287ac903288580e54807475e5e969") format("woff");
}

.scrolling-scrolling-item-text_yWRPne {
    text-decoration: none;
}

/* PRIMARY TEXT COLOR: #550000 */
.scrolling-scrolling-text-text_yWRPne {
    margin: 0px;
    margin-right: 40px;
    font-size: 26px;
    color: black;
    line-height: 130%;
    text-transform: unset;
    letter-spacing: 0px;
    text-decoration: none;
}

/* STENCIL: gold fill + maroon stroke for visual contrast */
.scrolling-scrolling-item-text_yWRPne .scrolling-scrolling-stencill {
  
  color:black;

}
background: linear-gradient(90deg, #7F6051, #b58c65, #f3dab3, #b58c65, #7F6051);
    background-size: 300% 100%;
    animation: shine 20s 
linear infinite;

@media(min-width: 1024px) {
    .scrolling-scrolling-text-text_yWRPne {
        margin-right: 50px;
        font-size: 72px;
    }
}

.scrolling-scrolling-text-text_yWRPne {
    font-family: "Bauer Bodoni", serif;
    font-weight: 900;
    font-style: normal;
}

@font-face {
    font-family: "Josefin Sans";
    font-weight: 400;
    font-style: normal;
    font-display: swap;
    src: url("http://section.store/cdn/fonts/josefin_sans/josefinsans_n4.70f7efd699799949e6d9f99bc20843a2c86a2e0f.woff2?h1=c2VjdGlvbi5zdG9yZQ&amp;h2=c2VjdGli1zdG9yZS1hcHAuYWNjb3VudC5teXNob3BpZnkuY29t&amp;hmac=e832db7f2fbaa8dbad95725de26caa3c2c1fc321bdb1c87134b0047278170ea1") format("woff2"),
        url("http://section.store/cdn/fonts/josefin_sans/josefinsans_n4.35d308a1bdf56e5556bc2ac79702c721e4e2e983.woff?h1=c2VjdGlvbi5zdG9yZQ&amp;h2=c2VjdGli1zdG9yZS1hcHAuYWNjb3VudC5teXNob3BpZnkuY29t&amp;hmac=66166f79cdcaf2e6faf07e002f88afdb7005be4cc1fd509315215faba22b4749") format("woff");
}

.countdown-timer-timer_ywjraz {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-right: 16px;
}

.countdown-timer-timer_ywjraz .time-row {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.countdown-timer-timer_ywjraz .time-block__num {
    min-width: 27.0px;
    text-align: center;
}

.countdown-timer-timer_ywjraz .separator {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    right: -6.0px;
}

/* SECONDARY / ACCENT COLOR: #e3bc6c for countdown numbers / separators / units */
.countdown-timer-timer_ywjraz .time-block__num,
.countdown-timer-timer_ywjraz .separator {
    margin: 0px;
    font-size: 15px;
    color: black;
    line-height: 100%;
    text-transform: unset;
}

.countdown-timer-timer_ywjraz .time-block__unit {
    display: block;
    width: 100%;
    text-align: center;
    margin: 0px;
    margin-top: 10px;
    font-size: 12px;
    color: black;
    line-height: 100%;
    text-transform: unset;
}

@media(min-width: 1024px) {
    .countdown-timer-timer_ywjraz {
        margin-right: 50px;
    }

    .countdown-timer-timer_ywjraz .time-block__unit {
        margin-top: 10px;
        font-size: 16px;
    }

    .countdown-timer-timer_ywjraz .time-block__num,
    .countdown-timer-timer_ywjraz .separator {
        font-size: 26px;
    }

    .countdown-timer-timer_ywjraz .separator {
        right: -10.8px;
    }

    .countdown-timer-timer_ywjraz .time-block__num {
        min-width: 48.6px;
    }
}

.countdown-timer-timer_ywjraz .time-block__unit {
    font-family: "SF Mono", Menlo, Consolas, Monaco, Liberation Mono, Lucida Console, monospace, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol;
    font-weight: 400;
    font-style: normal;
}

.scrolling-image-image_TyUr8J {
    margin-right: 40px;
    width: 50px;
    overflow: hidden;
    border-radius: 12px;
    border: 0px solid #e3bc6c;
}

.scrolling-image-image_TyUr8J img,
.scrolling-image-image_TyUr8J svg {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.scrolling-image-image_TyUr8J svg {
    background-color: #F7F7F7;
}

@media(min-width: 1024px) {
    .scrolling-image-image_TyUr8J {
        margin-right: 50px;
        width: 100px;
    }
}

/* completed text color uses the secondary accent */
.countdown-timer-completed-text-timer_ywjraz {
    display: none;
    color: #e3bc6c;
    font-weight: 700;
}
</style>

<!-- Fixed anchor (closing quote + close tag placed just AFTER the visible div) -->
<a href="https://hkrhijabs.myshopify.com/collections/lifestyle-miscellaneous" style="display: block; text-decoration: none; color: inherit;">
    <div class="section-template--21750111207753__ss_scrolling_text_8_NbyhTP" style="background: linear-gradient(90deg, #7F6051, #b58c65, #f3dab3, #b58c65, #7F6051);
    background-size: 300% 100%;
    animation: shine 20s 
linear infinite;">
        <div class="section-template--21750111207753__ss_scrolling_text_8_NbyhTP-settings">
            <div class="scrolling-scrolling-wrap-template--21750111207753__ss_scrolling_text_8_NbyhTP">
                <div class="scrolling-scrolling-list-template--21750111207753__ss_scrolling_text_8_NbyhTP">

                    <!-- Limited Edition + Timer + Image 40 -->
                    <div class="scrolling-scrolling-item-template--21750111207753__ss_scrolling_text_8_NbyhTP scrolling-scrolling-item-text_yWRPne">
                        <span class="scrolling-scrolling-text-text_yWRPne">Limited <span class="scrolling-scrolling-stencill">Edition</span></span>
                    </div>
                    <div class="scrolling-scrolling-item-template--21750111207753__ss_scrolling_text_8_NbyhTP scrolling-scrolling-item-timer_ywjraz">
                        <div class="countdown-timer-timer_ywjraz">
                            <div class="time-block"><span class="time-block__num js-timer-days-timer_ywjraz">00</span><span class="separator">:</span><span class="time-block__unit">DAYS</span></div>
                            <div class="time-block"><span class="time-block__num js-timer-hours-timer_ywjraz">00</span><span class="separator">:</span><span class="time-block__unit">HOUR</span></div>
                            <div class="time-block"><span class="time-block__num js-timer-minutes-timer_ywjraz">00</span><span class="separator">:</span><span class="time-block__unit">MIN</span></div>
                            <div class="time-block"><span class="time-block__num js-timer-seconds-timer_ywjraz">00</span><span class="time-block__unit">SEC</span></div>
                        </div>
                    </div>
                    <div class="scrolling-scrolling-item-template--21750111207753__ss_scrolling_text_8_NbyhTP scrolling-scrolling-item-image_TyUr8J">
                        <div class="scrolling-image-image_TyUr8J">
                            <img src="assets/images/9_155f3ba4-9cd4-42a5-8660-d564920b1008.webp" alt="">
                        </div>
                    </div>

                    <!-- Limited Edition + Timer + Image 41 -->
                    <div class="scrolling-scrolling-item-template--21750111207753__ss_scrolling_text_8_NbyhTP scrolling-scrolling-item-text_yWRPne">
                        <span class="scrolling-scrolling-text-text_yWRPne">Limited <span class="scrolling-scrolling-stencill">Edition</span></span>
                    </div>
                    <div class="scrolling-scrolling-item-template--21750111207753__ss_scrolling_text_8_NbyhTP scrolling-scrolling-item-timer_ywjraz">
                        <div class="countdown-timer-timer_ywjraz">
                            <div class="time-block"><span class="time-block__num js-timer-days-timer_ywjraz">00</span><span class="separator">:</span><span class="time-block__unit">DAYS</span></div>
                            <div class="time-block"><span class="time-block__num js-timer-hours-timer_ywjraz">00</span><span class="separator">:</span><span class="time-block__unit">HOUR</span></div>
                            <div class="time-block"><span class="time-block__num js-timer-minutes-timer_ywjraz">00</span><span class="separator">:</span><span class="time-block__unit">MIN</span></div>
                            <div class="time-block"><span class="time-block__num js-timer-seconds-timer_ywjraz">00</span><span class="time-block__unit">SEC</span></div>
                        </div>
                    </div>
                    <div class="scrolling-scrolling-item-template--21750111207753__ss_scrolling_text_8_NbyhTP scrolling-scrolling-item-image_TyUr8J">
                        <div class="scrolling-image-image_TyUr8J">
                            <img src="assets/images/images (2).jpg" alt="">
                        </div>
                    </div>

                    <!-- Limited Edition + Timer + Image 42 -->
                    <div class="scrolling-scrolling-item-template--21750111207753__ss_scrolling_text_8_NbyhTP scrolling-scrolling-item-text_yWRPne">
                        <span class="scrolling-scrolling-text-text_yWRPne">Limited <span class="scrolling-scrolling-stencill">Edition</span></span>
                    </div>
                    <div class="scrolling-scrolling-item-template--21750111207753__ss_scrolling_text_8_NbyhTP scrolling-scrolling-item-timer_ywjraz">
                        <div class="countdown-timer-timer_ywjraz">
                            <div class="time-block"><span class="time-block__num js-timer-days-timer_ywjraz">00</span><span class="separator">:</span><span class="time-block__unit">DAYS</span></div>
                            <div class="time-block"><span class="time-block__num js-timer-hours-timer_ywjraz">00</span><span class="separator">:</span><span class="time-block__unit">HOUR</span></div>
                            <div class="time-block"><span class="time-block__num js-timer-minutes-timer_ywjraz">00</span><span class="separator">:</span><span class="time-block__unit">MIN</span></div>
                            <div class="time-block"><span class="time-block__num js-timer-seconds-timer_ywjraz">00</span><span class="time-block__unit">SEC</span></div>
                        </div>
                    </div>
                    <div class="scrolling-scrolling-item-template--21750111207753__ss_scrolling_text_8_NbyhTP scrolling-scrolling-item-image_TyUr8J">
                        <div class="scrolling-image-image_TyUr8J">
                            <img src="assets/images/images (1).jpg" alt="">
                        </div>
                    </div>

                    <!-- (You can keep or remove manual duplicates. JS below will duplicate once automatically if needed.) -->

                </div>
            </div>
        </div>
    </div>
</a>

<script>
(function () {
    // safe guard for multiple inits and get/remove intervals
    var _intervals = [];

    function clearAllIntervals() {
        _intervals.forEach(function(i){ clearInterval(i); });
        _intervals = [];
    }

    function initScrollingText8() {
        // clear first (avoid double intervals)
        clearAllIntervals();

        /* ---------- TICKER: duplicate content once and compute a good animation duration ---------- */
        var listSelectors = document.querySelectorAll('.scrolling-scrolling-list-template--21750111207753__ss_scrolling_text_8_NbyhTP');

        listSelectors.forEach(function(list) {
            // only clone once per list (theme editor re-init protection)
            if (list.dataset.tickerCloned === '1') return;

            // Duplicate the content exactly once so the final animation translateX(-50%) is seamless.
            // If the markup already contains manual duplicates, this will still work (we only duplicate once programmatically).
            list.innerHTML = list.innerHTML + list.innerHTML;
            list.dataset.tickerCloned = '1';

            // Force a reflow and compute width of half content (one copy)
            // Use requestAnimationFrame to ensure DOM updated
            requestAnimationFrame(function() {
                var totalWidth = list.scrollWidth || 1;
                var halfWidth = totalWidth / 2;

                // Choose speed (pixels per second). Increase to make ticker faster.
                var pixelsPerSecond = 100; // tweak this number to control scroll speed
                var durationSec = Math.max(8, Math.round(halfWidth / pixelsPerSecond));

                // Set CSS variable to control animation duration
                list.style.setProperty('--ticker-duration', durationSec + 's');
            });
        });

        /* ---------- COUNTDOWN TIMERS (unchanged, but each wrapper gets its own interval) ---------- */
        var timers = document.querySelectorAll('.countdown-timer-timer_ywjraz');
        if (!timers || timers.length === 0) return;

        timers.forEach(function(wrapper) {
            // wrapper is the .countdown-timer-timer_ywjraz element
            var second = 1000,
                minute = second * 60,
                hour = minute * 60,
                day = hour * 24;

            // Set the countdown to 100 days from now
            var now = new Date();
            var countDown = new Date(now.getTime() + (100 * day));

            var interval = setInterval(function() {
                var currentTime = new Date().getTime();
                var distance = countDown - currentTime;

                var days = Math.max(0, Math.floor(distance / day));
                var hours = Math.max(0, Math.floor((distance % day) / hour));
                var minutes = Math.max(0, Math.floor((distance % hour) / minute));
                var seconds = Math.max(0, Math.floor((distance % minute) / second));

                var elDays = wrapper.querySelector('.js-timer-days-timer_ywjraz');
                var elHours = wrapper.querySelector('.js-timer-hours-timer_ywjraz');
                var elMinutes = wrapper.querySelector('.js-timer-minutes-timer_ywjraz');
                var elSeconds = wrapper.querySelector('.js-timer-seconds-timer_ywjraz');

                if (elDays) elDays.innerText = String(days).padStart(2, '0');
                if (elHours) elHours.innerText = String(hours).padStart(2, '0');
                if (elMinutes) elMinutes.innerText = String(minutes).padStart(2, '0');
                if (elSeconds) elSeconds.innerText = String(seconds).padStart(2, '0');

                if (distance < 0) {
                    if (elDays) elDays.innerText = '00';
                    if (elHours) elHours.innerText = '00';
                    if (elMinutes) elMinutes.innerText = '00';
                    if (elSeconds) elSeconds.innerText = '00';

                    // show the completed text that sits alongside this wrapper
                    var completed = wrapper.parentElement && wrapper.parentElement.querySelector('.countdown-timer-completed-text-timer_ywjraz');
                    if (completed) {
                        completed.style.display = 'block';
                        completed.style.color = '#e3bc6c';
                        if (!completed.innerText) completed.innerText = 'EXPIRED';
                    }
                    clearInterval(interval);
                }
            }, second);

            _intervals.push(interval);
        });
    }

    // initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScrollingText8);
    } else {
        initScrollingText8();
    }

    // clear intervals on shopify section unload (theme editor safety)
    document.addEventListener('shopify:section:unload', function(event) {
        // always clear intervals when a section unloads to avoid leaks
        clearAllIntervals();
    });

    // In theme editor, re-init on section load
    if (typeof Shopify !== "undefined" && Shopify.designMode) {
        document.addEventListener('shopify:section:load', function(event){
            initScrollingText8();
        });
    }

    // If someone removes block or leaves page, clear on beforeunload as extra safety
    window.addEventListener('beforeunload', clearAllIntervals);
})();
</script>