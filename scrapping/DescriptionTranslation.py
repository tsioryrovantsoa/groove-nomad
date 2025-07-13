import pandas as pd
from deep_translator import GoogleTranslator
import re

# Charger les données
df = pd.read_csv("data/festivals_formatted.csv")

# Étape 1 : créer une liste de noms courts (ex : "Bad Day", "Dreamstate")
def extract_keywords(name):
    if pd.isna(name): return []
    # On prend les 2 premiers mots en général (simple et souvent suffisant)
    return name.replace("Festival", "").split()[:2]

# Construire la liste des mots à protéger
keywords = set()
for name in df["name"].dropna():
    for word in extract_keywords(name):
        if len(word) > 2:  # éviter les petits mots comme "in", "of", etc.
            keywords.add(word)

# Étape 2 : protection
def protect_keywords(text, keywords):
    for word in keywords:
        text = re.sub(rf"\b{re.escape(word)}\b", f"{{{{{word}}}}}", text)
    return text

# Étape 3 : déprotection
def unprotect_keywords(text):
    return re.sub(r"\{\{(.+?)\}\}", r"\1", text)

# Étape 4 : traduction avec protection
def safe_translate(text):
    try:
        return GoogleTranslator(source='en', target='fr').translate(text)
    except:
        return text

# Appliquer
df["description_protected"] = df["description"].astype(str).apply(lambda x: protect_keywords(x, keywords))
df["description_translated"] = df["description_protected"].apply(lambda x: safe_translate(x))
df["description_fr"] = df["description_translated"].apply(unprotect_keywords)

# Nettoyage final
df.drop(columns=["description_protected", "description_translated"], inplace=True)
df.to_csv("festivals_translated_fr.csv", index=False, encoding='utf-8')
print("✅ Traductions complètes avec protection des noms courts.")

