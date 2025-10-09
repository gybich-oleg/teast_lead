const API_URL_ADD = "/api/addlead";
const API_URL_STATUS = "/api/getstatuses";
const STATIC_DATA = {
    box_id: 28,
    offer_id: 5,
    countryCode: "GB",
    language: "en",
    password: "qwerty12"
};

// Відправка ліда
document.addEventListener("DOMContentLoaded", () => {
    const leadForm = document.getElementById("leadForm");
    if (leadForm) {
        leadForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(leadForm);
            const leadData = {
                ...STATIC_DATA,
                firstName: formData.get("firstName"),
                lastName: formData.get("lastName"),
                phone: formData.get("phone"),
                email: formData.get("email"),
                ip: await getUserIP(),
                landingUrl: window.location.hostname
            };
            try {
                const res = await fetch(API_URL_ADD, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(leadData)
                });
                const result = await res.json();
                document.getElementById("leadResult").innerText =
                    result.status
                        ? `Lead додано! ID: ${result.id}, Email: ${result.email}`
                        : `Помилка: ${result.error || "Невідома"}`;
            } catch (err) {
                document.getElementById("leadResult").innerText =
                    "Помилка відправки!";
            }
        });
    }

    // Для статусів лідів
    const statusesForm = document.getElementById("filterForm");
    if (statusesForm) {
        statusesForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            await loadStatuses();
        });
        loadStatuses();
    }
});

async function getUserIP() {
    try {
        const res = await fetch("https://ipinfo.io/json?token=5df10d4ec43d7a");
        const data = await res.json();
        return data.ip || "127.0.0.1";
    } catch {
        return "127.0.0.1";
    }
}

async function loadStatuses() {
    const date_from = document.querySelector("[name='date_from']")?.value;
    const date_to = document.querySelector("[name='date_to']")?.value;
    const from = date_from ? date_from + " 00:00:00" : "";
    const to = date_to ? date_to + " 23:59:59" : "";
    const params = {
        date_from: from,
        date_to: to,
        page: 0,
        limit: 100
    };
    try {
        const res = await fetch(API_URL_STATUS, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(params)
        });
        const result = await res.json();
        if (result.status && result.data) {
            let leads = [];
            try { leads = JSON.parse(result.data); } catch { }
            const tbody = document.querySelector("#statusesTable tbody");
            tbody.innerHTML = "";
            leads.forEach(lead => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
          <td>${lead.id}</td>
          <td>${lead.email}</td>
          <td>${lead.status}</td>
          <td>${lead.ftd}</td>
        `;
                tbody.appendChild(tr);
            });
            document.getElementById("statusesError").innerText = "";
        } else {
            document.getElementById("statusesError").innerText =
                result.error || "Не вдалося отримати статуси";
        }
    } catch (err) {
        document.getElementById("statusesError").innerText = "Помилка завантаження!";
    }
}