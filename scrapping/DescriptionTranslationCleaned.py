import pandas as pd
import re

# Charger le fichier CSV traduit
df = pd.read_csv("data/festivals_translated_fr.csv")

# Nettoyage des accolades encore visibles
# Supprime les { ou {{ en début, et les } ou }} en fin de mots
df["description_fr"] = df["description_fr"].astype(str)
df["description_fr"] = df["description_fr"].str.replace(r"\{+\s*", "", regex=True)
df["description_fr"] = df["description_fr"].str.replace(r"\s*\}+", "", regex=True)

# Nettoyage des valeurs vides (laisser vide, pas "NaN")
df["description_fr"] = df["description_fr"].replace("nan", "")
df["description_fr"] = df["description_fr"].fillna("")
df.loc[df["description_fr"].str.strip() == "nan", "description_fr"] = ""

# Sauvegarde du fichier final
path="data/festivals_translated_fr_cleaned.csv"
df.to_csv(path, index=False, encoding="utf-8")
print("✅ Fichier nettoyé enregistré sous 'festivals_translated_fr_final.csv'")
