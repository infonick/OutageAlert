# Outage Alert
# Application Server
# Retrieve JSON file from BC Hydro
# 
# ---------------------------------------------------------------------------------------

import requests, OATime, sys, pytz, AppModel, json
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



# Sort outages into new and existing categories -----------------------------------------

# Make a list of outage id's from the JSON file
outageList = []
for outage in outages:
    outageList.append(outage['id'])

# Retrieve existing outages from the database
(dbOutages, err) = AppModel.GetOutageList(outageList)

if err == None:
    # Make a list of outage id's that exist in the database
    outageListRetrieved = []
    for outage in dbOutages:
        outageListRetrieved.append(outage['id'])

    # Sort JSON outages into two lists - new and existing outages
    newOutages = []
    oldOutages = []

    for outage in outages:
        if outage['id'] in outageListRetrieved:
            oldOutages.append(outage.copy())
        else:
            newOutages.append(outage.copy())

else:
    #There was a problem retrieving the existing outages from the database. Handle this error                               <----
    print("ERROR RETRIEVING EXISTING OUTAGES FROM DATABASE!")


# Save new outage data to database ------------------------------------------------------

err = AppModel.SaveNewOutages(newOutages)

if err != None:
    #There was a problem saving the new outages to the database. Handle this error                                          <----
    print("NEW OUTAGE DATA NOT SAVED TO DATABASE!")
    print(err)


# Alert users of new outages ------------------------------------------------------------                                   <----



# Compare old outages to find changes, update DB, and alert users -----------------------                                   <----

updateOutages = []

for oldOutage in oldOutages:

    target = oldOutage['id'] 
    left = 0
    right = len(dbOutages)-1

    while left <= right:
        attempt = (left + right) // 2
        if target == dbOutages[attempt]['id']:
            dbOutDict = json.loads(pdbOutages[attempt]['json'])
            for key in oldOutage:
                if oldOutage[key] != dbOutDict[key]:
                    updateOutages.append(dbOutDict.copy())
            break
            
        elif target <= dbOutages[attempt]['id']:
            right = attempt − 1

        else:
            left = attempt + 1
            
        #return unsuccessful


    
    in outageListRetrieved:
        oldOutages.append(outage.copy())
    else:
        newOutages.append(outage.copy())



# Update database with new outage information
err = AppModel.UpdateOutage(updateOutages)

if err != None:
    #There was a problem saving the updated outages to the database. Handle this error                                      <----
    print("UPDATED OUTAGE DATA NOT SAVED TO DATABASE!")
    print(err)






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






