document.addEventListener("DOMContentLoaded", function () {
  let form = document.querySelector("#product-filter-form");
  let productResults = document.querySelector("#product-results");
  let productCount = document.querySelector("#product-count");

  // Use ajax_object.ajaxurl if available, otherwise fallback to global ajaxurl
  let ajaxUrl =
    typeof ajax_object !== "undefined"
      ? ajax_object.ajaxurl
      : typeof ajaxurl !== "undefined"
      ? ajaxurl
      : null;

  if (!ajaxUrl) {
    console.error(
      "AJAX URL is not defined. Check wp_localize_script or wp_add_inline_script in PHP."
    );
    return;
  }

  // Function to update products dynamically
  function updateProducts() {
    productResults.innerHTML =
      '<p class="loading-message">Loading products...</p>'; // Show loading message
    productCount.innerHTML = "Loading products...";

    let formData = new FormData(form);

    fetch(ajaxUrl, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(formData) + "&action=filter_products",
    })
      .then((response) => response.text())
      .then((data) => {
        // Extract product count from response
        let parser = new DOMParser();
        let doc = parser.parseFromString(data, "text/html");
        let products = doc.querySelectorAll(".product").length; // Count product elements

        // Update results count
        productCount.innerHTML =
          products > 0 ? `${products} results found` : "No products found";

        // Update product results
        productResults.innerHTML = data;
      })
      .catch((error) => {
        console.error("Error fetching products:", error);
        productResults.innerHTML =
          '<p class="error-message">Failed to load products. Please try again.</p>';
        productCount.innerHTML = "Error loading products";
      });
  }

  // Function to update available categories & tags
  function updateFilters() {
    let selectedCategories = Array.from(
      document.querySelectorAll(
        ".filter-group input[name='product_cat[]']:checked"
      )
    ).map((checkbox) => checkbox.value);

    let selectedTags = Array.from(
      document.querySelectorAll(
        ".filter-group input[name='product_tag[]']:checked"
      )
    ).map((checkbox) => checkbox.value);

    let categoryParam =
      selectedCategories.length > 0 ? selectedCategories.join(",") : "none";
    let tagParam = selectedTags.length > 0 ? selectedTags.join(",") : "none";

    // console.log("Sending Filters:", { selectedCategories, selectedTags });

    fetch(ajaxUrl, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        action: "update_filters",
        selected_categories: categoryParam,
        selected_tags: tagParam,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        // console.log("Filter data received:", data);

        document
          .querySelectorAll(".filter-group input[name='product_cat[]']")
          .forEach((checkbox) => {
            checkbox.disabled = !data.categories.includes(checkbox.value);
          });

        document
          .querySelectorAll(".filter-group input[name='product_tag[]']")
          .forEach((checkbox) => {
            checkbox.disabled = !data.tags.includes(checkbox.value);
          });
      })
      .catch((error) => console.error("Error updating filters:", error));
  }

  // Call updateFilters() after products are updated
  document
    .querySelector("#product-filter-form")
    .addEventListener("change", function () {
      updateProducts();
      setTimeout(updateFilters, 500); // Allow products to load before updating filters
    });

  // Trigger AJAX filtering on load
  updateProducts();

  // Event listener for filter changes (Category & Tag checkboxes)
  form.addEventListener("change", function () {
    updateProducts();
  });
});
