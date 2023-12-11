class Tabs {

    constructor(options) {
        const defaultOption = {
            selector: ".tabs-list",
            activeClass: "active",
            checkHash: true,
            tabLinks: "a",
            attribute: "href",
            event: "click",
            onChange: null
        };
        this.options = {
            ...defaultOption,
            ...options
        };

        return this.init(this.options);
    }
    /**
     * Initializes the tabs using the provided options.
     *
     * @param {Object} options - The options for initializing the tabs.
     */
    init(options) {
        const tabs = document.querySelectorAll(options.selector);
        tabs.forEach(element => {
            this.setInitialState(element);
        });
    }
    /**
     * Updates the specified tabs based on the given selector.
     *
     * @param {string} selector - The CSS selector for the tabs to be updated. If not provided, the default selector will be used.
     * @return {void} This function does not return a value.
     */
    update(selector) {
        const tabs = document.querySelectorAll(selector || this.options.selector);
        tabs.forEach(element => {
            this.setInitialState(element);
        });
    }

    /**
     * Sets the initial state of the element.
     *
     * @param {Element} element - The element to set the initial state for.
     */
    setInitialState(element) {
        const links = element.querySelectorAll(this.options.tabLinks);
        this.addEvents(links);
        let historyLink = null;
        if (this.options.checkHash && window.location.hash) {
            historyLink = element.querySelector(
                `[${this.options.attribute}="${window.location.hash}"]`
            );
        }
        if (historyLink) {
            this.setActiveTab(historyLink);
        } else {
            links.forEach((link, index) => {
                if (index === 0) {
                    this.setActiveTab(link);
                }
            });
        }
    }
    /**
     * Adds event listeners to the given links.
     *
     * @param {Array} links - An array of DOM elements representing links.
     */
    addEvents(links) {
        links.forEach(link => {
            link.addEventListener(this.options.event, event => {
                event.preventDefault();
                if (!event.currentTarget.classList.contains(this.options.activeClass)) {
                    this.setActiveTab(link);
                }
            });
        });
    }
    /**
     * Sets the active tab and performs necessary actions.
     *
     * @param {HTMLElement} activeTab - The tab element to set as active.
     */
    setActiveTab(activeTab) {
        activeTab.classList.add(this.options.activeClass);
        const activeTabID = activeTab.getAttribute(this.options.attribute);
        if (activeTabID === "#") return;
        const activeTabBlock = document.querySelector(activeTabID);
        if (activeTabBlock) {
            activeTabBlock.classList.add("active");
        }
        this.removeTabs(activeTab);
        if (typeof this.options.onChange === "function") {
            this.options.onChange();
        }
    }
    /**
     * Removes the "active" class from all tab links and the corresponding tab blocks,
     * except for the active tab.
     *
     * @param {Element} activeTab - The currently active tab link element.
     */
    removeTabs(activeTab) {
        const tabNav = activeTab.closest(this.options.selector);
        tabNav.querySelectorAll(this.options.tabLinks).forEach(element => {
            if (element !== activeTab) {
                element.classList.remove("active");
                const tabID = element.getAttribute(this.options.attribute);
                const tabBlock = document.querySelector(tabID);
                if (tabBlock) {
                    tabBlock.classList.remove("active");
                }
            }
        });
    }
}

export default Tabs;