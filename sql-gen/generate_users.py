from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.chrome.options import Options

import datetime 
import names

url = 'http://admin.bts.localhost/user/create'


# 15 developer - 5 triager - 5 reviewer - 25 user
setup_options = Options()
setup_options.add_argument('start-maximized')
setup_options.add_experimental_option("detach", True)


setup_options.add_experimental_option("useAutomationExtension", False)
setup_options.add_experimental_option("excludeSwitches", ["enable-automation"])
setup_options.add_argument('disable-infobars')

driver = webdriver.Chrome(options=setup_options)

driver.get(url)
driver.implicitly_wait(3)


# respective no. of roles 
roles = [(15, 'i1'), (5, 'i2'), (5, 'i3'), (25, 'i4')]
dev_role = {'i1': 'developer', 'i2': 'reviewer', 'i3': 'triager', 'i4': 'user'}

names = [names.get_full_name() for i in range(50)]

login_user = driver.find_element_by_id("loginform-username")
login_pass = driver.find_element_by_id("loginform-password")

login_user.send_keys("webmaster")
login_pass.send_keys("webmaster")

submit = driver.find_element_by_class_name("btn-primary")
submit.click()

for role in roles:
    for iteration in range(role[0]):

        driver.implicitly_wait(3)
        username = driver.find_element_by_id("userform-username")
        
        used_name = names.pop()
        username.send_keys(used_name)

        email = driver.find_element_by_id("userform-email")
        email.send_keys(f"{used_name.replace(' ', '')}@nomail.com")

        password = driver.find_element_by_id("userform-password")
        password.send_keys("password")

        status = driver.find_element_by_id("userform-status")
        for option in status.find_elements_by_tag_name('option'):
            if option.text == 'Active':
                option.click()
                break
        
        b_role = driver.find_element_by_id(role[1])
        driver.execute_script("arguments[0].click();", b_role)

        submit = driver.find_element_by_class_name("btn-primary")
        submit.click()

        print(f"[{datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Generated {used_name} -> {iteration+1} {dev_role[role[1]]}")
        new_user = driver.find_element_by_class_name("btn-success")
        new_user.click()
