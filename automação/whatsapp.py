from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
import pandas as pd
import time
import random

# Caminho do seu WebDriver (ajuste para a sua versão)
chrome_driver_path = "C:/Users/medom/Downloads/chromedriver_win32/chromedriver.exe"

# Inicializar o navegador
options = webdriver.ChromeOptions()
options.add_argument("--user-data-dir=C:/Users/medom/AppData/Local/Google/Chrome/User Data/Profile 1")  # Mantém o login ativo
driver = webdriver.Chrome(options=options)

# Abrir WhatsApp Web
driver.get("https://web.whatsapp.com")
input("Escaneie o QR Code e pressione ENTER para continuar...")  # Aguarda login

# Carregar os números da planilha
df = pd.read_excel("numeros.xlsx")

mensagem = f"Boa noite! Tudo bem? Meu nome é Victor, faço parte da comissão de esportes do Campus Rolante e, a pedido da professora Myllena e do professor Luciano, estou te enviando o link para a comunidade do Whatsapp. Como não consigo adicionar manualmente, peço que você entre APENAS nos grupos que tu se inscreveu no formulário de interesse. https://chat.whatsapp.com/Bz79YMS4uur3sYDkyRnOmX"

# Função para enviar mensagem
def enviar_mensagem(numero):
    url = f"https://web.whatsapp.com/send?phone=55{numero}&text={mensagem}"
    driver.get(url)
    time.sleep(10)  # Espera carregar a página

    try:
        # Pressiona ENTER para enviar a mensagem
        caixa_de_texto = driver.find_element(By.XPATH, '//div[@contenteditable="true"][@data-tab="10"]')
        caixa_de_texto.send_keys(Keys.ENTER)
        time.sleep(5)
    except Exception as e:
        print(f"Erro ao enviar para {numero}: {e}")

# Percorrer os números e enviar as mensagens
for index, row in df.iterrows():
    numero = str(row["Telefone"])
    enviar_mensagem(numero)
    time.sleep(random.uniform(15, 20))  # Atraso aleatório entre 15 e 30 segundos

print("Mensagens enviadas com sucesso!")
driver.quit()  # Fecha o navegador