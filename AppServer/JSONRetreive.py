# Outage Alert
# Application Server
# Retrieve JSON file from BC Hydro
# 
# ---------------------------------------------------------------------------------------

import requests, OATime, sys, pytz
from datetime import datetime as dt


# URL Resources
url = 'https://www.bchydro.com/power-outages/app/outages-map-data.json'
#url = 'https://cs.tru.ca/~npilusof20/index.html' #for testing a failed connection


# Retrieve the json file from BC Hydro --------------------------------------------------
try:
    r = requests.get(url, allow_redirects=True)

except requests.exceptions.ConnectionError:
    for line in sys.exc_info():
        print(line)
    # Program a routine that notifies the admin and/or records a server connection error                                    <----
    # Also need to handle what happens next with the program!

except: 
    # For another type of error not explicitly caught above
    for line in sys.exc_info():
        print(line)
    # Program a routine that notifies the admin and/or records the error                                                    <----
    # Also need to handle what happens next with the program!


# Test for retrieval success ------------------------------------------------------------
try:
    r.raise_for_status() # Will raise an HTTPError if the HTTP request returned an unsuccessful status code.    

except:
    for line in sys.exc_info():
        print(line)
    # Program a routine that notifies the admin in the event a HTTP request error code ocours, etc.                         <----


if len(r.history) != 0:
    # Program a routine that prompts an admin to review the byhydro json url. A redirect or other issue may be developing   <----
    # https://requests.readthedocs.io/en/master/user/quickstart/#redirection-and-history
    pass

if r.headers.get('content-type') != 'application/json':
    #code to respond to unkonwn file type retrieved                                                                         <----
    pass


# Handle JSON file ----------------------------------------------------------------------
# Get time of JSON retrieval as a Python DateTime object
try:
    currentTime = OATime.GetTimeFromURLHeader(r)

except:
    currentTime = dt.now(pytz.utc) #get current time from the system
    # Program a routine that notifies the admin that retrieving the time from the HTTP header did not work                  <----


#convert JSON file to a list of Python dictionaries:
try:
    outages = r.json()

except: 
    #Error in parsing the json file
    for line in sys.exc_info():
        print(line)
    # Program a routine that notifies the admin, etc.                                                                       <----



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
#         dateOn: 1612742400000 (or None if power is not restored)
#         lastUpdated: 1612729685000
#         regionName: Northern
#         crewEtr: 1612742400000
#         showEta: False
#         showEtr: True
#         latitude: 54.208241
#         longitude: -125.713181
#         polygon: [-125.713123, 54.207342, -125.712825, 54.207366, -125.712541, 54.207423, -125.712281, 54.207512, ...]



"""
print("Time: ", currentTime)

for outage in outages:
    if outage['dateOn'] !=  None:
        print('')
        for key in outage.keys():
            print('\t' + key + ":", outage[key]) 
    
    elif outage['dateOn'] ==  None:
        print('')
        for key in outage.keys():
            print('\t' + key + ":", outage[key]) 
"""


#print (outages.keys())

#print("Start: ", outages)

#ctString = currentTime.strftime('%Y-%m-%d %H:%M:%S.%f %z %Z')
#print("TIme:  " + ctString)
#myTime = dt.strptime(ctString, '%Y-%m-%d %H:%M:%S.%f %z %Z')
#print("Time2:", myTime)



#from pprint import pprint
#pprint(vars(r))

#print(r.json())
#print(r.headers.get('Date'))
#print(currentTime)
#print(r.headers.get('content-type')) #'application/json'






