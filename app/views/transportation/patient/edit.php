<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Transportation Request</title>
    <link rel="stylesheet" href="/Medceylon/public/assets/css/header.css?v=3">
    <link rel="stylesheet" href="/Medceylon/public/assets/css/patient-transport-form.css?v=2">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #f0fdf5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }

        .form-container {
            max-width: 600px;
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(41, 157, 151, 0.1);
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #299D97;
            margin-bottom: 30px;
        }

        .request-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 500;
            margin-bottom: 6px;
            color: #333;
        }

        input, select {
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        input:focus, select:focus {
            border-color: #299D97;
            outline: none;
            box-shadow: 0 0 5px rgba(41, 157, 151, 0.3);
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .submit-btn {
            background-color: #299D97;
            color: white;
            padding: 12px;
            font-size: 15px;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #247c78;
        }

        footer, .footer {
            background-color: #299D97;
            color: white;
            padding: 30px 20px;
            text-align: center;
            font-size: 14px;
            border-top: 4px solid #1d6e6e;
        }

        .suggestions-box {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            background-color: white;
            z-index: 1000;
            width: 100%;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            border-radius: 6px;
        }

        .suggestions-box div {
            padding: 10px 12px;
            cursor: pointer;
        }

        .suggestions-box div:hover {
            background-color: #f0fdf5;
        }

        .autocomplete-group {
            position: relative;
        }

        @media screen and (max-width: 600px) {
            .form-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="page-wrapper">

    <div class="content-wrapper">
        <div class="form-container">
            <h2>Edit Transportation Request</h2>

            <form method="POST" action="/Medceylon/patient/transport/update/<?= $request['transport_request_id'] ?>" class="request-form">
                <div class="form-group autocomplete-group">
                    <label for="pickup_location">Pickup Location</label>
                    <input type="text" id="pickup_location" name="pickup_location" value="<?= htmlspecialchars($request['pickup_location']) ?>" required autocomplete="off" />
                    <div id="pickup_suggestions" class="suggestions-box"></div>
                </div>

                <div class="form-group autocomplete-group">
                    <label for="dropoff_location">Drop-off Location</label>
                    <input type="text" id="dropoff_location" name="dropoff_location" value="<?= htmlspecialchars($request['dropoff_location']) ?>" required autocomplete="off" />
                    <div id="dropoff_suggestions" class="suggestions-box"></div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" value="<?= $request['date'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="time">Time</label>
                        <input type="time" name="time" id="time" value="<?= $request['time'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="transport_type">Transport Type</label>
                    <select name="transport_type" id="transport_type" required>
                        <option <?= $request['transport_type'] == 'Ambulance' ? 'selected' : '' ?>>Ambulance</option>
                        <option <?= $request['transport_type'] == 'Car' ? 'selected' : '' ?>>Car</option>
                        <option <?= $request['transport_type'] == 'Premium Car' ? 'selected' : '' ?>>Premium Car</option>
                        <option <?= $request['transport_type'] == 'Van' ? 'selected' : '' ?>>Van</option>
                    </select>
                </div>

                <p><strong>Estimated Cost:</strong> <span id="fare">---</span></p>
                <input type="hidden" id="calculated_cost" name="cost" value="0" />

                <button type="submit" class="submit-btn">Update Request</button>
            </form>
        </div>
    </div>

    <?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>

</div>

<script>
function setupAutocomplete(inputId, boxId) {
    const input = document.getElementById(inputId);
    const box = document.getElementById(boxId);

    input.addEventListener("input", async () => {
        const query = input.value.trim();
        if (query.length < 3) {
            box.innerHTML = "";
            return;
        }

        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`;
        const res = await fetch(url);
        const data = await res.json();

        box.innerHTML = "";
        data.slice(0, 5).forEach(place => {
            const div = document.createElement("div");
            div.textContent = place.display_name;
            div.onclick = () => {
                input.value = place.display_name;
                box.innerHTML = "";
                estimateCost();
            };
            box.appendChild(div);
        });
    });

    document.addEventListener("click", function(e) {
        if (!box.contains(e.target) && e.target !== input) {
            box.innerHTML = "";
        }
    });
}

setupAutocomplete("pickup_location", "pickup_suggestions");
setupAutocomplete("dropoff_location", "dropoff_suggestions");

function estimateCost() {
    const pickup = document.getElementById('pickup_location').value;
    const dropoff = document.getElementById('dropoff_location').value;
    const type = document.querySelector('[name="transport_type"]').value;

    if (!pickup || !dropoff || !type) return;

    fetch(`/Medceylon/public/api/calculate-fare.php?pickup=${encodeURIComponent(pickup)}&dropoff=${encodeURIComponent(dropoff)}&type=${type}`)
        .then(res => res.json())
        .then(data => {
            const fare = parseFloat(data.fare);
            document.getElementById('fare').textContent = isNaN(fare) ? "Error" : `Rs. ${fare.toFixed(2)}`;
            document.getElementById('calculated_cost').value = isNaN(fare) ? "0" : fare.toFixed(2);
        });
}

document.getElementById('pickup_location').addEventListener('blur', estimateCost);
document.getElementById('dropoff_location').addEventListener('blur', estimateCost);
document.querySelector('[name="transport_type"]').addEventListener('change', estimateCost);
window.onload = estimateCost;
</script>

</body>
</html>
