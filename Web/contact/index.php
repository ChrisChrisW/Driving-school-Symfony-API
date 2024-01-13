<!DOCTYPE html>

<html lang="fr">
<?php require_once '../templates/_head.php'; ?>

<body>
<?php require_once './_sub-header.php'; ?>

<section class="location">
    <iframe
            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2625.547117826137!2d2.4478236!3d48.6222348!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x1f7d0b9c9f9e9f0c9!2sENSIIE!5e0!3m2!1sfr!2sfr!4v1611707838345!5m2!1sfr!2sfr"
            width="720" height="445" frameborder="0" style="border:0" allowfullscreen></iframe>
</section>

<section class="contact">
    <div class="row">
        <div class="contact-col">
            <div>
                <i class="fa fa-home"></i>
                <span>
                        <h5>ENSIIE</h5>
                        <p>Evry-Courcouronnes, 91000</p>
                    </span>
            </div>
            <div>
                <i class="fa fa-phone"></i>
                <span>
                        <h5>01 XX XX XX XX</h5>
                        <p>Ouvert lundi au vendredi</p>
                    </span>
            </div>
            <div>
                <i class="fa fa-envelope-o"></i>
                <span>
                        <h5>info@ensiie.com</h5>
                        <p>dirlap@ensiie.com</p>
                    </span>
            </div>
        </div>
        <div class="contact-col">
            <form method="post" action="contact-form-handler.php">
                <input type="text" name="name" placeholder="Nom" required>
                <input type="email" name="email" placeholder="Adresse" required>
                <input type="text" name="subject" placeholder="Sujet" required>
                <textarea rows="8" name="message" placeholder="Message" required></textarea>
                <button type="submit" class="hero-btn red-btn">Envoi</button>
            </form>
        </div>
    </div>
</section>

<?php require_once '../templates/_footer.php'; ?>
</body>

<?php require_once '../templates/_scripts.php'; ?>
</html>