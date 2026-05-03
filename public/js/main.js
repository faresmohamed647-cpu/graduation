(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.sticky-top').css('top', '0px');
        } else {
            $('.sticky-top').css('top', '-100px');
        }
    });
    
    
    // Dropdown on mouse hover
    const $dropdown = $(".dropdown");
    const $dropdownToggle = $(".dropdown-toggle");
    const $dropdownMenu = $(".dropdown-menu");
    const showClass = "show";
    
    $(window).on("load resize", function() {
        if (this.matchMedia("(min-width: 992px)").matches) {
            $dropdown.hover(
            function() {
                const $this = $(this);
                $this.addClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "true");
                $this.find($dropdownMenu).addClass(showClass);
            },
            function() {
                const $this = $(this);
                $this.removeClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "false");
                $this.find($dropdownMenu).removeClass(showClass);
            }
            );
        } else {
            $dropdown.off("mouseenter mouseleave");
        }
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Facts counter
    $('[data-toggle="counter-up"]').counterUp({
        delay: 10,
        time: 2000
    });


    // Header carousel
    $(".header-carousel").owlCarousel({
        autoplay: false,
        smartSpeed: 1500,
        items: 1,
        dots: false,
        loop: true,
        nav : true,
        navText : [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ]
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: false,
        smartSpeed: 1000,
        center: true,
        dots: true,
        loop: true,
        responsive: {
            0:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });
    
})(jQuery);
const childrenInput = document.getElementById("children");
const currentCostInput = document.getElementById("currentCost");
const savingEl = document.getElementById("saving");
const currentTotalEl = document.getElementById("currentTotal");
const schoolTotalEl = document.getElementById("schoolTotal");

const planRadios = document.querySelectorAll('input[name="plan"]');
const paymentRadios = document.querySelectorAll('input[name="payment"]');

function calculate() {
    const children = +childrenInput.value;
    const currentCost = +currentCostInput.value;

    let planPrice = 0;
    planRadios.forEach(r => r.checked && (planPrice = +r.value));

    let discount = 1;
    paymentRadios.forEach(r => {
        if (r.checked) {
            if (r.value === "quarterly") discount = 0.9;
            if (r.value === "yearly") discount = 0.75;
        }
    });

    const currentTotal = children * currentCost;
    const schoolTotal = children * planPrice * discount;
const saving = Math.max(0, currentTotal - schoolTotal);
    currentTotalEl.textContent = currentTotal.toFixed(0);
    schoolTotalEl.textContent = schoolTotal.toFixed(0);
    savingEl.textContent = saving.toFixed(0) + " EGP";
}

document.querySelectorAll("input").forEach(i =>
    i.addEventListener("input", calculate)
);

calculate();


