/**
 * Seat Selection JavaScript
 * Handles seat selection, real-time fare calculation
 */

const $ = window.jQuery || window.$

$(document).ready(() => {
  // Seat selection toggle with fare calculation
  $(".seat.available").on("change", () => {
    updateSelectedSeats()
    calculateTotalFare()
  })

  // Update selected seats display
  function updateSelectedSeats() {
    const selected = []
    $(".seat.available:checked").each(function () {
      selected.push($(this).data("seat-number"))
    })

    const selectedCount = selected.length
    if (selectedCount === 0) {
      $("#selectedSeatsDisplay").text("None").css("color", "var(--text-secondary)")
    } else {
      $("#selectedSeatsDisplay").text(selected.join(", ")).css("color", "var(--primary-color)")
    }

    // Enable/disable continue button
    $("#btnContinueToBooking").prop("disabled", selectedCount === 0)
  }

  // Calculate total fare based on selected seats
  function calculateTotalFare() {
    const farePerSeat = Number.parseFloat($("#tripFarePerSeat").val())
    const selectedCount = $(".seat.available:checked").length
    const totalFare = farePerSeat * selectedCount

    $("#totalFare").text(totalFare > 0 ? totalFare.toFixed(0) : "0")
  }

  // Prevent body scroll when modal is open
  $("#modalSeatSelection")
    .on("show.bs.modal", () => {
      $("body").css("overflow", "hidden")
    })
    .on("hidden.bs.modal", () => {
      $("body").css("overflow", "auto")
    })

  // Initialize
  updateSelectedSeats()
})

// Proceed to booking page
function proceedToBooking(tripNo) {
  const selectedSeats = []
  $(".seat.available:checked").each(function () {
    selectedSeats.push($(this).val())
  })

  const boardingPoint = $("#boarding_point").val()
  const farePerSeat = Number.parseFloat($("#tripFarePerSeat").val())
  const totalFare = farePerSeat * selectedSeats.length

  if (selectedSeats.length === 0) {
    alert("Please select at least one seat")
    return
  }

  if (!boardingPoint) {
    alert("Please select a boarding point")
    return
  }

  // Store in sessionStorage for buy_seat.php
  sessionStorage.setItem("selected_seats", selectedSeats.join(","))
  sessionStorage.setItem("boarding_point", boardingPoint)
  sessionStorage.setItem("total_fare", totalFare)
  sessionStorage.setItem("trip_no", tripNo)

  // Redirect to booking form
  window.location.href = "buy_seat.php?trip=" + tripNo
}
