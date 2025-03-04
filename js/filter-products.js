document.addEventListener("DOMContentLoaded", function () {
  let form = document.querySelector("#product-filter-form");
  let productResults = document.querySelector("#product-results");
  let productCount = document.querySelector("#product-count");
  let activeFilters = document.querySelector("#active-filters");

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

  function updateActiveFilters() {
    let selectedCategories = Array.from(
      form.querySelectorAll("input[name='product_cat[]']:checked")
    ).map((el) => el.labels[0]?.textContent.trim());

    let selectedTags = Array.from(
      form.querySelectorAll("input[name='product_tag[]']:checked")
    ).map((el) => el.labels[0]?.textContent.trim());

    let filtersHtml = "";

    if (selectedCategories.length > 0) {
      filtersHtml += `<strong>Categories:</strong> ${selectedCategories
        .map((cat) => `<span class="badge bg-primary">${cat}</span>`)
        .join(" ")}`;
    }
    if (selectedTags.length > 0) {
      filtersHtml += `<strong> Tags:</strong> ${selectedTags
        .map((tag) => `<span class="badge bg-secondary">${tag}</span>`)
        .join(" ")}`;
    }

    // Add Reset Button If Filters Are Applied
    if (selectedCategories.length > 0 || selectedTags.length > 0) {
      filtersHtml += ` <span id="reset-filters" class="badge bg-danger text-white" style="cursor: pointer;">Reset</span>`;
    }

    activeFilters.innerHTML = filtersHtml || "";

    // Attach Event Listener to Reset Button
    let resetButton = document.querySelector("#reset-filters");
    if (resetButton) {
      resetButton.addEventListener("click", function () {
        // Uncheck all checkboxes
        document
          .querySelectorAll("#product-filter-form input[type='checkbox']")
          .forEach((checkbox) => {
            checkbox.checked = false;
          });

        // Trigger filtering to reset products
        updateProducts();

        setTimeout(() => {
          updateFilters();
          updateActiveFilters();
        }, 250);
      });
    }
  }

  // Call updateFilters() after products are updated
  document
    .querySelector("#product-filter-form")
    .addEventListener("change", function () {
      updateProducts();
      setTimeout(() => {
        updateFilters();
        updateActiveFilters();
      }, 250);
    });

  // Trigger AJAX filtering on load
  updateProducts();

  // Event listener for filter changes (Category & Tag checkboxes)
  form.addEventListener("change", function () {
    updateProducts();
  });

  let filterContainer = document.getElementById("product-filter-container");
  let offcanvasContainer = document.getElementById(
    "offcanvas-filter-container"
  );
  let offcanvas = document.getElementById("productFilterSidebar");

  if (filterContainer && offcanvasContainer && offcanvas) {
    let originalParent = filterContainer.parentNode; // Store original sidebar location

    offcanvas.addEventListener("show.bs.offcanvas", function () {
      // Show filters when inside the offcanvas
      filterContainer.classList.remove("d-none");
      offcanvasContainer.appendChild(filterContainer);
    });

    offcanvas.addEventListener("hidden.bs.offcanvas", function () {
      // Hide filters when switching back to sidebar on mobile
      filterContainer.classList.add("d-none");
      originalParent.appendChild(filterContainer);
    });
  }
});
