# Instructions for generating of SQL


## Installation

Ensure that you have Selenium installed with the relevant Webdriver installed (exmaple shown here is Chrome)

[Selenium](https://selenium-python.readthedocs.io/)  
[ChromeDriver](https://chromedriver.chromium.org/downloads)

- Configure chromdriver on your system

```shell
sudo mv chromedriver /usr/bin/chromedriver
sudo chown root:root /usr/bin/chromedriver
sudo chmod +x /usr/bin/chromedriver
```

- Install the relevant python packages (ideally in a virtual environment)

```shell
python3 -m venv env
source env/bin/activate
pip3 install -r requirements.txt
```


## Usage

There are two scripts within this folder, **generate_users.py** to generate users via the application through Selenium automation,
and **generate_bugs.py** which generates the relevant .sql statements to populate the Bug, BugAction, BugTag models.

```shell
python3 generate_users.py
```

```shell
python3 generate_bugs.py
```

**Output of generate_bugs.py**
- bug.sql
- bug_action.sql
- bug_tag.sql
