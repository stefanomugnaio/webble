import '../styles/envoi_devis.scss';

document.addEventListener("DOMContentLoaded", () => {
    const config = window.devisRecapConfig ?? {};

    const webblePlusCheck = document.getElementById("devis_contrat_maintenance");
    const domaineCheck = document.getElementById("devis_domaine");
    const hebergementCheck = document.getElementById("devis_hebergement");

    if (!webblePlusCheck) {
        return;
    }

    const webblePlusRow = document.getElementById("webblePlusRow");
    const webblePlusAmount = document.getElementById("webblePlusAmount");
    const webblePlusInfo = document.getElementById("webblePlusInfo");

    const domaineRow = document.getElementById("domaineRow");
    const hebergementRow = document.getElementById("hebergementRow");

    const domaineAmount = document.getElementById("domaineAmount");
    const hebergementAmount = document.getElementById("hebergementAmount");

    const tvaAmount = document.getElementById("tvaAmount");
    const totalTtc = document.getElementById("totalTtc");

    const siteHT = Number(config.site_ht ?? 0);
    const tauxTVA = Number(config.taux_tva ?? 0);

    const webblePlusPrixMensuelHT = Number(config.webble_plus?.prix_mensuel_ht ?? 0);
    const webblePlusMoisOfferts = Number(config.webble_plus?.mois_offerts ?? 0);
    const webblePlusPremierMoisFacture = Number(config.webble_plus?.premier_mois_facture ?? 1);

    const domaineHT = Number(config.options?.domaine?.prix_ht ?? 0);
    const hebergementHT = Number(config.options?.hebergement?.prix_ht ?? 0);

    function formatEuro(value) {
        return new Intl.NumberFormat("fr-FR", {
            style: "currency",
            currency: "EUR",
        }).format(value);
    }

    function updateWebblePlusInfo() {
        if (!webblePlusAmount || !webblePlusInfo) {
            return;
        }

        if (!webblePlusCheck.checked) {
            webblePlusRow?.classList.add("gray-row");
            webblePlusAmount.textContent = "Non souscrit";
            webblePlusInfo.textContent = "Non inclus dans le total de création.";
            return;
        }

        webblePlusRow?.classList.remove("gray-row");
        webblePlusAmount.textContent = `${formatEuro(webblePlusPrixMensuelHT)} / mois`;

        if (webblePlusMoisOfferts > 0) {
            webblePlusInfo.textContent =
                `${webblePlusMoisOfferts} mois offerts, puis facturation à partir du ${webblePlusPremierMoisFacture}e mois. Non inclus dans le total de création.`;
            return;
        }

        webblePlusInfo.textContent =
            "Facturation mensuelle après la mise en ligne. Non inclus dans le total de création.";
    }

    function updateRecap() {
        const domaineChecked = domaineCheck ? domaineCheck.checked : false;
        const hebergementChecked = hebergementCheck ? hebergementCheck.checked : false;

        const domaineValue = domaineChecked ? domaineHT : 0;
        const hebergementValue = hebergementChecked ? hebergementHT : 0;

        if (domaineRow) {
            domaineRow.classList.toggle("gray-row", !domaineChecked);
        }

        if (domaineAmount) {
            domaineAmount.textContent = formatEuro(domaineValue);
        }

        if (hebergementRow) {
            hebergementRow.classList.toggle("gray-row", !hebergementChecked);
        }

        if (hebergementAmount) {
            hebergementAmount.textContent = formatEuro(hebergementValue);
        }

        const totalHT = siteHT + domaineValue + hebergementValue;
        const tva = totalHT * tauxTVA;
        const total = totalHT + tva;

        if (tvaAmount) {
            tvaAmount.textContent = formatEuro(tva);
        }

        if (totalTtc) {
            totalTtc.textContent = formatEuro(total);
        }

        updateWebblePlusInfo();
    }

    webblePlusCheck.addEventListener("change", updateRecap);

    if (domaineCheck) {
        domaineCheck.addEventListener("change", updateRecap);
    }

    if (hebergementCheck) {
        hebergementCheck.addEventListener("change", updateRecap);
    }

    updateRecap();
});
