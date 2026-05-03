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
