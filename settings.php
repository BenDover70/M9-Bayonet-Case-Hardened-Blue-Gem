<?php
    require_once 'functions.php';
    require_once 'head.php';
    require_once 'navbar.php';
?>

<body data-bs-theme="dark">
  <div class="text-center mt-4">
    <h1>Einstellungen</h1>
    <?php echo $message; ?>  
  </div>

  <br>

  <div class="container">
    <div class="d-flex align-items-center gap-2">
      <span class="fw-semibold">Darkmode:</span>
      <div class="form-check form-switch mx-4">
        <input 
          class="form-check-input p-2"
          type="checkbox" 
          role="switch"
          id="flexSwitchCheckChecked"
          checked
          onclick="toggleTheme()">
      </div>
    </div>
  </div>

    <script>
        function toggleTheme() {
        const body = document.body;
        const currentTheme = body.dataset.bsTheme;

        if (currentTheme === "dark") {
        // NICHT im localStorage speichern → Lightmode wird als 'Reset' behandelt
        body.dataset.bsTheme = "light";

        // Weiterleitung zur Logout-Seite (Session wird dort beendet)
        setTimeout(() => {
            window.location.href = "logout.php";
        }, 300);
        } else {
        // Darkmode aktivieren + speichern
        body.dataset.bsTheme = "dark";
        localStorage.setItem("theme", "dark");
        }
    }

        // Theme beim Laden setzen (nur wenn gespeichert)
        document.addEventListener("DOMContentLoaded", () => {
        const savedTheme = localStorage.getItem("theme");
        if (savedTheme === "dark") {
        document.body.dataset.bsTheme = "dark";
        } else {
        // kein Lightmode merken → immer default zu dark zurück
        document.body.dataset.bsTheme = "dark";
        }
    });
    </script>
</body>