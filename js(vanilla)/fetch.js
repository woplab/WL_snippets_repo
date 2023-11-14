export default function () {
    const sortForm = document.querySelector('.woocommerce-ordering');
    const orderBySelect = document.querySelector(".orderby");
    const productListContainer = document.querySelector(".shop__list");

    sortForm.addEventListener("submit", function(event) {
        event.preventDefault();

        orderBySelect.addEventListener("change", function(event) {
            event.preventDefault();
            const selectedValue = orderBySelect.value;

            const formData = new FormData();
            formData.append("orderby", selectedValue);
            formData.append("action", "update_product_list");

            fetch(theme_data.url, {
                method: "POST",
                body: formData,
            })
                .then(response => response.text())
                .then(data => {
                    productListContainer.innerHTML = data;
                })
                .catch(error => console.error("Error:", error));
        });
    });
}