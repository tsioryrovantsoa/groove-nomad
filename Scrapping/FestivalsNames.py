import requests
from bs4 import BeautifulSoup
import json
import pandas as pd

festivals_data = []

for page_num in range(1, 16):  # Scraping des 15 premières pages
    url = f"https://www.musicfestivalwizard.com/all-festivals/page/{page_num}/"
    print(f"Scraping page {page_num} : {url}")
    
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
    }
    response = requests.get(url, headers=headers)
    soup = BeautifulSoup(response.content, "html.parser")

    # Sélectionne uniquement les blocs de festivals
    festival_blocks = soup.find_all("div", id="artist-lineup-container")
    print(festival_blocks)

    
    for block in festival_blocks:
        script_tag = block.find("script", type="application/ld+json")
        if script_tag:
            try:
                data = json.loads(script_tag.string)
                if data.get("@type") == "Festival":
                    name = data.get("name")
                    start_date = data.get("startDate")
                    end_date = data.get("endDate")
                    location = data.get("location", {})
                    city = location.get("address", {}).get("addressLocality", "N/A")
                    country = location.get("address", {}).get("addressRegion", "N/A")
                    
                    festivals_data.append({
                        "Nom du Festival": name,
                        "Date Début": start_date,
                        "Date Fin": end_date,
                        "Ville": city,
                        "Pays": country
                    })
            except json.JSONDecodeError:
                continue  # Ignore les erreurs JSON

# Enregistrer dans un CSV
df = pd.DataFrame(festivals_data)
df.to_csv("festivals_music_wizard_15pages.csv", index=False, encoding="utf-8")
print("Scraping terminé avec succès ! Résultats enregistrés dans 'festivals_music_wizard_15pages.csv'")
