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

    //----------------------------------------------------------------------------------------------------------------//
    async function getData() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const numSs = urlParams.get('numSs');

        const table = document.getElementById("trainer-table");
        let tbody = table.getElementsByTagName("tbody")[0];
        if(table.childElementCount > 0) {
            tbody.remove();
            tbody = document.createElement("tbody");
            table.appendChild(tbody);
        }

        let formulas = []

        await fetch('http://localhost:8000/api/trainers?numSs=' + numSs)
            .then(response => response.json())
            .then(data => {
                data = data[0]
                console.log(data)

                if(data.formulas) {
                    data.formulas.map(formula => {
                        const row = document.createElement("tr");
                        const wordingCell = document.createElement("td");
                        wordingCell.innerHTML = formula.wording + '<i class="fa fa-window-close" style="cursor: pointer" id="delete-item" data-item='+ formula.slug + '></i>';
                        row.appendChild(wordingCell);
                        tbody.appendChild(row);

                        formulas.push(formula.slug)
                    })
                }

            })
            .catch(error => console.error('Error:', error));

        console.log(formulas)

        await fetch("http://127.0.0.1:8000/api/formulas")
            .then(response => response.json())
            .then(data => {
                console.log(data)

                data.map(item => {
                    if(!formulas.includes(item.slug)) {
                        const formulaSlug = document.getElementById("formulaSlug");
                        const option = document.createElement("option");
                        option.value = item.slug;
                        option.text = item.wording;
                        formulaSlug.appendChild(option);
                    }
                })

            })
            .catch(error => console.error('Error:', error));

        const items = await document.querySelectorAll("#delete-item");
        console.log(items)
        if(items) {
            items.forEach(item => {
                item.addEventListener("click", () => {
                    if (confirm("êtes vous sûr de supprimer la formation du moniteur ?")) {
                        fetch('http://localhost:8000/api/trainer/formula/remove/' + numSs, {
                            method: 'PUT',
                            mode: 'cors', // no-cors, *cors, same-origin
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: "application/json",
                            },
                            body: JSON.stringify({
                                "currentNumSs" : numSs,
                                "removeFormula" : item.getAttribute("data-item")
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

        fetch('http://localhost:8000/api/trainer/formula/add/' + numSs, {
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