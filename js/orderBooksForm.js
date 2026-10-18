window.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("orderForm");
  if (!form) return;

  const bookCheckboxes = [...form.querySelectorAll("input[name='book[]']")];
  const deliveryOptions = [...form.querySelectorAll("input[name='deliveryType']")];
  const totalInput = form.querySelector("input[name='total']");
  const customerType = form.querySelector("select[name='customerType']");
  const retailDetails = document.getElementById("retCustDetails");
  const tradeDetails = document.getElementById("tradeCustDetails");
  const termsCheckbox = form.querySelector("input[name='termsChkbx']");
  const termsText = document.getElementById("termsText");
  const submitButton = form.querySelector("button[name='submit']");
  const message = document.getElementById("orderMessage");

  function updateTotal() {
    const booksTotal = bookCheckboxes
      .filter((checkbox) => checkbox.checked)
      .reduce((sum, checkbox) => sum + Number(checkbox.dataset.price || 0), 0);
    const delivery = deliveryOptions.find((option) => option.checked);
    const deliveryPrice = booksTotal > 0 ? Number(delivery?.dataset.price || 0) : 0;
    totalInput.value = (booksTotal + deliveryPrice).toFixed(2);
  }

  function updateCustomerFields() {
    retailDetails.hidden = customerType.value !== "ret";
    tradeDetails.hidden = customerType.value !== "trd";
  }

  function updateTerms() {
    submitButton.disabled = !termsCheckbox.checked;
    termsText.classList.toggle("text-danger", !termsCheckbox.checked);
    termsText.classList.toggle("font-weight-bold", !termsCheckbox.checked);
  }

  [...bookCheckboxes, ...deliveryOptions].forEach((input) => input.addEventListener("change", updateTotal));
  customerType.addEventListener("change", updateCustomerFields);
  termsCheckbox.addEventListener("change", updateTerms);

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    message.className = "mt-3 alert alert-danger";

    if (!bookCheckboxes.some((checkbox) => checkbox.checked)) {
      message.textContent = "Select at least one book.";
      return;
    }
    if (!customerType.value) {
      message.textContent = "Select a customer type.";
      return;
    }
    if (customerType.value === "ret" && (!form.forename.value.trim() || !form.surname.value.trim())) {
      message.textContent = "Enter your forename and surname.";
      return;
    }
    if (customerType.value === "trd" && !form.companyName.value.trim()) {
      message.textContent = "Enter your company name.";
      return;
    }
    if (!termsCheckbox.checked) {
      message.textContent = "Accept the terms and conditions.";
      return;
    }

    message.className = "mt-3 alert alert-success";
    message.textContent = "The order form is complete. This demo does not charge or submit an order.";
  });

  updateTotal();
  updateCustomerFields();
  updateTerms();
});
