<!DOCTYPE html>

<html lang="fr">
    <?php require_once 'templates/_head.php'; ?>

    <body>
        <?php require_once 'templates/_header.php'; ?>

        <section class="ecole">
            <h1>Nos auto-écoles disponibles en France</h1>
            
            <div class="row">
                <div class="ecole-col">
                    <img src="assets/images/paris.png" alt="image d'un batiment sur Paris">
                    <div class="layer">
                        <h3>Paris</h3>
                    </div>
                </div>
                <div class="ecole-col">
                    <img src="assets/images/orleans.png" alt="image d'un batiement sur Orléans">
                    <div class="layer">
                        <h3>Orléans</h3>
                    </div>
                </div>
                <div class="ecole-col">
                    <img src="assets/images/rennes.png" alt="image d'un batiment sur Rennes">
                    <div class="layer">
                        <h3>Rennes</h3>
                    </div>
                </div>
            </div>
        </section>


        <section class="cta">
            <h1>Envie d'en savoir plus?</h1>
            <a href="/contact/" class="hero-btn">CONTACTER NOUS</a>
        </section>

        <?php require_once 'templates/_footer.php'; ?>
    </body>

    <?php require_once 'templates/_scripts.php'; ?>
</html>



