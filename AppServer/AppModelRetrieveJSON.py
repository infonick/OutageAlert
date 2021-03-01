# Outage Alert
# Application Server
# AppModelRetrieveJSON
#
# This script retrieves a JSON file from BC Hydro. The JSON file contains all current power outage data.
#   
# ---------------------------------------------------------------------------------------

# Import custom modules
import AppTimeLib   # Custom date and time related functions for the OutageAlert application
import AppModelDB   # Database commands
import AppModelOutageMessages # Functions related to generating and sending messages to users.

# Import standard modules
import requests     # Allows Python to send HTTP requests, used to retrieve BC Hydro JSON file
import sys          # Used with requests module error handling
import datetime     # Provides datetime object functions
import json         # Provides encoding and decoding functions for JSON strings/files
        


# URL Resources
url = 'https://www.bchydro.com/power-outages/app/outages-map-data.json'
#url = 'https://cs.tru.ca/~npilusof20/index.html' # Used for testing a failed connection



# Retrieve the json file from BC Hydro --------------------------------------------------
try:
    r = requests.get(url, allow_redirects=True)

except requests.exceptions.ConnectionError:
    for line in sys.exc_info():
        print(line)
    # TODO: Program a routine that notifies the admin and/or records a server connection error                                    <----
    # TODO: Also need to handle what happens next with the program!

except: 
    # For another type of error not explicitly caught above
    for line in sys.exc_info():
        print(line)
    # TODO: Program a routine that notifies the admin and/or records the error                                                    <----
    # TODO: Also need to handle what happens next with the program!



# Test for retrieval success 
try:
    r.raise_for_status() # Will raise an HTTPError if the HTTP request returned an 'unsuccessful' status code.    

except:
    for line in sys.exc_info():
        print(line)
    # TODO: Program a routine that notifies the admin in the event a HTTP request error code ocours, etc.                         <----


if len(r.history) != 0:
    # TODO: Program a routine that prompts an admin to review the byhydro json url. A redirect or other issue may be developing   <----
    # https://requests.readthedocs.io/en/master/user/quickstart/#redirection-and-history
    pass


if r.headers.get('content-type') != 'application/json':
    # TODO: Program a routine to respond to an unkonwn file type that was retrieved                                               <----
    pass



# Handle the retrieved JSON file ----------------------------------------------------------------------
# Record time of JSON file retrieval
try:
    # Get the time of the JSON file retrieval as a Python DateTime object
    currentTime = AppTimeLib.GetTimeFromURLHeader(r)

except:
    currentTime = AppTimeLib.GetCurrentUTCTime() # Get the current time in UTC from the system if getting the time from the HTTP header is unsuccessful
    # TODO: Program a routine that notifies the admin that retrieving the time from the HTTP header did not work                  <----


# Convert JSON file to a list of Python dictionaries:
try:
    outages = r.json()

except: 
    # Error in parsing the json file
    for line in sys.exc_info():
        print(line)
    # TODO: Program a routine that notifies the admin, etc.                                                                       <----
    # TODO: Another thought, can an injection to OutageAlert happen if the BCHydro json is compromised?                           <----
    # TODO: If the BCHydro json is empty (no outages, or no new outages) can we skip all other processing?



# Sort outages into new and existing categories -----------------------------------------

# Make a list of outage id's from the JSON file
outageIDList = []
for outage in outages:
    outageIDList.append(outage['id'])

# Retrieve existing outages from the database
(dbOutages, err) = AppModelDB.GetOutageList(outageIDList)

if err != None:
    # TODO: There was a problem retrieving the existing outages from the database. Handle this error                               <----
    print("ERROR RETRIEVING EXISTING OUTAGES FROM DATABASE!")

else:
    # Make a list of outage id's that already exist in the database
    outageListRetrieved = []
    for outage in dbOutages:
        outageListRetrieved.append(outage['id'])

    # Sort JSON outages into two lists - new and existing outages
    newOutages = []
    existingOutages = []

    for outage in outages:
        if outage['id'] in outageListRetrieved: # If the outage already exists in the database
            existingOutages.append(outage.copy()) # Add it to the list of existing outages
        else:
            newOutages.append(outage.copy()) # Otherwise add it to a list of newly discovered outages


# NEW POWER OUTAGES ---------------------------------------------------------------------
# Save new outage data to the database 

