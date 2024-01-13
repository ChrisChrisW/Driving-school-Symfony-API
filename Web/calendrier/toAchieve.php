<!DOCTYPE html>

<html lang="fr">
<?php require_once '../templates/_head.php'; ?>

<body>
<?php require_once './_sub-header.php'; ?>

<section class="planning">
    <div class="">
        <label for="trainerNumSs">Moniteur : </label><select id="trainerNumSs" name="trainerNumSs"></select>
    </div>

    <div id="calendar">
        <div id="calendar-header">
            <button id="prev-month"><<</button>
            <h1 id="month-year"></h1>
            <button id="next-month">>></button>
        </div>
        <table id="calendar-body">
            <thead>
            <tr>
                <th>Dimanche</th>
                <th>Lundi</th>
                <th>Mardi</th>
                <th>Mercredi</th>
                <th>Jeudi</th>
                <th>Vendredi</th>
                <th>Samedi</th>
            </tr>
            </thead>
            <tbody id="calendar-dates"></tbody>
        </table>
    </div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>

<script>
    // Obtenir la date actuelle
    const date = new Date();

    // Obtenir le mois et l'année actuels
    let month = date.getMonth();
    let year = date.getFullYear();

    // Mettre à jour le mois et l'année affichés sur le calendrier
    const monthYear = document.getElementById("month-year");

    // Obtenir les boutons pour changer de mois
    const prevMonth = document.getElementById("prev-month");
    const nextMonth = document.getElementById("next-month");

    // Obtenir la table qui contiendra les dates du calendrier
    const calendarDates = document.getElementById("calendar-dates");

    async function getData() {
        let url = 'http://localhost:8000/api/courseDates?isConfirm=1&isAchieve=0'
        const trainerNumSs =  document.getElementById("trainerNumSs")
        if(trainerNumSs.value) {
            url = url + "&trainer.numSs=" + trainerNumSs.value
        }

        await fetch(url)
            .then(response => response.json())
            .then(data => {
                data.map((item) => {
                    console.log(item)
                    const date = new Date(item.startDate);
                    const minutesDate = date.getMinutes()
                    const hourDate = date.getHours()
                    const dayDate = date.getDate()
                    const monthDate = date.getUTCMonth() + 1;
                    const yearDate = date.getUTCFullYear()

                    console.log(item)
                    const coursesCell = document.getElementById(dayDate + "-" + monthDate + "-" + yearDate);
                    if(coursesCell) {
                        coursesCell.innerHTML = dayDate + "<br><br><b>" +
                            (dayDate + "/" + monthDate + "/" + yearDate) + " à " + (hourDate + ":" + minutesDate) +
                            "</b><br>Trainer : " + item.trainer.identity.firstName + " " + item.trainer.identity.lastName +
                            "<br>Candidat : " + item.trainer.identity.firstName + " " + item.trainer.identity.lastName +
                            // '<br><i class="fa fa-pencil-square-o"></i>' +
                            '<br><i class="fa fa-window-close" style="cursor: pointer" id="success-item" data-item='+ item.id + '></i>'
                        ;
                    }
                })
            })
            .catch(error => console.error('Error:', error));


        const items = await document.querySelectorAll("#success-item");
        console.log(items)
        if(items) {
            items.forEach(item => {
                item.addEventListener("click", () => {
                    if (confirm("êtes vous sûr d'achever le cours ?")) {
                        fetch('http://localhost:8000/api/courseDate/achieve/' + item.getAttribute("data-item"), {
                            method: 'PATCH',
                            mode: 'cors', // no-cors, *cors, same-origin
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: "application/json",
                            },
                            body: JSON.stringify({ "isAchieve" : true })
                        })
                            .then(response => response.json())
                            .then(data => {
                                console.log(data)
                                if(data?.violations === undefined) {
                                    if(data.detail) {
                                        alert(data.detail)
                                    } else {
                                        updateCalendar(month, year)
                                        alert("Données envoyées avec succès!")
                                    }
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });
            })
        }
    }

    // Fonction pour mettre à jour le calendrier
    const updateCalendar = (month, year) => {
        // Mettre à jour le mois et l'année affichés sur le calendrier
        const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
            "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
        ];

        monthYear.innerHTML = `${monthNames[month]} ${year}`;

        // Obtenir le premier jour du mois et de l'année sélectionnés
        const firstDay = new Date(year, month, 1);

        // Obtenir le dernier jour du mois et de l'année sélectionnés
        const lastDay = new Date(year, month + 1, 0);

        // Obtenir le jour de la semaine du premier jour
        const firstDayWeekDay = firstDay.getDay();

        // Obtenir le nombre de jours dans le mois sélectionné
        const daysInMonth = lastDay.getDate();

        // Initialiser une variable pour stocker les dates du calendrier
        let calendarHtml = "";

        // Boucle pour créer les lignes du calendrier
        for (let i = 0; i < 6; i++) {
            calendarHtml += "<tr>";

            // Boucle pour créer les jours de chaque ligne
            for (let j = 0; j < 7; j++) {
                let day = i * 7 + j - firstDayWeekDay + 1;

                // Si le jour est compris entre 1 et le nombre de jours dans le mois, l'afficher
                if (day > 0 && day <= daysInMonth) {
                    calendarHtml += `<td id=${day + "-" + (month + 1) + "-" + year}>${day}</td>`;
                } else {
                    calendarHtml += "<td></td>";
                }
            }

            calendarHtml += "</tr>";
        }

        // Mettre à jour la table avec les dates du calendrier
        calendarDates.innerHTML = calendarHtml;

        getData()
    }

    // Ajouter des écouteurs d'événements aux boutons pour changer de mois
    prevMonth.addEventListener("click", () => {
        if (month === 0) {
            month = 11;
            year--;
        } else {
            month--;
        }
        updateCalendar(month, year);
    });
    nextMonth.addEventListener("click", () => {
        if (month === 11) {
            month = 0;
            year++;
        } else {
            month++;
        }
        updateCalendar(month, year);
    });

    // Appeler la fonction pour mettre à jour le calendrier avec le mois et l'année actuels
    updateCalendar(month, year);

    fetch("http://127.0.0.1:8000/api/trainers")
        .then(response => response.json())
        .then(data => {
            const trainers = document.getElementById("trainerNumSs");
            console.log(data)
            data.map(item => {
                const option = document.createElement("option");
                option.value = item.numSs;
                option.text = item.identity.firstName + " " + item.identity.lastName;
                trainers.appendChild(option);
            })

            updateCalendar(month, year)
        })
        .catch(error => console.error('Error:', error));

    const trainerNumSs = document.getElementById("trainerNumSs");
    document.addEventListener("change", () => {
        updateCalendar(month, year)
    }, false)

    getData()
</script>
</html>
