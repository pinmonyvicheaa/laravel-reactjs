import React, { useState } from "react";
import img1 from "../assets/img/arrivel/arrivel_1.png"; // Adjust the path as needed
import img2 from "../assets/img/arrivel/arrivel_2.png";

// Reusable Cart Item Component
const CartItem = ({ image, name, price, onQuantityChange }) => {
  const [quantity, setQuantity] = useState(1); // Initialize quantity to 1

  const handleIncrement = () => {
    const newQuantity = Math.min(quantity + 1, 10); // Limit max quantity to 10
    setQuantity(newQuantity);
    onQuantityChange(price, 1); // Pass price and delta to parent
  };

  const handleDecrement = () => {
    const newQuantity = Math.max(quantity - 1, 0); // Limit min quantity to 0
    setQuantity(newQuantity);
    onQuantityChange(price, -1); // Pass price and delta to parent
  };

  return (
    <tr>
      <td>
        <div className="media">
          <div className="d-flex">
            <img src={image} alt={name} />
          </div>
          <div className="media-body">
            <p>{name}</p>
          </div>
        </div>
      </td>
      <td>
        <h5>${price.toFixed(2)}</h5>
      </td>
      <td>
        <div className="product_count">
          <button onClick={handleDecrement} className="buttonbg" aria-label="Decrease Quantity">
            -
          </button>
          <input
            className="input-number"
            type="text"
            value={quantity}
            readOnly
            aria-label="Current Quantity"
          />
          <button onClick={handleIncrement} className="buttonbg" aria-label="Increase Quantity">
            +
          </button>
        </div>
      </td>
      <td>
        <h5>${(price * quantity).toFixed(2)}</h5>
      </td>
    </tr>
  );
};

export default function Cart() {
  const [totalPrice, setTotalPrice] = useState(0); // Initialize totalPrice to 0
  const [shippingCost, setShippingCost] = useState(0); // Initialize shipping cost

  // Handle quantity changes and update total price dynamically
  const handleQuantityChange = (price, delta) => {
    setTotalPrice((prev) => prev + price * delta);
  };

  // Handle shipping option changes
  const handleShippingChange = (e) => {
    const selectedShippingCost = parseFloat(e.target.value);
    setShippingCost(selectedShippingCost);
  };

  return (
    <div>
      {/* Breadcrumb Section */}
      <section className="breadcrumb_part">
        <div className="container">
          <div className="row">
            <div className="col-lg-12">
              <div className="breadcrumb_iner">
                <h2>Cart List</h2>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Cart Area */}
      <section className="cart_area section_padding">
        <div className="container">
          <div className="cart_inner">
            <div className="table-responsive">
              <table className="table">
                <thead>
                  <tr>
                    <th scope="col">Product</th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <CartItem
                    image={img1}
                    name="Minimalistic Shop for Multipurpose Use"
                    price={360}
                    onQuantityChange={handleQuantityChange}
                  />
                  <CartItem
                    image={img2}
                    name="Minimalistic Shop for Multipurpose Use"
                    price={450} // Different price for testing flexibility
                    onQuantityChange={handleQuantityChange}
                  />
                  <tr className="bottom_button">
                    <td>
                      <button className="btn_1">Update Cart</button>
                    </td>
                    <td />
                    <td />
                    <td>
                      <div className="cupon_text float-right">
                        <button className="btn_1">Close Coupon</button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td />
                    <td />
                    <td>
                      <h5>Subtotal</h5>
                    </td>
                    <td>
                      <h5>${totalPrice.toFixed(2)}</h5>
                    </td>
                  </tr>
                  <tr className="shipping_area">
                    <td />
                    <td />
                    <td>
                      <h5>Shipping</h5>
                    </td>
                    <td>
                      <div className="shipping_box">
                        <ul className="list">
                          <li>
                            <label>
                              Flat Rate: $5.00
                              <input
                                type="radio"
                                name="shipping"
                                value="5"
                                onChange={handleShippingChange}
                              />
                            </label>
                          </li>
                          <li>
                            <label>
                              Free Shipping
                              <input
                                type="radio"
                                name="shipping"
                                value="0"
                                onChange={handleShippingChange}
                              />
                            </label>
                          </li>
                          <li>
                            <label>
                              Flat Rate: $10.00
                              <input
                                type="radio"
                                name="shipping"
                                value="10"
                                onChange={handleShippingChange}
                              />
                            </label>
                          </li>
                          <li>
                            <label>
                              Local Delivery: $2.00
                              <input
                                type="radio"
                                name="shipping"
                                value="2"
                                onChange={handleShippingChange}
                              />
                            </label>
                          </li>
                        </ul>
                        <h6>
                          Calculate Shipping
                          <i className="fa fa-caret-down" aria-hidden="true" />
                        </h6>
                        <select className="shipping_select">
                          <option value="1">Bangladesh</option>
                          <option value="2">India</option>
                          <option value="4">Pakistan</option>
                        </select>
                        <select className="shipping_select section_bg">
                          <option value="1">Select a State</option>
                          <option value="2">Select a State</option>
                          <option value="4">Select a State</option>
                        </select>
                        <input
                          className="post_code"
                          type="text"
                          placeholder="Postcode/Zipcode"
                        />
                        <button className="btn_1">Update Details</button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td />
                    <td />
                    <td>
                      <h5>Total</h5>
                    </td>
                    <td>
                      <h5>${(totalPrice + shippingCost).toFixed(2)}</h5>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div className="checkout_btn_inner float-right">
                <button className="btn_1 buttonbg1">Continue Shopping</button>
                <a href="/checkout" className="btn_1 checkout_btn_1 buttonbg1" >Proceed to Checkout</a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}