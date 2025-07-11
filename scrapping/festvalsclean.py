import pandas as pd
from datetime import datetime

# Charger le fichier CSV
csv_file = "data/festivals.csv"
df = pd.read_csv(csv_file)

df["region"] = df["region"].astype(str).str.strip()


print((df["region"].unique()))


# ✅ Conversion des dates
def clean_date(date_str):
    try:
        # Cas où il y a déjà des jours et mois écrits comme "Jul 12 2025"
        dt = datetime.strptime(date_str.strip(), "%b %d %Y")
        return dt.strftime("%Y-%m-%d")
    except:
        return date_str  # En cas d'erreur, on laisse tel quel

df["startDate"] = df["startDate"].apply(clean_date)
df["endDate"] = df["endDate"].apply(clean_date)

region_mapping = {
    "AL": "Alabama", "AK": "Alaska", "AZ": "Arizona", "AR": "Arkansas", "CO": "Colorado",
    "CT": "Connecticut", "CA": "California", "DE": "Delaware", "GA": "Georgia", "HI": "Hawaii",
    "ID": "Idaho", "IN": "Indiana", "IA": "Iowa", "KS": "Kansas", "KY": "Kentucky",
    "LA": "Louisiana", "ME": "Maine", "MD": "Maryland", "MA": "Massachusetts", "MI": "Michigan",
    "MS": "Mississippi", "MO": "Missouri", "MT": "Montana", "NE": "Nebraska", "NV": "Nevada",
    "NH": "New Hampshire", "NJ": "New Jersey", "NM": "New Mexico", "ND": "North Dakota",
    "OH": "Ohio", "OK": "Oklahoma", "NY": "New York", "PA": "Pennsylvania", "RI": "Rhode Island",
    "SC": "South Carolina", "TN": "Tennessee", "TX": "Texas", "FL": "Florida", "IL": "Illinois",
    "UK": "United Kingdom", "UT": "Utah", "VA": "Virginia", "WV": "West Virginia", "WI": "Wisconsin",
    "WY": "Wyoming", "NC": "North Carolina", "SD": "South Dakota", "VT": "Vermont", "WA": "Washington",
    "AB": "Alberta", "MN": "Minnesota", "OR": "Oregon", "FRANCE": "France", "BELGIUM": "Belgium",
    "SWITZERLAND": "Switzerland"
}

df["region"] = df["region"].apply(lambda x: region_mapping.get(str(x).strip(), x))

# ✅ Sauvegarde du fichier nettoyé
#df.to_csv("data/festivals_cleaned.csv", index=False, encoding="utf-8")
print("✅ Fichier nettoyé sauvegardé sous 'data/festivals_cleaned.csv'")
