import pandas as pd
from region_mappings import abbr_to_region
from city_to_region import city_to_region
import unicodedata

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

def remove_accents(text):
    if not isinstance(text, str):
        return ""
    return unicodedata.normalize("NFKD", text).encode("ASCII", "ignore").decode("utf-8")

# Appliquer à la colonne 'region'
df["region"] = df["region"].apply(remove_accents)

# --- 7. Compléter region depuis region_abbr (si encore vide)
def fill_region(row):
    if (pd.isna(row["region"]) or row["region"] == "") and row["region_abbr"] in abbr_to_region:
        return abbr_to_region[row["region_abbr"]]
    return row["region"]

df["region"] = df.apply(fill_region, axis=1)

# --- 8. Compléter region_abbr à partir de region (dernier fallback)
region_to_abbr = {v.strip(): k for k, v in abbr_to_region.items()}
print(f"Régions disponibles: {list(region_to_abbr.keys())}")

# Assurer que region_abbr est une chaîne de caractères
df["region_abbr"] = df["region_abbr"].fillna("").astype(str).str.strip()
df["region"] = df["region"].fillna("").astype(str).str.strip()
df["city"] = df["city"].fillna("").astype(str).str.strip()

def fill_region_abbr(row):
    region = str(row["region"]).strip()
    abbr = str(row["region_abbr"]).strip()

    print(f"\n--- Vérification de la ligne ---")
    print(f"Region: '{region}'")
    print(f"Region Abbr: '{abbr}'")

    is_abbr_nan = pd.isna(row["region_abbr"])
    is_abbr_empty = abbr == ""
    is_abbr_space = abbr == " "
    is_region_mappable = region in region_to_abbr

    print(f"-> pd.isna(region_abbr): {is_abbr_nan}")
    print(f"-> region_abbr == '': {is_abbr_empty}")
    print(f"-> region_abbr == ' ': {is_abbr_space}")
    print(f"-> region in region_to_abbr: {is_region_mappable}")

    if (is_abbr_nan or is_abbr_empty or is_abbr_space) and is_region_mappable:
        print(f"✅ Remplissage: '{region}' => '{region_to_abbr[region]}'")
        return region_to_abbr[region]

    print("❌ Pas de remplissage.")
    return abbr

df["region_abbr"] = df.apply(fill_region_abbr, axis=1)

# --- 8bis. Correction des types de données

# Colonnes à forcer en texte (string)
text_columns = [
    "name", "url", "image", "description", "description_fr",
    "location", "city", "region", "region_abbr"
]
for col in text_columns:
    df[col] = df[col].fillna("").astype(str).str.strip()

# Convertir les dates en datetime (coerce les erreurs éventuelles)
df["startDate"] = pd.to_datetime(df["startDate"], errors="coerce")
df["endDate"] = pd.to_datetime(df["endDate"], errors="coerce")

# Convertir 'page' s'il existe en entier nullable
if "page" in df.columns:
    df["page"] = pd.to_numeric(df["page"], errors="coerce").astype("Int64")

print(df.dtypes)

# --- 9. Export final
df.to_csv("data/festivals_formatted.csv", index=False)
