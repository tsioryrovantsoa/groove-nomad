from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=False)  # navigateur visible obligatoire ici
    page = browser.new_page()
    page.goto("https://www.musicfestivalwizard.com/all-festivals/")
    
    # Donne-toi le temps de vérifier que la page est bien passée
    page.wait_for_timeout(30000)  # attend manuellement 30 secondes

    # Quand la page est bien chargée (festivals visibles)
    html_content = page.content()
    with open("festivals_ok.html", "w", encoding="utf-8") as f:
        f.write(html_content)
    print("Page OK sauvegardée.")

    browser.close()
    print("Navigateur fermé.")