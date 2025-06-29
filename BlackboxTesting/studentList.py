from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager
import time

# Set up the Chrome driver with Service object
service = Service(ChromeDriverManager().install())
driver = webdriver.Chrome(service=service)

# Open UIU
driver.get("http://localhost/SmartLibrary/studentList.php")

# Print the title of the page
print(driver.title)

#input Searchbar
Searchbar = driver.find_element(By.NAME, 'search')
Searchbar.send_keys('011221044')
time.sleep(2) 



#search button
login = driver.find_element(By.XPATH, '/html/body/div/div/main/div[1]/form/button')
login.click()
time.sleep(2)

#Delete button
delete_button = driver.find_element(By.XPATH, '/html/body/div/div/main/div[2]/table/tbody/tr/td[6]/form/button')
delete_button.click()
time.sleep(2)

# Keep browser open for 10000 seconds (you can reduce this if needed)
time.sleep(2)
