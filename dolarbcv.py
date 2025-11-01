import requests
from bs4 import BeautifulSoup
import time


url = "https://www.bcv.org.ve"


user_agents = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.82 Safari/537.36"
]


CRAWL_DELAY = 3

for user_agent in user_agents:
    headers = {
        "User-Agent": user_agent,
        "Referer": "https://www.google.com",
        "Accept-Language": "es-VE,es;q=0.9"
    }
    try:
        response = requests.get(url, headers=headers, verify=False, timeout=10)
        response.raise_for_status()

        time.sleep(CRAWL_DELAY)

        soup = BeautifulSoup(response.text, "html.parser")

        dolar_element = soup.find("div", {"id": "dolar"})
        if dolar_element:
            price_element = dolar_element.find("strong")
            if price_element:
                dolar_price = price_element.text.strip()
                print(dolar_price)
                break
            else:
                print("No se encontró el precio dentro del elemento <strong>.")
        else:
            print("No se encontró el contenedor del dólar en la página.")

    except requests.exceptions.SSLError as ssl_error:
        print(f"Error SSL: {ssl_error}")
    except requests.exceptions.RequestException as e:
        print(f"Error al acceder a la URL con User-Agent {user_agent}: {e}")
