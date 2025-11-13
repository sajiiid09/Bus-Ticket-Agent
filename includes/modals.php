<?php
/**
 * Modal Dialogs - Routes map, payment verify, contact, bus info, seat selection
 */
?>

<!-- Routes Map Modal -->
<div class="modal fade" id="modalRouteMap" tabindex="-1" role="dialog" aria-labelledby="modalRouteMapLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRouteMapLabel">Routes Map</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img src="assets/img/map.png" class="img-fluid" alt="Routes Map">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Verification Modal -->
<div class="modal fade" id="modalVerifyPayment" tabindex="-1" role="dialog" aria-labelledby="modalVerifyPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerifyPaymentLabel">Verify Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="payment_confirm.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="refNumber">Reference Number</label>
                        <input type="text" class="form-control" id="refNumber" name="ref_number" placeholder="Enter reference number" required>
                    </div>
                    <div class="form-group">
                        <label for="transactionId">Transaction ID</label>
                        <input type="text" class="form-control" id="transactionId" name="transaction_id" placeholder="Enter transaction ID" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Verify</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="modalContact" tabindex="-1" role="dialog" aria-labelledby="modalContactLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalContactLabel">Contact Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="contact-info">
                    <p><strong>Hotline:</strong> 16334</p>
                    <p><strong>Phone:</strong> 025-784359</p>
                    <p><strong>Mobile:</strong> 0171-2656666, 0191-1432546</p>
                    <p><strong>Email:</strong> support@busticket.bd</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bus Info Modal -->
<div class="modal fade" id="modalBusInfo" tabindex="-1" role="dialog" aria-labelledby="modalBusInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBusInfoLabel">Bus Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="busInfoContent">
                <!-- Loaded via AJAX from bus_info.php -->
            </div>
        </div>
    </div>
</div>

<!-- Seat Selection Modal -->
<div class="modal fade" id="modalSeatSelection" tabindex="-1" role="dialog" aria-labelledby="modalSeatSelectionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSeatSelectionLabel">Select Seats</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="seatSelectionContent">
                <!-- Loaded via AJAX from seat_info.php -->
            </div>
        </div>
    </div>
</div>
