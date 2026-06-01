<header>

    <div class="logo-container">

        <img
            src="images/logo.svg"
            alt="Logo del Gattile Felice"
            class="logo">

        <div>

            <h1>Gattile Felice</h1>

            <p>
                Adozioni e volontariato
            </p>

        </div>

    </div>

    <div class="user-status">

        <?php if (isset($_SESSION["username"])): ?>

            Utente:
            <strong>
                <?= htmlspecialchars($_SESSION["username"]) ?>
            </strong>

        <?php else: ?>

            Non loggato

        <?php endif; ?>

    </div>

</header>

<nav aria-label="Menu principale">

    <ul>

        <li>
            <a href="index.php">
                Home
            </a>
        </li>

        <li>
            <a href="gatti.php">
                I nostri gatti
            </a>
        </li>

        <li>
            <a href="volontariato.php">
                Volontariato
            </a>
        </li>

        <?php if (!isset($_SESSION["user_id"])): ?>

            <li>
                <a href="login.php">
                    Login
                </a>
            </li>

            <li>
                <a href="registrazione.php">
                    Registrazione
                </a>
            </li>

        <?php endif; ?>

        <?php if (isset($_SESSION["user_id"])): ?>

            <li>
                <a href="logout.php">
                    Logout
                </a>
            </li>

        <?php endif; ?>

        <?php if (
            isset($_SESSION["is_admin"])
            && $_SESSION["is_admin"] === true
        ): ?>

            <li>
                <a href="nuovo-gatto.php">
                    Nuovo gatto
                </a>
            </li>

        <?php endif; ?>

    </ul>

</nav>