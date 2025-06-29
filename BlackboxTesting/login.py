from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager
import time

# Set up the Chrome driver with Service object
service = Service(ChromeDriverManager().install())
driver = webdriver.Chrome(service=service)

# Open UIU
driver.get("http://localhost/SmartLibrary/login.php")

# Print the title of the page
print(driver.title)

#input studentId
studentId = driver.find_element(By.NAME, 'student_id')
studentId.send_keys('011221257')
time.sleep(2) 

#email
email = driver.find_element(By.NAME, 'email')
email.send_keys('mzihad221257@bscse.uiu.ac.bd')

time.sleep(2) 

#login button
login = driver.find_element(By.XPATH, '/html/body/div/div/div[2]/form/button')
login.click()



# Keep browser open for 10000 seconds (you can reduce this if needed)
time.sleep(10000)
