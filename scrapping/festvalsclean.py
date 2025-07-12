import pandas as pd
from region_mappings import abbr_to_region
from city_to_region import city_to_region

# --- 1. Chargement du CSV
df = pd.read_csv("data/festivals.csv")

# --- 2. Formatage des dates
df["startDate"] = pd.to_datetime(df["startDate"]).dt.strftime("%Y-%m-%d")
df["endDate"] = pd.to_datetime(df["endDate"]).dt.strftime("%Y-%m-%d")

# --- 3. Ajout colonne description_fr après description
df.insert(df.columns.get_loc("description") + 1, "description_fr", "")

# --- 4. Initialiser region_abbr si absente
if "region_abbr" not in df.columns:
    df["region_abbr"] = ""

# --- 5. Remplir region depuis city si vide
def fill_region_from_city(row):
    city = str(row["city"]).strip()
    if pd.isna(row["region"]) and city in city_to_region:
        return city_to_region[city]
    return row["region"]

df["region"] = df.apply(fill_region_from_city, axis=1)

# --- 6. Déplacer valeurs abrégées (2 lettres) de region → region_abbr
def handle_region(value):
    if isinstance(value, str) and len(value.strip()) == 2:
        return pd.Series(["", value.strip().upper()])
    return pd.Series([value, ""])

df[["region", "region_abbr"]] = df["region"].apply(handle_region)

# --- 7. Compléter region depuis region_abbr (si encore vide)
def fill_region(row):
    if (pd.isna(row["region"]) or row["region"] == "") and row["region_abbr"] in abbr_to_region:
        return abbr_to_region[row["region_abbr"]]
    return row["region"]

df["region"] = df.apply(fill_region, axis=1)

# --- 8. Compléter region_abbr à partir de region (dernier fallback)
region_to_abbr = {v: k for k, v in abbr_to_region.items()}

# Assurer que region_abbr est une chaîne de caractères
df["region_abbr"] = df["region_abbr"].fillna("").astype(str).str.strip()
df["region"] = df["region"].fillna("").astype(str).str.strip()
df["city"] = df["city"].fillna("").astype(str).str.strip()

def fill_region_abbr(row):
    print(f"Traitement de la ligne : {row['region']}, abréviation actuelle : {row['region_abbr']}")
    if (pd.isna(row["region_abbr"]) or row["region_abbr"] == "") and row["region"] in region_to_abbr:
        print(f"Région '{row['region']}' convertie en abréviation '{region_to_abbr[row['region']]}'")
        return region_to_abbr[row["region"]]
    return row["region_abbr"]

df["region_abbr"] = df.apply(fill_region_abbr, axis=1)

# --- 9. Export final
df.to_csv("data/festivals_formatted.csv", index=False)
