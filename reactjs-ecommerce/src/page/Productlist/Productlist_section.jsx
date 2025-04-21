import React, { useState, useEffect } from "react";
import { useProduct } from "../../context/ProductContext";
import { useCategory } from "../../context/CategoryContext";

export default function Productlist_section() {
  const { products, fetchProducts } = useProduct(); // Fetch products from context
  const { categories, fetchCategories } = useCategory(); // Fetch categories from context

  const [searchTerm, setSearchTerm] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("All");
  const [selectedCategoryId, setSelectedCategoryId] = useState(null); // To store selected category's ID
  const [isDropdownVisible, setIsDropdownVisible] = useState(false);

  // Fetch products & categories on mount
  useEffect(() => {
    fetchProducts();
    fetchCategories();
  }, []);

  // Toggle dropdown visibility
  const toggleDropdown = () => {
    setIsDropdownVisible(!isDropdownVisible);
  };

  // Handle category selection
  const handleCategorySelect = (category) => {
    setSelectedCategory(category.name);
    setSelectedCategoryId(category.id); // Set the category ID
    setIsDropdownVisible(false);
  };

  // Handle search input change
  const handleSearchChange = (e) => {
    setSearchTerm(e.target.value);
  };

  // Filter products based on category_id and search term (ignoring case)
  const filteredProducts = products.filter((product) => {
    const matchesCategory =
      selectedCategory === "All" ||
      product.category_id === selectedCategoryId; // Compare category_id
    const matchesSearch = product.name
      .toLowerCase()
      .includes(searchTerm.toLowerCase());

    return matchesCategory && matchesSearch;
  });

  return (
    <div>
      {/* Breadcrumb */}
      <section className="breadcrumb_part">
        <div className="container">
          <div className="row">
            <div className="col-lg-12">
              <div className="breadcrumb_iner">
                <h2>Product List</h2>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Product List */}
      <section className="product_list section_padding">
        <div className="container">
          <div className="row">
            {/* Sidebar */}
            <div className="col-md-4">
              <div className="product_sidebar">
                {/* Search Input */}
                <div className="single_sedebar">
                  <input
                    type="text"
                    placeholder="Search product..."
                    value={searchTerm}
                    onChange={handleSearchChange}
                  />
                  <i className="ti-search" />
                </div>

                {/* Category Dropdown */}
                <div className="single_sedebar">
                  <div className="select_option">
                    <div
                      className="select_option_list"
                      onClick={toggleDropdown}
                      style={{ cursor: "pointer" }}
                    >
                      {selectedCategory} <i className="right fas fa-caret-down" />
                    </div>
                    {isDropdownVisible && (
                      <div className="select_option_dropdown">
                        <p onClick={() => handleCategorySelect({ name: "All", id: null })}>
                          <a href="#">All</a>
                        </p>
                        {categories
                          .slice() // Create a copy before sorting
                          .sort((a, b) =>
                            a.name.localeCompare(b.name, undefined, {
                              sensitivity: "base",
                            })
                          )
                          .map((category) => (
                            <p
                              key={category.id}
                              onClick={() => handleCategorySelect(category)}
                            >
                              <a href="#">{category.name}</a>
                            </p>
                          ))}
                      </div>
                    )}
                  </div>
                </div>
              </div>
            </div>

            {/* Product List */}
            <div className="col-md-8">
              <div className="product_list">
                <div className="row">
                  {filteredProducts.length > 0 ? (
                    filteredProducts.map((product) => (
                      <div key={product.id} className="col-lg-6 col-sm-6">
                        <div className="single_product_item">
                          <img
                            src={product.image_url}
                            alt={product.name}
                            className="img-fluid"
                          />
                          <h3>
                            <a href="single-product.html">{product.name}</a>
                          </h3>
                          <p>From ${product.price}</p>

                          {/* Shop Now Button */}
                          <div className="shop_now_button">
                            <button className="btn_1">Shop Now</button>
                          </div>
                        </div>
                      </div>
                    ))
                  ) : (
                    <p className="text-center">No products found.</p>
                  )}
                </div>
                {/* Load More Button */}
                <div className="load_more_btn text-center">
                  <a href="#" className="btn_3">
                    Load More
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
