"use strict";

export default function () {
    const class_form = ".mailchimp-form";
    const forms = document.querySelectorAll('.class_form');

    /**
     * Sets a cookie with the given name and value.
     *
     * @param {string} name - The name of the cookie.
     * @param {string} value - The value to be stored in the cookie.
     */
    function setCookie(name, value) {
        const daysToExpire = 2;
        const date = new Date();
        const expires = `expires=${date.toUTCString()}`;
        date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};${expires};path=/`;
    }

    /**
     * Retrieves the value of a cookie with the given name from the document's cookies.
     *
     * @param {string} name - The name of the cookie to retrieve.
     * @return {string} The value of the cookie, or an empty string if the cookie was not found.
     */
    function getCookie(name) {
        const cookieName = name + "=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookieArray = decodedCookie.split(';');

        for (let i = 0; i < cookieArray.length; i++) {
            let cookie = cookieArray[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(cookieName) === 0)  return cookie.substring(cookieName.length);

        }

        return "";
    }

    /**
     * Creates an IntersectionObserver that observes a set of target elements for intersection.
     *
     * @param {Function} callback - The callback function to be called when an observed element intersects the root element.
     * @param {Object} options - The options for configuring the IntersectionObserver.
     *   - {Element} root - The root element to use for intersection. If null, the viewport is used.
     *   - {string} rootMargin - A margin around the root element's bounding box. Can have values similar to CSS margin property.
     *   - {number|number[]} threshold - The threshold(s) at which to trigger the callback function. Can be a single number or an array of numbers between 0 and 1.
     */
    const options = {
        root: null,
        rootMargin: "0px",
        threshold: 0
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                callback($(element));
                observer.unobserve(element);
            }
        });
    }, options);

    target.each((i, e) => {
        observer.observe(e)
    });
}