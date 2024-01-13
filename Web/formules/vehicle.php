<!DOCTYPE html>

<html lang="fr">
<?php require_once '../templates/_head.php'; ?>

<body>
<?php require_once './_sub-header.php'; ?>

<section class="contact">
    <div class="row">
        <div class="contact-col">
            <div>
                <i class="fa fa-file"></i>
                <span>
                        <table id="formule-table">
                            <thead>
                                <tr>
                                    <th>
                                        <h5>Véhicules dans la formule</h5>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </span>
            </div>
        </div>

        <div class="contact-col">
            <form id="formule" action="">

                <label for="vehicleNumPlate">Voiture à associer</label><select id="vehicleNumPlate" name="vehicleNumPlate"></select>
                <input type="submit" id="submit">
            </form>
        </div>
    </div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>

<script>
    async function getData() {
        const apiUrls = "http://127.0.0.1:8000/api/vehicles";
        let allVehiclesNumPlate = []


        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const slug = urlParams.get('slug');

        await fetch('http://localhost:8000/api/formulas?slug=' + slug)
            .then(response => response.json())
            .then(data => {
                data = data[0]
                console.log(data)

                if(data.drivingFormula.vehicles) data.drivingFormula.vehicles.map(item => allVehiclesNumPlate.push(item.numPlate))
            })
            .catch(error => console.error('Error:', error));

        console.log(allVehiclesNumPlate)

        const vehicleNumPlate = document.getElementById("vehicleNumPlate");
        const options = vehicleNumPlate.querySelectorAll("option")
        if(options.length > 0) {
            options.forEach(option => option.remove())
        }

        const table = document.getElementById("formule-table");
        let tbody = table.getElementsByTagName("tbody")[0];
        if(table.childElementCount > 0) {
            tbody.remove();
            tbody = document.createElement("tbody");
            table.appendChild(tbody);
        }

        await fetch(apiUrls)
            .then(response => response.json())
            .then(data => {
                console.log(data)

                data.map(item => {
                    if(!allVehiclesNumPlate.includes(item.numPlate)) {
                        const option = document.createElement("option");
                        option.value = item.numPlate;
                        option.text = item.numPlate;
                        vehicleNumPlate.appendChild(option);
                    } else {
                        const row = document.createElement("tr");
                        const wordingCell = document.createElement("td");
                        wordingCell.innerHTML = item.numPlate + '<i class="fa fa-window-close" style="cursor: pointer" id="delete-item" data-item='+ item.numPlate + '></i>';
                        row.appendChild(wordingCell);
                        tbody.appendChild(row);
                    }
                })
            })
            .catch(error => console.error('Error:', error));


        const items = await document.querySelectorAll("#delete-item");
        console.log(items)
        if(items) {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const slug = urlParams.get('slug');

            items.forEach(item => {
                item.addEventListener("click", () => {
                    if (confirm("êtes vous sûr de supprimer le vehicule de la formation ?")) {
                        fetch('http://localhost:8000/api/formula/vehicle/remove/' + slug, {
                            method: 'PUT',
                            mode: 'cors', // no-cors, *cors, same-origin
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: "application/json",
                            },
                            body: JSON.stringify({
                                "currentSlug" : slug,
                                "removeVehicle" : item.getAttribute("data-item")
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                console.log(data)
                                if(data?.violations === undefined) {
                                    getData(); // update screen
                                    alert("Données envoyées avec succès!")
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });
            })
        }
    }
    getData()

    const formula = document.getElementById('formule'); // get submit
    formula.addEventListener("submit", submitForm, false);
    function submitForm(e) {
        e.preventDefault();
        console.log(e.target);
        console.log(e.currentTarget);

        const formData = new FormData(e.currentTarget);
        const apiUrl = "http://localhost:8000/api";

        let formToObject = Object.fromEntries(formData.entries());

        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const slug = urlParams.get('slug');

        formToObject.currentSlug = slug

        const formToJson = JSON.stringify(formToObject);

        console.log(formToObject)
        console.log(formToJson)


        fetch(apiUrl + '/formula/vehicle/add/' + slug, {
            method: 'PUT',
            mode: 'cors', // no-cors, *cors, same-origin
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
                    document.getElementById('formule').reset() // reset form
                    alert("Données envoyées avec succès!")
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
</html>
