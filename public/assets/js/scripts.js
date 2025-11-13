/**
 * Main JavaScript - Global functionality and utilities
 */

const $ = window.jQuery || window.$

$(document).ready(() => {
  // Navbar shrink effect on scroll
  $(window).scroll(() => {
    if ($(document).scrollTop() > 50) {
      $("#main-navbar").addClass("shrink")
    } else {
      $("#main-navbar").removeClass("shrink")
    }
  })

  // Smooth scrolling for anchor links
  $('a[href*="#"]').on("click", function (e) {
    const target = $(this.getAttribute("href"))
    if (target.length) {
      e.preventDefault()
      $("html, body")
        .stop()
        .animate(
          {
            scrollTop: target.offset().top - 80,
          },
          800,
        )
    }
  })

  // Bus info modal loading
  document.addEventListener("click", (e) => {
    if (e.target.classList.contains("btn-bus-info")) {
      const busId = e.target.dataset.busId
      loadBusInfo(busId)
    }
  })

  // Format phone input
  $('input[type="tel"]').on("input", function () {
    const value = $(this).val().replace(/\D/g, "")
    if (value.length > 0) {
      if (value.length <= 4) {
        $(this).val(value)
      } else if (value.length <= 8) {
        $(this).val(value.slice(0, 4) + "-" + value.slice(4))
      } else {
        $(this).val(value.slice(0, 4) + "-" + value.slice(4, 8) + "-" + value.slice(8, 11))
      }
    }
  })

  // Modal scroll prevention
  $(".modal")
    .on("show.bs.modal", () => {
      $("body").addClass("modal-open-custom")
    })
    .on("hidden.bs.modal", () => {
      $("body").removeClass("modal-open-custom")
    })
})

// Load bus info via AJAX
function loadBusInfo(busId) {
  $.ajax({
    url: "bus_info.php",
    method: "POST",
    data: { bus_id: busId },
    success: (data) => {
      $("#busInfoContent").html(data)
      $("#modalBusInfo").modal("show")
    },
    error: () => {
      alert("Error loading bus information")
    },
  })
}

// Format currency display
function formatCurrency(value) {
  return "৳" + Number.parseInt(value).toLocaleString("en-BD")
}

// Get query parameter from URL
function getQueryParam(name) {
  const urlParams = new URLSearchParams(window.location.search)
  return urlParams.get(name)
}

// Show toast notification
function showToast(message, type = "info") {
  const toastHtml = `
        <div class="toast-notification toast-${type}">
            <span class="toast-message">${message}</span>
            <button class="toast-close" onclick="this.parentElement.remove();">&times;</button>
        </div>
    `
  $("body").append(toastHtml)

  setTimeout(() => {
    $(".toast-notification").fadeOut(300, function () {
      $(this).remove()
    })
  }, 3000)
}

// Add to CSS for toasts
const toastStyles = `
    .toast-notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 15px;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast-info {
        background-color: #2196F3;
    }

    .toast-success {
        background-color: #4CAF50;
    }

    .toast-warning {
        background-color: #FF9800;
    }

    .toast-error {
        background-color: #f44336;
    }

    .toast-close {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toast-close:hover {
        opacity: 0.8;
    }

    .modal-open-custom {
        overflow: hidden;
    }
`

// Inject toast styles
$("<style>").text(toastStyles).appendTo("head")

// Prevent form submission on enter in specific fields
$('input[type="tel"], input[type="text"]:not(form)').on("keypress", (e) => {
  if (e.which === 13) {
    e.preventDefault()
  }
})