err = AppModelDB.SaveNewOutages(newOutages, currentTime)

if err != None:
    # TODO:There was a problem saving the new outages to the database. Handle this error                                          <----
    print("NEW OUTAGE DATA NOT SAVED TO DATABASE!")
    print(err)

# Generate the messages we will send to users for the new outages
ListOfOutageMessages = AppModelOutageMessages.GenerateNewOutageMessages(newOutages)


# TODO: Calculate which properties are inside which new outages and add a record to the DB
# TODO: Find a more efficient way to retrieve a more concise list of relevant properties from the DB
allProperties = AppModelDB.GetProperties()






# EXISTING POWER OUTAGES ----------------------------------------------------------------
# Compare old outages to find changes                                  

updateOutages = [] # Stores tuples of outages (Outage ID #, Outage Info in DB, Updated Outage Info)

dbOutagesCopy = dbOutages.copy() # For the binary search, work with a copy of the dbOutages list

for existingOutage in existingOutages:

    # Use a binary search to compare outage information from BC Hydro with outage information from the DB
    target = existingOutage['id']   # Target outage ID number
    left = 0                        # Left binary search index number
    right = len(dbOutagesCopy)-1    # Right binary search index number

    while left <= right:                                    # While the left and right numbers still provide a search option
        attempt = (left + right) // 2                       # Next index to attempt
        if target == dbOutagesCopy[attempt]['id']:          # If the ID number matches
            dbOutageInfo = dbOutagesCopy[attempt]['json']    
            updatedOutageInfo = existingOutage[attempt]['json']
            for key in updatedOutageInfo:                           # Search for values that are not the same between the two instances of the outage
                if updatedOutageInfo[key] != dbOutageInfo[key]:
                    updateOutages.append((target, dbOutageInfo.copy(),updatedOutageInfo.copy())) # If a difference is found, add it to the update list
                    dbOutagesCopy.pop(attempt)                      # Remove this outage from the dbOutage list so that we don't have to search over it again.
                    break
            break
            
        elif target < dbOutagesCopy[attempt]['id']:         # If the target ID number is less than the attempt index ID number
            right = attempt - 1                             # Move the right index marker for the binary search

        else:                                               # If the target ID number is greater than the attempt index ID number
            left = attempt + 1                              # Move the left index marker for the binary search
            
    
    if left > right:
        # TODO: Program a routine to deal with cases where two outage records did not coincide                                    <----
        print('ERROR: AN EXISTING OUTAGE WAS NOT FOUND IN THE DB RECORDS!')



# Update database with new outage information
dbUpdates = [outage for (_,_,outage) in updateOutages] #python list comprehension

err = AppModelDB.UpdateOutage(dbUpdates, currentTime)

if err != None:
    # TODO:There was a problem saving the updated outages to the database. Handle this error                                      <----
    print("UPDATED OUTAGE DATA NOT SAVED TO DATABASE!")
    print(err)


# Generate the messages we will send to users for the outage updates and add that to the list of outage messages.
ListOfOutageMessages += AppModelOutageMessages.GenerateOutageUpdateMessages(updateOutages)








# COMPLETE:
# Retrieve the json file from BC Hydro
# Handle the retrieved JSON file
# Sort outages into new and existing categories
# NEW POWER OUTAGES - save json to DB & generate user messages for each outage
# EXISTING POWER OUTAGES - save json to DB & generate user messages for each outage


# TODO: Calculate which properties are inside which new outages and add a PropertyOutage record to the DB (use AppModelPolygonPointFunctions.py && use InsertPropertyOutages in AppModelDB.py)
# TODO: Deactivate property-outages in the DB where power has been restored (use UpdatePropertyOutages in AppModelDB.py)
# TODO: Reactivate property-outages in the DB where power has been lost again (use UpdatePropertyOutages in AppModelDB.py)
# TODO: Retrieve user contact settings & property info from DB for updated/new outages (add DB functionality to AppModelDB.py)
# TODO: Send outage messages in ListOfOutageMessages to the users (use functions in AppModelSESMail.py to send emails)
# TODO: Rename AppModelRetrieveJSON.py to AppController.py and move non-controller functions (json functions,etc) to their own Model files.



# OLD CODE ------------------------------------------------------------------------------

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






