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



# Compare old outages to find changes, update DB, and alert users -----------------------                                   

updateOutages = [] #stores tuples of outages (Outage ID #, Outage Info in DB, Updated Outage Info)

dbOutagesCopy = dbOutages.copy()

for oldOutage in oldOutages:

    #use a binary search to compare outage information from BC Hydro with outage information from the DB
    target = oldOutage['id'] 
    left = 0
    right = len(dbOutagesCopy)-1

    while left <= right:
        attempt = (left + right) // 2
        if target == dbOutagesCopy[attempt]['id']:
            dbOutageInfo = dbOutagesCopy[attempt]['json']
            updatedOutageInfo = oldOutage[attempt]['json']
            for key in updatedOutageInfo:
                if updatedOutageInfo[key] != dbOutageInfo[key]:
                    updateOutages.append((target, dbOutageInfo.copy(),updatedOutageInfo.copy()))
                    dbOutagesCopy.pop(attempt) #remove this item from the dbOutage list.
                    break
            break
            
        elif target <= dbOutagesCopy[attempt]['id']:
            right = attempt - 1

        else:
            left = attempt + 1
            
    
    if left > right:
        # Program a routine to deal with cases where  two outage records did not coincide                                   <----
        print('ERROR: AN EXISTING OUTAGE WAS NOT FOUND IN THE DB RECORDS!')



# Update database with new outage information
dbUpdates = [outage for (_,_,outage) in updateOutages] #python list comprehension

err = AppModel.UpdateOutage(dbUpdates)

if err != None:
    #There was a problem saving the updated outages to the database. Handle this error                                      <----
    print("UPDATED OUTAGE DATA NOT SAVED TO DATABASE!")
    print(err)



# Figure out what changed so we can act on those changes
for outageNum in range(len(updateOutages)):
    (_,dbOutageInfo,updatedOutageInfo) = updateOutages[outageNum]
    for key in updatedOutageInfo:
        if updatedOutageInfo[key] == dbOutageInfo[key]:
            updateOutages.pop(key)
            dbOutageInfo.pop(key)
            break
    if len(updatedOutageInfo) == len(dbOutageInfo) == 0
        updateOutages.pop(outageNum)


    
#Alert users to outage updates

updateAlerts = []

for outageNum in range(len(updateOutages)):
    (outageID, dbOutageInfo,updatedOutageInfo) = updateOutages[outageNum]

    outageMessages = []

    for key in updatedOutageInfo:
        oldValue = dbOutageInfo[key]
        newValue = updatedOutageInfo[key]

        if key == 'cause':
            if oldValue == None:
                outageMessages.append("Cause of power outage: " + newValue)
            else:
                outageMessages.append("Cause of power outage updated from \'" + oldValue + "\' to \'" + newValue + "\'.")
            break
            break


        elif key == 'crewStatusDescription':
            if oldValue == None:
                outageMessages.append("Power restoration crew status: " + newValue)
            else:
                outageMessages.append("Power restoration crew status updated from \'" + oldValue + "\' to \'" + newValue + "\'.")
            break


        elif key == 'crewEta':
            dateTimeCrewETA = OATime.DateTimeFromJSToPython(newValue)
            dateTimeCrewETA = OATime.PythonChangeTimeZone(dateTimeCrewETA, 'America/Vancouver')
            if oldValue == None:
                outageMessages.append("Power restoration crew ETA: " + datetime.datetime.strftime(dateTimeOff, '%Y-%b-%d %I:%M:%S %p %Z'))
            else:
                outageMessages.append("Power restoration crew ETA updated to: " + datetime.datetime.strftime(dateTimeOff, '%Y-%b-%d %I:%M:%S %p %Z'))
            break


        elif key == 'dateOff':
            dateTimeOff = OATime.DateTimeFromJSToPython(newValue)
            dateTimeOff = OATime.PythonChangeTimeZone(dateTimeOff, 'America/Vancouver')
            if oldValue == None:
                outageMessages.append("Power outage began on " + datetime.datetime.strftime(dateTimeOff, '%Y-%b-%d %I:%M:%S %p %Z'))
            else:
                outageMessages.append("Power outage start time was updated to " + datetime.datetime.strftime(dateTimeOff, '%Y-%b-%d %I:%M:%S %p %Z'))
            break


        elif key == 'dateOn':
            dateTimeOn = OATime.DateTimeFromJSToPython(newValue)
            dateTimeOn = OATime.PythonChangeTimeZone(dateTimeOn, 'America/Vancouver')
            if oldValue == None:
                outageMessages.append("Power restored on " + datetime.datetime.strftime(dateTimeOn, '%Y-%b-%d %I:%M:%S %p %Z'))
            else:
                outageMessages.append("Power restoration time was updated to " + datetime.datetime.strftime(dateTimeOn, '%Y-%b-%d %I:%M:%S %p %Z'))
            break


        elif key == 'polygon':
            #need to check for newly affected customers, or newly restored customers if the polygon changes                 <----
            break
        elif key == 'lastUpdated':
            #What do we do with this part? anything?                                                                        <----
            break
        elif key == 'numCustomersOut':
            break
        elif key == 'id':
            break
        elif key == 'gisId':
            break
        elif key == 'regionId':
            break
        elif key == 'municipality':
            break
        elif key == 'area':
            break
        elif key == 'regionName':
            break
        elif key == 'crewEtr':
            break
        elif key == 'showEta':
            break
        elif key == 'showEtr':
            break
        elif key == 'latitude':
            break
        elif key == 'longitude':
            break
    
    updateAlerts.append((outageID, outageMessages)) #create a tuple consisting of the outage ID #, and all the related update messages


#send the updateAlerts tuple-list to a function that sends the messages to our users                                        <----



# Outage dictionary keys w/ examples
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






