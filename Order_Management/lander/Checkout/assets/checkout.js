$(document).ready(function () {

    const basePath = (function () {
        const p = window.location.pathname;
        return p.substring(0, p.lastIndexOf('/') + 1);
    })();

    // ✅ Handle Place Order button click
    $('#place-order-btn').click(function () {
        const formData = $('#checkout-form').serialize();

        $.post(basePath + 'checkout_ajax.php', formData, function (res) {
            if (res && res.success) {

                const successAlert = `
                    <div class="alert alert-success text-center mb-3" role="alert">
                        ✅ Order placed successfully! Tracking details below.
                    </div>
                `;

                // ✅ Hide only the customer form and show order tracking
                $('#checkout-left').fadeOut(400, function () {
                    $(this).html(successAlert + '<div id="order-view-inner"></div>').fadeIn(400, function () {
                        $('#order-view-inner').load(basePath + 'order_view_status.php?id=' + res.order_id, function () {
                            const remaining = res.remaining ? parseInt(res.remaining, 10) : 3600;
                            startTimer(remaining);
                        });
                    });
                });

            } else {
                alert(res && res.message ? res.message : 'Failed to place order.');
            }
        }, 'json').fail(function (xhr, status, err) {
            alert('Server error while placing order: ' + err);
        });
    });

    // ✅ Timer Function
    function startTimer(duration) {
        duration = parseInt(duration, 10) || 3600;
        let timer = duration, minutes, seconds;
        const display = document.getElementById('timer-display');
        if (!display) return;

        updateDisplay(timer, display);

        const interval = setInterval(function () {
            timer -= 1;
            if (timer < 0) {
                clearInterval(interval);
                display.textContent = "Done";
                return;
            }
            updateDisplay(timer, display);
        }, 1000);

        function updateDisplay(sec, el) {
            minutes = parseInt(sec / 60, 10);
            seconds = parseInt(sec % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            el.textContent = minutes + ":" + seconds;
        }
    }

    // ✅ On page reload (if already tracking)
    if ($('#order-view-inner').children().length > 0) {
        $.getJSON(basePath + 'order_status_api.php')
            .done(function (data) {
                const remaining = data && data.remaining ? data.remaining : 3600;
                startTimer(remaining);
            })
            .fail(function () {
                startTimer(3600);
            });
    }
});
