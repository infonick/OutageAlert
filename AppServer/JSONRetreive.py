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

#convert JSON file to a list of Python dictionaries:
outages = r.json()


#dictionary keys w/ examples
#         id: 1588375
#         gisId: 1578468
#         regionId: 1602964060
#         municipality: Burns Lake
#         area: M 32/3  WADDLE
#         cause: Wire down
#         numCustomersOut: 1
#         crewStatusDescription: Crew on-site
#         crewEta: 1612723800000
#         dateOff: 1612720680000
#         dateOn: 1612742400000 (or None)
#         lastUpdated: 1612729685000
#         regionName: Northern
#         crewEtr: 1612742400000
#         showEta: False
#         showEtr: True
#         latitude: 54.208241
#         longitude: -125.713181
#         polygon: [-125.713123, 54.207342, -125.712825, 54.207366, -125.712541, 54.207423, -125.712281, 54.207512, ...]




print("Time: ", currentTime)

for outage in outages:
    if outage['dateOn'] !=  None:
        print('')
        for key in outage.keys():
            print('\t' + key + ":", outage[key]) 

#print (outages.keys())

#print("Start: ", outages)

#ctString = currentTime.strftime('%Y-%m-%d %H:%M:%S.%f %z %Z')
#print("TIme:  " + ctString)
#myTime = dt.strptime(ctString, '%Y-%m-%d %H:%M:%S.%f %z %Z')
#print("Time2:", myTime)

#PST = pytz.FixedOffset(-8*60) #Convert to PST -8h
#myTime = currentTime.astimezone(PST)
#print(myTime)

#from pprint import pprint
#pprint(vars(r))

#print(r.json())