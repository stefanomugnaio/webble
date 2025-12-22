import '../styles/envoi_devis.scss';

document.addEventListener("DOMContentLoaded", () => {

    console.log("JS envoi_devis chargé.");

    // ID réel généré par Symfony
    const check = document.getElementById("devis_contrat_maintenance");

    if (!check) {
        console.error("❌ Checkbox introuvable dans le DOM !");
        return;
    }

    const row = document.getElementById("maintenanceRow");
    const maintenanceAmount = document.getElementById("maintenanceAmount");
    const tvaAmount = document.getElementById("tvaAmount");
    const totalTtc = document.getElementById("totalTtc");

    const siteHT = parseFloat(window.montantSiteHT ?? 0);
    const maintenanceHTNormal = parseFloat(window.maintenanceHT ?? 0);
    const offer = (window.offerLabel ?? "").toLowerCase();
    const tauxTVA = parseFloat(window.tauxTVA ?? 0.20);

    // Offre Sérieuse → 3 mois gratuits = 9 mois payés
    const maintenanceHT =
        offer.includes("sérieuse") || offer.includes("serieuse")
            ? 30 * 9
            : maintenanceHTNormal;

    function updateRecap() {

        const checked = check.checked;

        // Grise / dégrise la ligne
        row.classList.toggle("gray-row", !checked);

        const maintenanceValue = checked ? maintenanceHT : 0;

        maintenanceAmount.textContent =
            maintenanceValue.toFixed(2).replace(".", ",") + " €";

        const totalHT = siteHT + maintenanceValue;
        const tva = totalHT * tauxTVA;
        const ttc = totalHT + tva;

        tvaAmount.textContent = tva.toFixed(2).replace(".", ",") + " €";
        totalTtc.textContent = ttc.toFixed(2).replace(".", ",") + " €";
    }

    // Quand on clique
    check.addEventListener("change", updateRecap);

    // Initialisation
    updateRecap();
});
