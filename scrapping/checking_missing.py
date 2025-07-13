import pandas as pd

# Charger le fichier CSV
df = pd.read_csv("data/festivals_formatted.csv")

# 🔍 1. Afficher les colonnes avec des valeurs manquantes
print("\n--- Colonnes avec valeurs manquantes ---")
missing_by_col = df.isna().sum()
missing_by_col = missing_by_col[missing_by_col > 0]
if missing_by_col.empty:
    print("✅ Aucune valeur manquante détectée.")
else:
    print(missing_by_col)

# 🔍 2. Afficher les lignes contenant des valeurs manquantes
print("\n--- Lignes incomplètes ---")
incomplete_rows = df[df.isna().any(axis=1)]
if incomplete_rows.empty:
    print("✅ Toutes les lignes sont complètes.")
else:
    print(f"{len(incomplete_rows)} lignes avec des données manquantes.\n")
    print(incomplete_rows[["name", "region", "region_abbr"]])  # afficher les colonnes critiques

# 🔍 3. (Optionnel) Afficher les lignes avec des chaînes vides
print("\n--- Lignes avec champs vides ('') ---")
empty_string_rows = df[(df == "").any(axis=1)]
if empty_string_rows.empty:
    print("✅ Aucune chaîne vide détectée.")
else:
    print(f"{len(empty_string_rows)} lignes contiennent des champs vides.")
    print(empty_string_rows[["name", "region", "region_abbr"]])

# Nettoyer et normaliser la colonne
df["location"] = df["location"].fillna("").astype(str).str.strip()

# Filtrer les lignes où region_abbr est vide
missing_region_abbr = df[df["location"] == ""]

# Affichage
print(f"\n--- Lignes où 'location' est vide ({len(missing_region_abbr)} lignes) ---")
print(missing_region_abbr[["name", "city", "region", "location"]])

print(df.dtypes)
