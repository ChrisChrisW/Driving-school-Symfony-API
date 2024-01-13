<!DOCTYPE html>

<html lang="fr">
<?php require_once '../templates/_head.php'; ?>

<body>
<?php require_once './_sub-header.php'; ?>

<section class="contact">
    <div class="row">
        <div class="contact-col">
            <div>
                <i class="fa fa-user-circle-o"></i>
                <span>
                        <table id="candidates-table">
                            <thead>
                                <tr>
                                    <th>
                                        <h5>Candidats</h5>
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
            <form id="candidat" action="">
                <label for="formuleConduite">Formule souhaité</label><select id="formuleConduite" name="formuleConduite"></select>
                <input type="submit" id="submit">
            </form>
        </div>
    </div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>

<script>
    function getDateFormatter(date = "") {
        let newDate = new Date(date);
        if(date === "") {
            newDate = new Date();
        }

        const yyyy = newDate.getFullYear();
        let mm = newDate.getMonth() + 1; // Months start at 0!
        let dd = newDate.getDate();

        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;

        return yyyy + '-' + mm + '-' + dd;
    }

    //----------------------------------------------------------------------------------------------------------------//
    async function getData(){
        let formulasCode = []

        const formulas = await fetch("http://localhost:8000/api/formulas")
            .then(response => response.json())
            .then(data => {
                data.map(item => {
                    if(!item.drivingFormula) formulasCode.push(item.slug)
                })
                return data
            })
            .catch(error => console.error('Error:', error))


        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const email = urlParams.get('email');

        const candidates = document.getElementById("formuleConduite");
        const options = candidates.querySelectorAll("option")
        if(options.length > 0) {
            options.forEach(option => option.remove())
        }

        const table = document.getElementById("candidates-table");
        let tbody = table.getElementsByTagName("tbody")[0];
        if(tbody.childElementCount > 0) {
            tbody.remove();
            tbody = document.createElement("tbody");
            table.appendChild(tbody);
        }


        await fetch('http://localhost:8000/api/candidates?email=' + email)
            .then(response => response.json())
            .then(data => {
                data = data[0]
                console.log(data)

                let myFormulas = []

                let row = document.createElement("tr");
                let candidateCell = document.createElement("td");
                candidateCell.innerHTML = "Formule de code :"
                row.appendChild(candidateCell);
                tbody.appendChild(row);

                if(data.formulaCodeDates.length > 0) {
                    data.formulaCodeDates.map((item) => {
                        myFormulas.push(item.formula.slug)
                        const row = document.createElement("tr");
                        candidateCell = document.createElement("td");
                        candidateCell.innerHTML = "<p>" + item.formula.wording + " | " + getDateFormatter(item.startDate) + " - " + (item.endDate ? getDateFormatter(item.endDate) : "") + "</p>"
                        row.appendChild(candidateCell);
                        tbody.appendChild(row);
                    })
                } else {
                    row = document.createElement("tr");
                    candidateCell = document.createElement("td");
                    candidateCell.innerHTML = "<p>Pas encore de formule code</p>"
                    row.appendChild(candidateCell);
                    tbody.appendChild(row);
                }

                row = document.createElement("tr");
                candidateCell = document.createElement("td");
                candidateCell.innerHTML = "Formule de conduite :"
                row.appendChild(candidateCell);
                tbody.appendChild(row);

                if(data.drivingFormulas.length > 0) {
                    data.drivingFormulas.map((item) => {
                        myFormulas.push(item.formula.slug)
                        const row = document.createElement("tr");
                        const candidateCell = document.createElement("td");
                        candidateCell.innerHTML = "<p>" + item.formula.wording + "</p>"
                        row.appendChild(candidateCell);
                        tbody.appendChild(row);
                    })
                } else {
                    row = document.createElement("tr");
                    candidateCell = document.createElement("td");
                    candidateCell.innerHTML = "<p>Pas encore de formule de conduite</p>"
                    row.appendChild(candidateCell);
                    tbody.appendChild(row);
                }


                // Select button
                formulas.map(item => {
                    if(!myFormulas.includes(item.slug)) {
                        const option = document.createElement("option");
                        option.value = item.slug;
                        option.text = item.wording;
                        candidates.appendChild(option);
                    }
                })

            })
            .catch(error => console.error('Error:', error));

        const formuleSelect = document.getElementById("formuleConduite")
        function addInput() {
            const formuleSelect = document.getElementById("formuleConduite")

            const target = formuleSelect.options[formuleSelect.options.selectedIndex].value

            const startDate = document.getElementById("startDate");

            if(formulasCode.includes(target) && !startDate) {
                formuleSelect.insertAdjacentHTML("afterend", ' <label for="startDate">Date Début</label><input type="date" name="startDate" id="startDate" placeholder="Début Date" min="'+ getDateFormatter() + '" required>')
            } else if(!formulasCode.includes(target) && startDate) {
                document.querySelector("label[for=startDate]").remove() // remove label
                startDate.remove() // remove date-time
            }
        }
        addInput()
        formuleSelect.addEventListener("change", addInput, false)
    }
    getData()
    //-------------------------------------------------------------------------------------------------//
    function submitForm(e) {
        e.preventDefault();
        console.log(e.target);
        console.log(e.currentTarget);

        const formData = new FormData(e.currentTarget);

        let formToObject = Object.fromEntries(formData.entries());

        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const email = urlParams.get('email');

        formToObject.currentEmail = email
        if(formToObject.formuleConduite === "formule-code") {
            formToObject.formuleCode = true
            delete formToObject.formuleConduite
        }
        if(formToObject.formuleConduite === "formule-code-illimite") {
            formToObject.formuleCodeIllimite = true
            delete formToObject.formuleConduite
        }
        const formToJson = JSON.stringify(formToObject);

        console.log(formToObject)
        console.log(formToJson)

        fetch('http://localhost:8000/api/candidate/formula/' + email, {
            method: 'PATCH',
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
                    document.getElementById('candidat').reset() // reset form
                    alert("Données envoyées avec succès!")
                }
            })
            .catch(error => console.error('Error:', error));
    }
    const candidat = document.getElementById('candidat'); // get submit
    candidat.addEventListener("submit", submitForm, false);
</script>
</html>
