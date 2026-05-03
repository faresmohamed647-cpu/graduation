const container = document.getElementById("container");
const registerBtn = document.getElementById("register");
const loginBtn = document.getElementById("login");

registerBtn.addEventListener("click", () => {
    container.classList.add("active");
});

loginBtn.addEventListener("click", () => {
    container.classList.remove("active");
});

// Handle hash on page load
window.addEventListener("load", () => {
    const hash = window.location.hash;
    if (hash === "#carowner") {
        container.classList.add("active");
    } else if (hash === "#parents") {
        container.classList.remove("active");
    }
});

// ── Password strength checker ──
const passwordBlocks = document.querySelectorAll(".password-block");

const rules = {
    length8: (value) => value.length >= 8,
    length12: (value) => value.length >= 12,
    uppercase: (value) => /[A-Z]/.test(value),
    lowercase: (value) => /[a-z]/.test(value),
    number: (value) => /[0-9]/.test(value),
    special: (value) => /[^A-Za-z0-9\s]/.test(value),
    "no-spaces": (value) => !/\s/.test(value),
    match: (value, confirmValue) => value.length > 0 && value === confirmValue,
};

passwordBlocks.forEach((block) => {
    const passwordInput = block.querySelector("[data-password]");
    const confirmInput = block.querySelector("[data-confirm]");
    const checklist = block.querySelector(".password-checklist");
    const listItems = checklist.querySelectorAll("li[data-rule]");
    const bar = checklist.querySelector(".strength-bar");
    const text = checklist.querySelector(".strength-text");
    const emailInput = block.closest("form")?.querySelector("[data-email]");

    const updateChecklist = () => {
        const passwordValue = passwordInput.value || "";
        const confirmValue = confirmInput.value || "";
        let metCount = 0;

        listItems.forEach((item) => {
            const rule = item.getAttribute("data-rule");
            const isMet = rules[rule](passwordValue, confirmValue, emailInput?.value || "");
            item.classList.toggle("met", isMet);
            if (isMet) {
                metCount += 1;
            }
        });

        const percent = Math.round((metCount / listItems.length) * 100);
        bar.style.width = `${percent}%`;
        text.textContent = `${percent}%`;
    };

    passwordInput.addEventListener("input", updateChecklist);
    confirmInput.addEventListener("input", updateChecklist);
    passwordInput.addEventListener("focus", updateChecklist);
    confirmInput.addEventListener("focus", updateChecklist);
});
