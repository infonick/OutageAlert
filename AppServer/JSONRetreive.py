# Outage Alert
# Application Server
# Retrieve JSON file from BC Hydro
# 
# ---------------------------------------------------------------------------------

import requests, pytz
from datetime import datetime as dt

url = 'https://www.bchydro.com/power-outages/app/outages-map-data.json'


currentTime = dt.now(pytz.utc)
r = requests.get(url, allow_redirects=False)



#print("start: ", r.content)

print("Time: ", currentTime)

