import json
import csv
import html
from bs4 import BeautifulSoup
import os

# Fonction pour nettoyer les entités HTML
def clean_html_entities(text):
    if text:
        return html.unescape(text)
    return text

# Dossier contenant les fichiers HTML
html_dir = "musicfestivalwizard_html"

festivals_data = []

# Boucle sur les 15 pages sauvegardées localement
for page_num in range(1, 16):
    file_path = os.path.join(html_dir, f"page{page_num}.html")
    print(f"📄 Lecture de {file_path}")
    
    with open(file_path, "r", encoding="utf-8") as file:
        html_content = file.read()

    soup = BeautifulSoup(html_content, 'html.parser')

    # Extraction des données JSON-LD dans chaque page
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
output_dir = "data"
os.makedirs(output_dir, exist_ok=True)
csv_file = os.path.join(output_dir, "festivals.csv")
with open(csv_file, mode='w', newline='', encoding='utf-8') as f:
    writer = csv.DictWriter(f, fieldnames=festivals_data[0].keys())
    writer.writeheader()
    writer.writerows(festivals_data)

print(f"✅ Données enregistrées dans {csv_file} ({len(festivals_data)} festivals)")
