"use strict";

import modal, { closeModal } from "./components/modal";
import testimonials from "./modules/testimonials";
import blog_content from "./template-parts/blog_content";
import mobileMenuToggle from "./components/mobile-menu-toggle";
import single_post from "./template-parts/single_post";
import search from "./components/search";
import smoothScroll from "./modules/smooth-scroll";
import productFilter from "./components/product-filter";
import productSorting from "./components/product-sorting";
import productFilterToggle from "./components/product-filter-toggle";
import equalHeight from "./modules/equal-height";
import checkout from "./template-parts/checkout";
import sitePreloader from "./modules/site-preloader";
import cart from "./components/cart";
import { openModal } from "./components/modal";
import wowAnimate from "./modules/wow-animate";

document.addEventListener("DOMContentLoaded", function () {
    const shown = localStorage.getItem("modalShown");

    if (document.querySelector(".testimonials__slider")) {
        testimonials();
    }
    if (document.querySelector(".single-post")) {
        single_post();
    }
    if (document.querySelector(".blog")) {
        blog_content();
    }
    if (document.querySelector(".menu-btn")) {
        mobileMenuToggle();
    }
    if (document.querySelector(".search-form")) {
        search();
    }
    if (document.querySelector("a[data-smooth-scroll]")) {
        smoothScroll();
    }
    if (document.querySelector("#category-filter-form")) {
        productFilter();
    }
    if (document.querySelector(".orderby")) {
        productSorting();
    }
    if (document.querySelector(".product-filter__mobile-filter-button")) {
        productFilterToggle();
    }
    if (document.querySelector(".woocommerce-cart")) {
        cart();
    }

    if (document.querySelector(".woocommerce-loop-product__title")) {
        equalHeight();
    }

    if (shown !== "true") {
        const modalTimerId = setTimeout(
            () => openModal(".modal", modalTimerId),
            10000
        );
        modal(".modal", modalTimerId);
    }

    if (document.querySelector(".site-preloader")) {
        sitePreloader();
    }

    if (document.querySelector(".checkout-section__form")) {
        checkout();
    }

    wowAnimate();

    window.addEventListener("resize", equalHeight);
});
