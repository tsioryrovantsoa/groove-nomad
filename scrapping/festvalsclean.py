import pandas as pd
from region_mappings import abbr_to_region
from city_to_region import city_to_region

# --- 1. Chargement du CSV
df = pd.read_csv("data/festivals.csv")

# --- 2. Formatage des dates
df["startDate"] = pd.to_datetime(df["startDate"]).dt.strftime("%Y-%m-%d")
df["endDate"] = pd.to_datetime(df["endDate"]).dt.strftime("%Y-%m-%d")

# --- 3. description_fr juste après description
df.insert(df.columns.get_loc("description") + 1, "description_fr", "")

# --- 4. Initialiser la colonne region_abbr
df["region_abbr"] = ""


# --- Compléter les régions vides depuis la ville
def fill_region_from_city(row):
    city = str(row["city"]).strip()  # nettoyage
    print(f"Vérification de la ville: {city}")
    print(f"Région actuelle: {row['region']}")
    if pd.isna(row["region"]) and city in city_to_region:
        region_found = city_to_region[city]
        print(f"Remplissage de la région depuis la ville: {row['city']} -> {region_found}")
        return region_found
    return row["region"]

df["region"] = df.apply(fill_region_from_city, axis=1)

# --- 5. Déplacement des régions abrégées vers region_abbr (en MAJ)
def handle_region(value):
    if isinstance(value, str) and len(value.strip()) == 2:
        print(f"Déplacement de la région abrégée: {value.strip().upper()}")
        return pd.Series(
            ["", value.strip().upper()]
        )  # déplacer vers abbr, mettre region vide
    else:
        print(f"Aucune région abrégée trouvée pour: {value}")
        return pd.Series([value, ""])  # laisser region, region_abbr vide


df[["region", "region_abbr"]] = df["region"].apply(handle_region)


def fill_region(row):
    if row["region"] == "" and row["region_abbr"] in abbr_to_region:
        return abbr_to_region[row["region_abbr"]]
    return row["region"]


df["region"] = df.apply(fill_region, axis=1)

# --- 1. Inverser le mapping : nom complet → abréviation
region_to_abbr = {v: k for k, v in abbr_to_region.items()}

# --- 2. Remplir region_abbr à partir de region
def fill_region_abbr(row):
    if (pd.isna(row["region_abbr"]) or row["region_abbr"] == "") and row["region"] in region_to_abbr:
        return region_to_abbr[row["region"]]
    return row["region_abbr"]

df["region_abbr"] = df.apply(fill_region_abbr, axis=1)

# --- 6. Export
df.to_csv("data/festivals_formatted.csv", index=False)
