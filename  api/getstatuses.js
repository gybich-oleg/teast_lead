export default async function handler(req, res) {
    if (req.method !== "POST") {
        res.status(405).json({ error: "Method not allowed" });
        return;
    }

    const CRM_URL = "https://crm.belmar.pro/api/v1/getstatuses";
    const TOKEN = "ba67df6a-a17c-476f-8e95-bcdb75ed3958"; // заміни на актуальний!
    try {
        const crmRes = await fetch(CRM_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "token": TOKEN,
            },
            body: JSON.stringify(req.body),
        });
        const data = await crmRes.json();
        res.status(200).json(data);
    } catch (error) {
        res.status(500).json({ error: "CRM API error" });
    }
}