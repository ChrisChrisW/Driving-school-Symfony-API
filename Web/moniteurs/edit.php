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
                            <th><h5>Moniteur</h5></th>
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
<!--                <label for="formulaSlug">Formule (code/permis) associé :<p>pas obligatoire</p></label><select id="formulaSlug" name="formulaSlug"></select>-->
                <input type="submit" id="submit">
            </form>
        </div>
    </div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>

<script>

    // function callApi() {
    //     fetch("http://127.0.0.1:8000/api/formulas")
    //         .then(response => response.json())
    //         .then(data => {
    //             const formula = document.getElementById("formulaSlug");
    //             console.log(data)
    //             for (const item of data) {
    //                 const option = document.createElement("option");
    //                 option.value = item.slug;
    //                 option.text = item.wording;
    //                 formula.appendChild(option);
    //             }
    //         })
    //         .catch(error => console.error('Error:', error));
    // }
    // document.addEventListener("DOMContentLoaded", callApi, false);

    //----------------------------------------------------------------------------------------------------------------//
    function getData() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const numSs = urlParams.get('numSs');

        fetch('http://localhost:8000/api/trainers?numSs=' + numSs)
            .then(response => response.json())
            .then(data => {
                data = data[0]
                console.log(data)

                document.getElementById("numSs").value = data.numSs
                document.getElementById("firstName").value = data.identity.firstName
                document.getElementById("lastName").value = data.identity.lastName
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


        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const numSs = urlParams.get('numSs');

        formToObject.currentNumSs = numSs

        const formToJson = JSON.stringify(formToObject);

        console.log(formToObject)
        console.log(formToJson)

        fetch('http://localhost:8000/api/trainer/' + numSs, {
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
                    document.getElementById('trainers').reset() // reset form
                    alert("Données envoyées avec succès!")
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
</html>