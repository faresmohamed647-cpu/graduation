(function () {
  const form = document.getElementById("applicationForm");
  if (!form) return;

  const role = (form.dataset.role || "").toLowerCase();
  const submitBtn = form.querySelector(".application-submit");
  const submitLabel = submitBtn ? submitBtn.textContent : "Submit";
  const apiToken =
    document.querySelector('meta[name="application-api-token"]')?.content || "";

  const clearErrors = () => {
    form.querySelectorAll(".field-error").forEach((node) => {
      node.textContent = "";
    });
  };

  const showToast = (type, message) => {
    let container = document.querySelector(".toast-container");
    if (!container) {
      container = document.createElement("div");
      container.className = "toast-container";
      document.body.appendChild(container);
    }

    const toast = document.createElement("div");
    toast.className = "app-toast " + type;
    toast.textContent = message;
    container.appendChild(toast);

    requestAnimationFrame(() => {
      toast.classList.add("show");
    });

    setTimeout(() => {
      toast.classList.remove("show");
      setTimeout(() => toast.remove(), 220);
    }, 3000);
  };

  const setError = (key, message) => {
    const node = form.querySelector('[data-error-for="' + key + '"]');
    if (node) node.textContent = message;
  };

  const collectData = () => {
    const data = {};
    const formData = new FormData(form);
    formData.forEach((value, key) => {
      data[key] = typeof value === "string" ? value.trim() : value;
    });
    return data;
  };

  form.addEventListener("submit", async function (event) {
    event.preventDefault();
    clearErrors();

    if (!apiToken) {
      showToast(
        "error",
        "Application API token is missing. Contact support."
      );
      return;
    }

    const payload = collectData();
    payload.role = role;

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.classList.add("is-loading");
      submitBtn.innerHTML =
        '<span class="btn-spinner" aria-hidden="true"></span><span>Submitting...</span>';
    }

    try {
      const response = await fetch("/api/applications", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          Authorization: "Bearer " + apiToken,
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json().catch(() => ({}));

      if (response.ok) {
        form.classList.add("is-success");
        setTimeout(() => {
          form.reset();
          form.classList.remove("is-success");
        }, 180);
        showToast(
          "success",
          data.message || "Application submitted successfully."
        );
        return;
      }

      if (response.status === 422 && data.errors) {
        Object.entries(data.errors).forEach(([field, messages]) => {
          const message = Array.isArray(messages) ? messages[0] : messages;
          setError(field, message);
        });
        showToast("error", data.message || "Please fix the highlighted fields.");
        return;
      }

      showToast("error", data.message || "Server error. Please try again.");
    } catch (err) {
      showToast("error", "Network error. Please try again.");
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.classList.remove("is-loading");
        submitBtn.textContent = submitLabel;
      }
    }
  });
})();
