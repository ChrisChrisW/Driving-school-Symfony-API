<!DOCTYPE html>

<html lang="fr">
<?php require_once '../templates/_head.php'; ?>

<body>
<?php require_once './_sub-header.php'; ?>

<section class="contact">
    <div class="row">
        <div class="contact-col">
            <div>
                <i class="fa fa-car"></i>

                <span>
                    <table id="vehicle-table">
                        <thead>
                            <tr>
                                <th><h5>Véhicules Plaques</h5></th>
                            </tr>
                        </thead>

                        <tbody></tbody>
                    </table>
                </span>
            </div>
        </div>
        <div class="contact-col">
            <form id="vehicule">
                <label for="numPlate">Numéro d'immatriculation</label>
                <input type="text" name="numPlate" id="numPlate" placeholder="Plaque" min="7" max="9" required>

                <label for="purchaseDate">Date</label>
                <input type="date" name="purchaseDate" id="purchaseDate" placeholder="Date" max="" required>

                <label for="power">Puissance</label>
                <input type="number" name="power" id="power" placeholder="Puissance" min="1" required>

                <input type="submit" id="submit">
            </form>
        </div>
    </div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>

<script>
    function getDate(date = "") {
        const today = new Date(date);
        const yyyy = today.getFullYear();
        let mm = today.getMonth() + 1; // Months start at 0!
        let dd = today.getDate();

        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;

        return yyyy + '-' + mm + '-' + dd;
    }
    document.getElementById('purchaseDate').max = getDate();

    async function getData() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const numPlate = urlParams.get('numPlate');

        await fetch('http://localhost:8000/api/vehicle?numPlate=' + numPlate)
            .then(response => response.json())
            .then(data => {
                data = data[0]
                document.getElementById("numPlate").value = data.numPlate;
                console.log(document.getElementById("numPlate").value)
                document.getElementById("purchaseDate").value = getDate(data.purchaseDate);
                document.getElementById("power").value = data.power;

            })
            .catch(error => console.error('Error:', error));
    }
    getData()

    async function submitForm(e) {
        e.preventDefault();

        const formData = new FormData(e.currentTarget);
        const apiUrl = "http://localhost:8000/api";

        let formToObject = Object.fromEntries(formData.entries());
        formToObject.power = parseInt(formToObject.power)


        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const numPlate = urlParams.get('numPlate');


        formToObject.cuurentNumPlate = numPlate

        const formToJson = JSON.stringify(formToObject);
        console.log(formToJson)

        await fetch(apiUrl + '/vehicle/' + numPlate, {
            method: 'PUT',
            mode: 'cors',
            headers: {
                'Content-Type': 'application/json',
                Accept: "application/json",
            },
            body: formToJson
        })
            .then(response => response.json())
            .then(data => {
                console.log(data)
                if(data?.violations === undefined) {
                    getData(); // update screen
                    document.getElementById('vehicule').reset() // reset form
                    alert("Données envoyées avec succès!")
                }
            })
            .catch(error => console.error('Error:', error));
    }

    const vehicle = document.getElementById('vehicule'); // get submit
    vehicle.addEventListener("submit", submitForm, false);
</script>
</html>
