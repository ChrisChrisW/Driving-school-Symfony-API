<!DOCTYPE html>

<html lang="fr">
<?php require_once '../templates/_head.php'; ?>

<body>
<?php require_once './_sub-header.php'; ?>

<section class="contact">
    <div class="row">
        <div class="contact-col">
            <div>
                <i class="fa fa-address-card"></i>
                <span>
                    <table id="trainer-table">
                        <thead>
                        <tr>
                            <th><h5>Moniteurs</h5></th>
                        </tr>
                        </thead>

                        <tbody></tbody>
                    </table>
                </span>
            </div>
        </div>
        <div class="contact-col">
            <form id="trainers" action="">
                <label for="numSs">Numéro de Sécurité sociale</label><input type="text" name="numSs" id="numSs" placeholder="Numéro Sécurité Social" minlength="13" maxlength="15" required>
                <label for="lastName">Nom</label><input type="text" name="lastName" id="lastName" placeholder="Nom" required>
                <label for="firstName">Prénom</label><input type="text" name="firstName" id="firstName" placeholder="Prènom" required>
                <label for="formulaSlug">Formule (code/permis) associé :<p>pas obligatoire</p></label><select id="formulaSlug" name="formulaSlug"></select>
                <input type="submit" id="submit">
            </form>
        </div>
    </div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>

<script>

    function callApi() {
        //-----------------------------------------------------------------------------------------------------------//
        fetch("http://127.0.0.1:8000/api/formulas")
            .then(response => response.json())
            .then(data => {
                const formula = document.getElementById("formulaSlug");
                console.log(data)
                for (const item of data) {
                    const option = document.createElement("option");
                    option.value = item.slug;
                    option.text = item.wording;
                    formula.appendChild(option);
                }
            })
            .catch(error => console.error('Error:', error));
    }
    document.addEventListener("DOMContentLoaded", callApi, false);

    //----------------------------------------------------------------------------------------------------------------//
    function getData() {
        fetch('http://localhost:8000/api/trainers')
            .then(response => response.json())
            .then(data => {
                const table = document.getElementById("trainer-table");
                let tbody = table.getElementsByTagName("tbody")[0];
                if(tbody.childElementCount > 0) {
                    tbody.remove();
                    tbody = document.createElement("tbody");
                    table.appendChild(tbody);
                }

                data.map((item) => {
                    const row = document.createElement("tr");
                    const trainerCell = document.createElement("td");
                    trainerCell.innerHTML = item.identity.firstName + " " + item.identity.lastName  + '<br><a href="edit.php?numSs=' + item.numSs + '"><i class="fa fa-pencil-square-o"></i>Modifier</a><br><a href="vehicle.php?numSs=' + item.numSs + '"><i class="fa fa-car"></i> Ajouter/Enlever une voiture</a>';
                    row.appendChild(trainerCell);
                    tbody.appendChild(row);
                })
            })
            .catch(error => console.error('Error:', error));
    }
    getData()

    //-------------------------------------------------------------------------------------------------//
        const trainer = document.getElementById('trainers'); // get submit
        trainer.addEventListener("submit", submitForm, false);
        function submitForm(e) {
            e.preventDefault();
            console.log(e.target);
            console.log(e.currentTarget);

            const formData = new FormData(e.currentTarget);

            let formToObject = Object.fromEntries(formData.entries());

            const formToJson = JSON.stringify(formToObject);

            console.log(formToObject)
            console.log(formToJson)

            fetch('http://localhost:8000/api/trainer', {
                method: 'POST',
                mode: 'cors', // no-cors, *cors, same-origin
                headers: {
                    'Content-Type': 'application/json',
                    Accept: "application/json",
                },
                body: formToJson
            })
                .then(response => response.json())
                .then(data => {
                    if(data?.violations === undefined) {
                        getData(); // update screen
                        document.getElementById('trainers').reset() // reset form
                        alert("Données envoyées avec succès!")
                    }
                })
                .catch(error => console.error('Error:', error));
        }
</script>
</html>