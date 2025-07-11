import json
import csv
import html
from bs4 import BeautifulSoup

# Fonction pour nettoyer les entités HTML
def clean_html_entities(text):
    if text:
        return html.unescape(text)
    return text

# Charger le fichier HTML
with open("musicfestivalwizard_html/page1.html", "r", encoding="utf-8") as file:
    html_content = file.read()

soup = BeautifulSoup(html_content, 'html.parser')

# Extraire tous les scripts JSON-LD contenant les infos de festivals
festivals_data = []
for script in soup.find_all("script", type="application/ld+json"):
    try:
        data = json.loads(script.string)
        if isinstance(data, dict) and data.get("@type") == "Festival":
            festivals_data.append({
                "name": clean_html_entities(data.get("name")),
                "url": data.get("url"),
                "image": data.get("image"),
                "startDate": data.get("startDate"),
                "endDate": data.get("endDate"),
                "description": clean_html_entities(data.get("description")),
                "location": clean_html_entities(data.get("location", {}).get("name")),
                "city": clean_html_entities(data.get("location", {}).get("address", {}).get("addressLocality")),
                "region": clean_html_entities(data.get("location", {}).get("address", {}).get("addressRegion")),
            })
    except json.JSONDecodeError:
        continue

# Exporter vers un fichier CSV
csv_file = "data/festivals.csv"
with open(csv_file, mode='w', newline='', encoding='utf-8') as f:
    writer = csv.DictWriter(f, fieldnames=festivals_data[0].keys())
    writer.writeheader()
    writer.writerows(festivals_data)

print(f"✅ Données enregistrées dans {csv_file}")
